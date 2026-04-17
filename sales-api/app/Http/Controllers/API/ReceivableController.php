<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Receivable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PointsOfSale;

class ReceivableController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    // public function index(Request $request)
    // {   
    //     // $search = $request->input('search');
    //     // $pageSize = $request->input('pageSize', 10); // valor por defecto

    //     // $query = Pay::query();

    //     // if ($search) {
    //     //     $query->where('num_comprobante_abono', 'like', "%{$search}%");
    //     // }

    //     // $pays = $query->orderBy('id', 'desc')->paginate($pageSize);

    //     // return response()->json([
    //     //     'code'  => 200,
    //     //     'total' => $pays->total(),
    //     //     'page'  => $pays->currentPage(),
    //     //     'pays'  => $pays->items(),
    //     //     'pagination' => [
    //     //         'current_page' => $pays->currentPage(),
    //     //         'last_page' => $pays->lastPage(),
    //     //         'per_page' => $pays->perPage(),
    //     //     ],
    //     // ]);
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user        = auth()->user();
        $pointOfSale = $user->pointsOfSale()->with('branch')->first();
     
        if (!$pointOfSale) {
            return response()->json(['error' => 'El usuario no tiene puntos de venta asignados'], 403);
        }
     
        $id_branch = $pointOfSale->id_branch;
        if (!$id_branch) {
            return response()->json(['error' => 'Usuario no tiene sucursal asignada'], 403);
        }
     
        $validator = Validator::make($request->all(), [
            'id_sale'     =>    'required|exists:sales,id',
            'valor_abono' =>    'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

    
        $ultimoSecuencial    = $pointOfSale->secuencial_actual_receivable;
        $nuevoSecuencial     = $ultimoSecuencial ? $ultimoSecuencial + 1 : 1;
        $numeroConCeros      = str_pad($nuevoSecuencial, 9, '0', STR_PAD_LEFT);
        $num_establecimiento = $pointOfSale?->branch?->num_establecimiento;
     

        $num_receivable = $num_establecimiento.'-'.$pointOfSale->codigo_punto_emision.'-'.$numeroConCeros;
       

        $receivable = Receivable::create([
            'id_sale'               => $request->id_sale,
            'secuencial'            => $nuevoSecuencial,
            'num_comprobante_abono' => $num_receivable,
            'valor_abono'           => $request->valor_abono,
            'observacion'           => $request->observacion
        ]);

        $this->updateSecuencialReceivablePointSale($id_branch, $pointOfSale->codigo_punto_emision);
            
        return response()->json([
            'code' => 201,
            'message' => 'Receivable created successfully',
            'receivable' => $receivable
        ], 201);

    }

    public function updateSecuencialReceivablePointSale($id_branch, 
                                              $codigo_punto_emision)
    {
        $pointOfSale = PointsOfSale::where('id_branch', $id_branch)
                                    ->where('codigo_punto_emision', $codigo_punto_emision)
                                    ->first();

        $secuencial_actual_receivable = $pointOfSale->secuencial_actual_receivable;

        $pointOfSale->update(['secuencial_actual_receivable' => $secuencial_actual_receivable +1]);
        return $pointOfSale->fresh();
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $receivable = Receivable::find($id);

        if (!$receivable) {
            return response()->json([
                'code' => 404,
                'message' => 'Receivable not found'
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'receivable' => $receivable
        ]);
    }


    // public function sale()
    // {                                        
    //     return $this->belongsTo(Sale::class, 'id_sale');
    // }

    public function pdf($id)
    {
        $user = auth()->user(); // Obtiene el usuario autenticado

        // Limpiar buffers
        if (ob_get_length()) ob_clean();
        
        $receivable = Receivable::with(['sale'])->findOrFail($id);
        
        $valor_abono_total = Receivable::where('id_sale', $receivable->id_sale)
                                ->sum('valor_abono');
                                        
        $pdf = Pdf::loadView('receivable.pdf', compact('receivable','valor_abono_total','user'))
                                ->setPaper('a4');

        $filename = $receivable->num_comprobante_abono . '.pdf';

        $path = storage_path("app/receivable/pdfs/{$filename}");

        // Crear directorio
        $directory = dirname($path);
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Guardar el PDF
        $pdf->save($path);

        // Descargar usando response()->download()
        return response()->download($path, $filename, [
            'Content-Type' => 'application/pdf',
        ]); 
    }


    public function rePrintPdf($id)
    {
        $user = auth()->user(); // Usuario autenticado

        // Limpiar buffers (evita errores de encabezados)
        if (ob_get_length()) ob_clean();

        // Obtener datos del recibo
        $receivable = Receivable::with(['sale'])->findOrFail($id);

        $valor_abono_total = Receivable::where('id_sale', $receivable->id_sale)
            ->sum('valor_abono');

        // Generar el PDF (sin guardar)
        $pdf = Pdf::loadView('receivable.rePrintPdf', [
            'receivable' => $receivable,
            'valor_abono_total' => $valor_abono_total,
            'user' => $user
        ])->setPaper('a4');

        // Mostrar en el navegador (abre el PDF directamente)
        return $pdf->stream($receivable->num_comprobante_abono . '.pdf');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $receivable = Receivable::find($id);

            if (!$receivable) {
                return response()->json([
                    'code' => 404,
                    'message' => 'El pago no existe.'
                ], 404);
            }

            // Si el pago tiene una imagen asociada, eliminarla del storage
            if (!empty($receivable->imagen)) {
                // Verificar si la ruta es válida dentro del storage
                if (Storage::exists($receivable->imagen)) {
                    Storage::delete($receivable->imagen);
                } else {
                    // Si la imagen está en /storage/app/public/receivables
                    $publicPath = str_replace('/storage', 'public', 'receivables', $receivable->imagen);
                    if (Storage::exists($publicPath)) {
                        Storage::delete($publicPath);
                    }
                }
            }

            // Eliminar el registro de la base de datos
            $receivable->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Cobro eliminado correctamente.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error al eliminar el cobro.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
