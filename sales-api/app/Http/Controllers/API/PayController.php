<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pay;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PayController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {   
        // $search = $request->input('search');
        // $pageSize = $request->input('pageSize', 10); // valor por defecto

        // $query = Pay::query();

        // if ($search) {
        //     $query->where('num_comprobante_abono', 'like', "%{$search}%");
        // }

        // $pays = $query->orderBy('id', 'desc')->paginate($pageSize);

        // return response()->json([
        //     'code'  => 200,
        //     'total' => $pays->total(),
        //     'page'  => $pays->currentPage(),
        //     'pays'  => $pays->items(),
        //     'pagination' => [
        //         'current_page' => $pays->currentPage(),
        //         'last_page' => $pays->lastPage(),
        //         'per_page' => $pays->perPage(),
        //     ],
        // ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_buy'                => 'required|exists:buys,id',
            'num_comprobante_abono' => 'required|unique:pays,num_comprobante_abono',
            'valor_abono'           => 'required|numeric|min:0.01',
            'pay_img'               => 'nullable|image|max:2048' // opcional, máximo 2 MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        // Subir imagen si existe
        if ($request->hasFile('pay_img')) {
            $path = $request->file('pay_img')->store('pays', 'public');
            $request->merge(['imagen' => $path]);
        }

        $pay = Pay::create($request->only([
            'id_buy',
            'num_comprobante_abono',
            'valor_abono',
            'imagen'
        ]));

        return response()->json([
            'code' => 201,
            'message' => 'Pay created successfully',
            'pay' => $pay
        ], 201);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $pay = Pay::find($id);

        if (!$pay) {
            return response()->json([
                'code' => 404,
                'message' => 'Pay not found'
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'pay' => $pay
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $pay = Pay::find($id);

            if (!$pay) {
                return response()->json([
                    'code' => 404,
                    'message' => 'El pago no existe.'
                ], 404);
            }

            // Si el pago tiene una imagen asociada, eliminarla del storage
            if (!empty($pay->imagen)) {
                // Verificar si la ruta es válida dentro del storage
                if (Storage::exists($pay->imagen)) {
                    Storage::delete($pay->imagen);
                } else {
                    // Si la imagen está en /storage/app/public/pays
                    $publicPath = str_replace('/storage', 'public', 'pays', $pay->imagen);
                    if (Storage::exists($publicPath)) {
                        Storage::delete($publicPath);
                    }
                }
            }

            // Eliminar el registro de la base de datos
            $pay->delete();

            return response()->json([
                'code' => 200,
                'message' => 'Pago eliminado correctamente.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Error al eliminar el pago.',
                'error' => $e->getMessage()
            ], 500);
        }

    }
}
