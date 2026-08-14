<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\Branch;
use App\Models\SaleDetail;
use App\Models\Configuration;
use App\Models\PointsOfSale;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Http\Resources\SaleResource;
use App\Http\Resources\SaleCollection;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\CustomerCollection;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductCollection;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Inventory;
use App\Services\SriFacturaService;
use DOMDocument;
use Illuminate\Support\Facades\DB;
use Log;
use App\Mail\FacturaCustomerPdfXmlMail;
use Illuminate\Support\Facades\Mail;
use App\Jobs\ConsultarAutorizacionSriJob;
use Picqer\Barcode\BarcodeGeneratorPNG;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\VentasExport;

class SaleController extends Controller
{
    protected $sri;
    protected $iva;
    protected $ruc;
    protected $razonSocial;
    protected $nombreComercial;
    protected $dirMatriz;
    protected $branch;

    public function __construct(SriFacturaService $sri)
    {
        $this->sri  =   $sri;
        $this->getConfig();
    }

    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        $pointOfSale = $user->pointsOfSale()->first();
        $query = Sale::with('customer')
            ->where('id_point_of_sale', $pointOfSale->id);

        if ($request->fecha_ini && $request->fecha_fin) {

            $inicio = Carbon::parse($request->fecha_ini)->startOfDay();
            $fin = Carbon::parse($request->fecha_fin)->endOfDay();

            $query->whereBetween('created_at', [$inicio,$fin]);
        }

        $sales = $query
            ->orderBy('created_at','desc')
            ->get();

        $pdf = Pdf::loadView('exportPdf.sales', compact('sales'));

        $pdf->setPaper('A4','landscape');

        return $pdf->download('ventas.pdf');
    }

    public function excel(Request $request)
    {
        $user = auth()->user();

        // Obtener primer punto de venta del usuario
        $pointOfSale = $user->pointsOfSale()->first();
        if (!$pointOfSale) {
            return response()->json([
                'error' => 'El usuario no tiene puntos de venta asignados'
            ], 403);
        }

        $id_branch = $pointOfSale->id_branch;
        if (!$id_branch) {
            return response()->json([
                'error' => 'No se pudo determinar la sucursal del usuario'
            ], 403);
        }
    
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;

        // Consulta base
        $query = Sale::with('customer')
                        ->where('id_point_of_sale', $pointOfSale->id);

        if ($fecha_ini && $fecha_fin) {
            try {
                $inicio = \Carbon\Carbon::parse($fecha_ini)->startOfDay();
                $fin    = \Carbon\Carbon::parse($fecha_fin)->endOfDay();
                $query->whereBetween('created_at', [$inicio, $fin]);
            } catch (\Exception $e) {
                \Log::error('Error al parsear fechas: ' . $e->getMessage());
            }
        }

        $query->select([
                'id_customer',
                'total',
                'subtotal',
                'discount',
                'numero_factura',
                'iva',
                'iva0',
                'clave_acceso',
                'estado_sri',
                'numero_autorizacion',
                'fecha_autorizacion_sri',
                'error_no_autorizada',
                'establecimiento',
                'punto_emision',
                'secuencial',
                'ambiente',
                'created_at',
                'form_pay',
                'plazo',
                'unidadTiempo',
                'ice'
            ])->orderBy('created_at', 'desc');

            return Excel::download(
                new VentasExport($query),
                'ventas.xlsx'
            );
    }

    public function getConfig()
    {
        $this->iva          =   Configuration::where('name',    'iva')->first();
        $this->ruc          =   Configuration::where('name',    'ruc')->first();
        $this->razonSocial  =   Configuration::where('name',    'razonSocial')->first();
        $this->nombreComercial = Configuration::where('name',   'nombreComercial')->first();
        $this->dirMatriz    =   Configuration::where('name',    'dirMatriz')->first();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {     
        $user = auth()->user();

        // Obtener primer punto de venta del usuario
        $pointOfSale = $user->pointsOfSale()->first();

        if (!$pointOfSale) {
            return response()->json([
                'error' => 'El usuario no tiene puntos de venta asignados'
            ], 403);
        }

        $id_branch = $pointOfSale->id_branch;
        // error_log($id_branch);
        
        if (!$id_branch) {
            return response()->json([
                'error' => 'No se pudo determinar la sucursal del usuario'
            ], 403);
        }
    
        $search    = $request->search;
        $pageSize  = $request->pageSize ?? 10;
        $fecha_ini = $request->fecha_ini;
        $fecha_fin = $request->fecha_fin;
        $form_pay  = $request->form_pay;

        // Consulta base
        $query = Sale::where('id_point_of_sale', $pointOfSale->id)->take(1000);

        if ($search) {
            $query->FilterSale($search);
        }

        if ($fecha_ini && $fecha_fin) {
            try {
                $inicio = \Carbon\Carbon::parse($fecha_ini)->startOfDay();
                $fin    = \Carbon\Carbon::parse($fecha_fin)->endOfDay();
                $query->whereBetween('created_at', [$inicio, $fin]);
            } catch (\Exception $e) {
                \Log::error('Error al parsear fechas: ' . $e->getMessage());
            }
        }

        if($form_pay)
        {
            $query->where('form_pay', (string)$form_pay);
        }   

        $total_autorizado       = (clone $query)->where('estado_sri', 'AUTORIZADO')
                                                ->sum('total');
        $total_autor_no_autor   = (clone $query)->where('estado_sri', 'NO AUTORIZADO')
                                    ->sum('total');
        // Orden y paginación
        $sales = $query->orderBy('created_at', 'desc')
                      ->paginate($pageSize);

        return response()->json([
            'code'      =>      200,
            'total'     =>      $sales->total(),
            'total_autorizado'      => $total_autorizado,
            'total_autor_no_autor'  => $total_autor_no_autor,
            'Sales'     =>      SaleCollection::make($sales),
        ]);
    }

    public function getCustomers(Request $request)
    {
        $searchCustomer   = $request->searchCustomer;
        if($searchCustomer){
            $customers = Customer::where('name', 'like', '%'.$searchCustomer.'%')
                            ->orWhere('num_identificador', 'like', '%'.$searchCustomer.'%')
                            ->orderBy('name', 'asc')
                            ->take(10)
                            ->get();   
        }else{
            $customers = Customer::orderBy('name', 'asc')
                                ->take(10)
                                ->get();         
        }

        return response()->json(
            [   'code'      => 200,
                'customers' => CustomerCollection::make($customers)
            ]);
    }

    public function getProducts(Request $request)
    {
        $user = auth()->user();

        // Obtener sucursal del usuario
        $pointOfSale = $user->pointsOfSale()->first();

        if (!$pointOfSale) {
            return response()->json([
                'error' => 'El usuario no tiene puntos de venta asignados'
            ], 403);
        }

        $id_branch = $pointOfSale->id_branch;

        if (!$id_branch) {
            return response()->json([
                'error' => 'Usuario no tiene sucursal asignada'
            ], 403);
        }

        $query = Product::select('products.*',
                            //COALESCE  ->  si es null return 0  
                    \DB::raw('COALESCE(inventories.stock, 0) as stock'),
                    \DB::raw('COALESCE(inventories.stock_min, 0) as stock_min')
                )->leftJoin('inventories', function($join) use ($id_branch) {
                    $join->on('products.id', '=', 'inventories.id_product')
                        ->where('inventories.id_branch', $id_branch);
                });

        // Filtros
        $search = $request->searchProduct;
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('products.name', 'LIKE', "%{$search}%")
                  ->orWhere('products.cod_pro_barras', 'LIKE', "%{$search}%")
                  ->orWhere('products.cod_pro', 'LIKE', "%{$search}%");
            });
        }

        $products = $query->orderBy('products.name')
                            ->take(10)
                            ->get();

        return response()->json([
            'code' => 200,
            'Products' => ProductCollection::make($products)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $ambiente = Configuration::where('name','ambiente')->value('value');

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
            'id_customer'            => 'required|exists:customers,id',
            'items'                  => 'required|array|min:1',
            'items.*.id_product'     => 'required|exists:products,id',
            'items.*.quantity'       => 'required|integer|min:1',
        ]);
     
        if ($validator->fails()) {
            return response()->json(['code' => 403, 'message' => $validator->errors()]);
        }
     
        $ultimoSecuencial    = $pointOfSale->secuencial_actual;
        $nuevoSecuencial     = $ultimoSecuencial ? $ultimoSecuencial + 1 : 1;
        $numeroConCeros      = str_pad($nuevoSecuencial, 9, '0', STR_PAD_LEFT);
        $num_establecimiento = $pointOfSale?->branch?->num_establecimiento;
     
        try {
            DB::beginTransaction();
     
            $sale = Sale::create([
                'id_customer'      => $request->id_customer,
                'id_point_of_sale' => $pointOfSale->id,
                'establecimiento'  => $num_establecimiento,
                'punto_emision'    => $pointOfSale->codigo_punto_emision,
                'secuencial'       => $numeroConCeros,
                'numero_factura'   => $num_establecimiento . '-' . $pointOfSale->codigo_punto_emision . '-' . $numeroConCeros,
                'iva'              => $request->iva,
                'iva0'             => $request->iva0,
                'ice'              => $request->ice,
                'subtotal'         => $request->subtotal,
                ////////////////////////////////////////////////////////////////////////////
                                    // SIN UTILIZACION DEL SISTEMA FINANCIERO     01 contado  
                                    // COMPENSACIÓN DE DEUDAS                     15 credito
                                    // TARJETA DE DÉBITO                          16 contado
                                    // DINERO ELECTRÓNICO                         17 contado
                'form_pay'         => $request->form_pay,                    
                                    // TARJETA PREPAGO                            18 contado
                                    // TARJETA DE CRÉDITO                         19 contado
                                    // OTROS CON UTILIZACIÓN DEL SISTEMA FINANCIERO  20  credito
                                    // ENDOSO DE TÍTULOS                          21  credito
                ////////////////////////////////////////////////////////////////////////////
                'plazo'            => $request->plazo,
                'unidadTiempo'     => $request->unidadTiempo,
                'total'            => $request->total,
                'discount'         => $request->discount,
                'estado_sri'       => 'PENDIENTE',
                'ambiente'         => $ambiente, 
                'clave_acceso'     => $this->sri->generarClaveAcceso(
                    date('d-m-Y'),
                    '01',
                    $this->ruc['value'],
                    $ambiente,
                    $num_establecimiento,
                    $pointOfSale->codigo_punto_emision,
                    $numeroConCeros,
                    '12345678',
                    '1'
                ),
            ]);

            foreach ($request->items as $item) {
                SaleDetail::create([
                    'id_sale'    => $sale->id,
                    'id_product' => $item['id_product'],
                    'cod_pro'    => $item['cod_pro'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'subtotal'   => $item['subtotal'],
                    'discount'   => $item['discount'],
                    'iva'        => $item['iva'],
                    'ice'        => $item['ice'],
                    'impuesto'   => json_encode($item['impuesto']),
                    'impuesto_ice' => isset($item['impuesto_ice'])
                        ? json_encode($item['impuesto_ice'])
                        : null,
                ]);
     
                // $this->updateStockSale($item['id_product'], $id_branch, $item['quantity']);

            }


            $resp = $this->generarFirmarEnviar($sale);

            $this->updateSecuencialPointSale($id_branch, $pointOfSale->codigo_punto_emision);
            
            if (in_array($resp['code'], [500, 422, 400])) {
                DB::rollBack();
                return response()->json(['resp' => $resp]);
            }
     
            DB::commit();

            $sale->refresh();

            return response()->json(['sale' => $sale, 
                                     'resp' => $resp
                                   ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating sale: ' . $e->getMessage());
            return response()->json([
                'code'    => 500,
                'status'  => 'error',
                'message' => 'Error en el proceso',
                'errors'  => $e->getMessage(),
            ]);
        }
    }


    public function generarFirmarEnviar($sale): array
    {
        if (!$sale || !$sale->customer) {
            return [
                'code' => 422,
                'status' => 'error',
                'message' => 'Datos de venta o cliente incompletos'
            ];
        }

        try {

            $subtotalMenosDescuento = $sale['subtotal'] - $sale['discount'];
            $fechaEmision = \Carbon\Carbon::parse($sale['created_at'])->format('d/m/Y');
            $ambienteCode = $sale->ambiente;
            $ambiente     = $ambienteCode === '1' ? 'pruebas' : 'produccion';

            $branch = Branch::where('num_establecimiento', $sale->establecimiento)->first();
            $dirEstablecimiento = $branch->address ?? 'N/A';

            $obligadoContabilidad = Configuration::where('name','obligadoContabilidad')->value('value');

            $claveAcceso   = $sale['clave_acceso'];
            $nombreArchivo = $claveAcceso . '.xml';

            // ✅ carpetas seguras
            $genPath = storage_path('app/facturas/generados');
            $firPath = storage_path('app/facturas/firmados');

            if (!is_dir($genPath)) mkdir($genPath, 0755, true);
            if (!is_dir($firPath)) mkdir($firPath, 0755, true);



            $itemsImpuestos = [];
            $detalles = $sale->details->map(function ($detail) use (&$itemsImpuestos) {

                $impuesto = json_decode($detail->impuesto, true);
                if (!$impuesto) {
                    throw new \Exception('Item sin impuesto');
                }

                $base_ice = 0;
                $codigoPorcentaje_ice = null;
                $tarifa_ice = 0;
                $valor_ice = 0;

                $impuesto_ice = json_decode($detail->impuesto_ice, true);
                if ($impuesto_ice) {
                    $base_ice       = $impuesto_ice['baseImponible'];           //45
                    $codigoPorcentaje_ice = $impuesto_ice['codigoPorcentaje'];  //4 -> 15%
                    $tarifa_ice     = (float) $impuesto_ice['tarifa'];          //12.00
                    $valor_ice = round((float)$impuesto_ice['valor'], 2);
                }

                $codigoPorcentaje = $impuesto['codigoPorcentaje'];
                $tarifa           = (float)$impuesto['tarifa'];
                $valor            = round((float)$impuesto['valor'], 2);

                $baseIva = (float)$impuesto['baseImponible'];


                // 🔥 alimentar cálculo global
                $ivaCod = '';
                if($codigoPorcentaje == '0'){
                    $ivaCod = 'iva0';
                }
                if($codigoPorcentaje == '2'){
                    $ivaCod = 'iva12';
                }
                if($codigoPorcentaje == '3'){
                    $ivaCod = 'iva14';
                }
                if($codigoPorcentaje == '4'){
                    $ivaCod = 'iva15';
                }


                $itemsImpuestos[] = [
                    'base' => $baseIva,
                    'tipo' => $ivaCod,
                    'valor' => $valor,

                    'base_ice' => $base_ice,
                    'tipo_ice' => $codigoPorcentaje_ice,
                    'valor_ice' => $valor_ice,
                ];

                $impuestos = [[
                        'codigo'           => '2',
                        'codigoPorcentaje' => $codigoPorcentaje,
                        'tarifa'           => number_format($tarifa, 2, '.', ''),
                        'baseImponible'    => number_format($baseIva, 2, '.', ''),
                        'valor'            => number_format($valor, 2, '.', ''),
                ]];

                if ($impuesto_ice) {

                    $impuestos[] = [
                        'codigo' => '3',
                        'codigoPorcentaje' => $codigoPorcentaje_ice,
                        'tarifa' => number_format($tarifa_ice, 2, '.', ''),
                        'baseImponible' => number_format($base_ice, 2, '.', ''),
                        'valor' => number_format($valor_ice, 2, '.', ''),
                    ];
                }
                
                $precioTotalSinImpuesto = ($detail->quantity * $detail->price) - $detail->discount;

               
                return [
                    'codigoPrincipal'        => $detail->product->cod_pro ?? 'cod_pro',
                    'descripcion'            => optional($detail->product)->name ?? 'Producto',
                    'cantidad'               => number_format($detail->quantity, 2, '.', ''),
                    'precioUnitario'         => number_format($detail->price, 2, '.', ''),
                    'descuento'              => number_format($detail->discount, 2, '.', ''),
                    'precioTotalSinImpuesto' => number_format($precioTotalSinImpuesto, 2, '.', ''),
                    'impuestos' => $impuestos,
                ];
            })->values()->toArray();

            $pagos = [[
                'pago' => [
                    'formaPago'     => $sale['form_pay'],
                    'total'         => number_format($sale['total'], 2, '.', ''),
                    'plazo'         => $sale['plazo'] ?? null,
                    'unidadTiempo'  => $sale['unidadTiempo'] ?? null,
                ],
            ]];        

            // ================= DATA =================
            $data = [
                'version' => '2.1.0',
                'id'      => 'comprobante',
                'infoTributaria' => [
                    'ambiente'           => $ambienteCode,
                    'tipoEmision'        => '1',
                    'razonSocial'        => $this->razonSocial['value']     ?? '',
                    'nombreComercial'    => $this->nombreComercial['value'] ?? '',
                    'ruc'                => $this->ruc['value'] ?? '',
                    'claveAcceso'        => $claveAcceso,
                    'codDoc'             => '01',
                    'estab'              => $sale['establecimiento'],
                    'ptoEmi'             => $sale['punto_emision'],
                    'secuencial'         => $sale['secuencial'],
                    'dirMatriz'          => $this->dirMatriz['value'] ?? '',
                ],
                'infoFactura' => [
                    'fechaEmision'                => $fechaEmision,
                    'dirEstablecimiento'          => $dirEstablecimiento,
                    'obligadoContabilidad'        => $obligadoContabilidad,
                    'tipoIdentificacionComprador' => $this->obtenerTipoIdentificacion($sale->customer['num_identificador']),
                    'razonSocialComprador'        => $sale->customer['name'],
                    'identificacionComprador'     => $sale->customer['num_identificador'],
                    'totalSinImpuestos'           => number_format($sale['subtotal'] - $sale['discount'], 2, '.', ''),
                    'totalDescuento'              => number_format($sale['discount'], 2, '.', ''),
                    'totalConImpuestos' => $this->calcularImpuestosSri($itemsImpuestos),
                    'propina'      => '0.00',
                    'importeTotal' => number_format($sale['total'], 2, '.', ''),
                    'moneda'       => 'DOLAR',
                    'pagos' => $pagos,
                ],
                'detalles' => $detalles,

                'infoAdicional' => [
                    ['nombre' => 'Email',    'valor' => $sale->customer->email ?? 'cliente@correo.com'],
                    ['nombre' => 'Telefono', 'valor' => $sale->customer->phone ?? '0999999999'],
                ],
            ];

            // ================= XML =================
            $xml = $this->sri->generarXml($data);
            if (!$xml) throw new \Exception('Error al generar XML');

            file_put_contents("$genPath/$nombreArchivo", $xml);

            // ================= FIRMA =================
            $xmlFirmado = $this->sri->firmarXml(
                $xml,
                storage_path('app/certificados/firma_juel/uanataca_aes.p12'),
                env('CERTIFICADO_CLAVE')
            );

            if (!$xmlFirmado) throw new \Exception('Error al firmar XML');

            file_put_contents("$firPath/$nombreArchivo", $xmlFirmado);

            // ================= XSD =================
            $xsd = $this->sri->validarContraXsd($xmlFirmado);
            if (!$xsd['valid']) {
                return [
                    'code' => 422,
                    'status' => 'error',
                    'message' => 'XML inválido',
                    'errors' => $xsd['errors']
                ];
            }

            // ================= ENVÍO =================
            $recepcion = $this->sri->enviarComprobanteSri($xmlFirmado, $ambiente);
            \Log::info('SRI recepción', $recepcion);
            if (!isset($recepcion['estado'])) {
                throw new \Exception('Respuesta inválida del SRI');
            }

            // 🔥 DEVUELTA
            if ($recepcion['estado'] === 'DEVUELTA') {
                $mensajes = $recepcion['mensajes'] ?? [];
                \Log::error('DEVUELTA SRI', $mensajes);

                $es70 = collect($mensajes)
                    ->contains(fn($m) => ($m['identificador'] ?? '') === '70');

                if (!$es70) {
                    return [
                        'code' => 400,
                        'status' => 'error',
                        'message' => 'DEVUELTA por el SRI',
                        'errores' => $mensajes
                    ];
                }
            }

            if (!in_array($recepcion['estado'], ['RECIBIDA', 'DEVUELTA'])) {
                return [
                    'code' => 500,
                    'status' => 'error',
                    'message' => 'Estado inesperado: ' . $recepcion['estado']
                ];
            }

            // ================= JOB =================
            $ahora = now()->timestamp;
            $sale->update(['estado_sri' => 'PENDIENTE']);

            \App\Jobs\ConsultarAutorizacionSriJob::dispatch(
                $claveAcceso,
                $ambiente,
                $sale->id,
                $ahora,
                0,
                $ahora
            )->delay(now()->addSeconds(10));

            return [
                'code' => 202,
                'status' => 'processing',
                'message' => 'Enviado al SRI',
                'clave_acceso' => $claveAcceso,
            ];

        } catch (\Throwable $e) {
            \Log::error('Error facturación', [
                'error' => $e->getMessage()
            ]);
            $sale->update(['estado_sri' => 'ERROR']);
            return [
                'code' => 500,
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
    

    private function calcularImpuestosSri(array $items): array
    {
        $totales = [];

        foreach ($items as $item) {

            // ================= IVA =================
            $base  = round((float)($item['base'] ?? 0), 2);
            $valor = round((float)($item['valor'] ?? 0), 2);
            $tipo  = $item['tipo'] ?? null;

            switch ($tipo) {
                case 'iva0':
                    $codigo = '2';
                    $codigoPorcentaje = '0';
                    break;

                case 'iva12':
                    $codigo = '2';
                    $codigoPorcentaje = '2';
                    break;

                case 'iva14':
                    $codigo = '2';
                    $codigoPorcentaje = '3';
                    break;

                case 'iva15':
                    $codigo = '2';
                    $codigoPorcentaje = '4';
                    break;

                default:
                    $codigo = null;
                    $codigoPorcentaje = null;
            }

            if ($codigo) {

                $key = $codigo . '-' . $codigoPorcentaje;

                if (!isset($totales[$key])) {
                    $totales[$key] = [
                        'codigo'           => $codigo,
                        'codigoPorcentaje' => $codigoPorcentaje,
                        'baseImponible'    => 0,
                        'valor'            => 0,
                    ];
                }

                $totales[$key]['baseImponible'] += $base;
                $totales[$key]['valor'] += $valor;
            }

            // ================= ICE =================

            $tipo_ice  = $item['tipo_ice'] ?? null;
            $base_ice  = round((float)($item['base_ice'] ?? 0), 2);
            $valor_ice = round((float)($item['valor_ice'] ?? 0), 2);

            if (!empty($tipo_ice) && $valor_ice > 0) {

                $keyIce = '3-' . $tipo_ice;

                if (!isset($totales[$keyIce])) {
                    $totales[$keyIce] = [
                        'codigo'           => '3',
                        'codigoPorcentaje' => $tipo_ice,
                        'baseImponible'    => 0,
                        'valor'            => 0,
                    ];
                }

                $totales[$keyIce]['baseImponible'] += $base_ice;
                $totales[$keyIce]['valor'] += $valor_ice;
            }
        }

        return array_values(array_map(function ($t) {

            return [
                'codigo'           => $t['codigo'],
                'codigoPorcentaje' => $t['codigoPorcentaje'],
                'baseImponible'    => number_format($t['baseImponible'], 2, '.', ''),
                'valor'            => number_format($t['valor'], 2, '.', ''),
            ];

        }, $totales));
    }



    /**
     * Guarda un archivo asegurándose de que el directorio existe
     */
    private function guardarArchivo($ruta, $contenido)
    {
        $directorio = dirname($ruta);
        if (!file_exists($directorio)) {
            mkdir($directorio, 0775, true);
        }
        return file_put_contents($ruta, $contenido);
    }

    /**
     * Determina el tipo de identificación basado en el número
     */
    private function obtenerTipoIdentificacion($identificacion)
    {
        $longitud = strlen($identificacion);
        
        if ($longitud === 10) return '05'; // Cédula
        if ($longitud === 13) return '04'; // RUC
        if ($identificacion === '9999999999999') return '07'; // Consumidor final
        
        return '06'; // Pasaporte por defecto
    }

    public function updateStockSale($id_product, $id_branch, $quantity)
    {
        $inventory = Inventory::where('id_product', $id_product)
                                ->where('id_branch', $id_branch)
                                ->first();
                
        $inventory->update(['stock' => $inventory->stock - $quantity]);
        return $inventory->fresh();
    }

    public function updateSecuencialPointSale($id_branch, 
                                              $codigo_punto_emision)
    {
        $pointOfSale = PointsOfSale::where('id_branch', $id_branch)
                                    ->where('codigo_punto_emision', $codigo_punto_emision)
                                    ->first();

        $secuencial_actual = $pointOfSale->secuencial_actual;

        $pointOfSale->update(['secuencial_actual' => $secuencial_actual +1]);
        return $pointOfSale->fresh();
    }




    public function barcodeGeneratorPng($clave_acceso)
    {
        $generator = new BarcodeGeneratorPNG();

        $barcode = base64_encode(
            $generator->getBarcode(
                $clave_acceso,
                $generator::TYPE_CODE_128
            )
        );

        return $barcode;        
    }
    
    public function pdf($id)
    {
        $sale = $this->generarPdf($id);
        $path = storage_path(
            'app/facturas/pdfs/' . $sale->clave_acceso . '.pdf'
        );
        return response()->download(
            $path,
            $sale->clave_acceso . '.pdf',
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }

    public function generarPdf($id)
    {
        if (ob_get_length()) {
            ob_clean();
        }
        $configs = Configuration::whereIn('name', [
            'version',
            'ambiente',
            'razonSocial',
            'nombreComercial',
            'ruc',
            'dirMatriz',
            'obligadoContabilidad',
            'iva',
            'logoPdf',
            'correo'
        ])->pluck('value', 'name');
        $sale = Sale::with(['customer', 'details'])->findOrFail($id);
        $barcode = $this->barcodeGeneratorPng($sale->clave_acceso);
        $pdf = Pdf::loadView('factura.pdf', [
            'sale' => $sale,
            'version' => $configs['version'] ?? '',
            'ambiente' => $configs['ambiente'] ?? '',
            'razonSocial' => $configs['razonSocial'] ?? '',
            'nombreComercial' => $configs['nombreComercial'] ?? '',
            'ruc' => $configs['ruc'] ?? '',
            'dirMatriz' => $configs['dirMatriz'] ?? '',
            'obligadoContabilidad' => $configs['obligadoContabilidad'] ?? '',
            'iva' => $configs['iva'] ?? '',
            'logoPdf' => $configs['logoPdf'] ?? '',
            'correo' => $configs['correo'] ?? '',
            'barcode' => $barcode,
        ])->setPaper('a4');

        $filename = $sale->clave_acceso . '.pdf';
        $path = storage_path("app/facturas/pdfs/{$filename}");
        $directory = dirname($path);

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        $pdf->save($path);
        // Devuelve la venta actualizada
        return Sale::with(['customer', 'details'])
            ->findOrFail($id);
    }

    public function rePrintFacturaPdf($clave)
    {
        $path = storage_path("app/facturas/pdfs/{$clave}.pdf");

        if (!file_exists($path)) {

            $sale = Sale::where('clave_acceso', $clave)->first();

            if (!$sale) {
                abort(404, 'Factura no encontrada');
            }

            // Regenera el PDF
            $sale = $this->generarPdf($sale->id);

            // Verifica que se haya generado
            if (!file_exists($path)) {
                abort(500, 'No fue posible generar el PDF');
            }
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $clave . '.pdf"',
        ]);
    }







    public function reconsultarSri($id, SriFacturaService $sri)
    {
        // $user        = auth()->user();
        // $pointOfSale = $user->pointsOfSale()->with('branch')->first();

        // if (!$pointOfSale) {
        //     return response()->json(['error' => 'El usuario no tiene puntos de venta asignados'], 403);
        // }

        // $id_branch = $pointOfSale->id_branch;

        // if (!$id_branch) {
        //     return response()->json(['error' => 'Usuario no tiene sucursal asignada'], 403);
        // }

        $sale = Sale::findOrFail($id);

        // 🔥 EVITA DOBLE DESCUENTO
        if ($sale->estado_sri === 'AUTORIZADO') {

            return response()->json([
                'estado'=>'AUTORIZADO'
            ]);

        }

        // $sale->load('details.product');

        // $respuesta = $sri->autorizarSri($sale->clave_acceso, $sale->ambiente);

        // if ($respuesta['estado'] === 'AUTORIZADO') {

        //     $fechaAutorizacionRaw = $respuesta['fechaAutorizacion'] ?? null;
        //     $fechaAutorizacion = $fechaAutorizacionRaw
        //         ? Carbon::parse($fechaAutorizacionRaw)->format('Y-m-d H:i:s')
        //         : now()->format('Y-m-d H:i:s');

        //     $sale->update([
        //         'estado_sri'             => 'AUTORIZADO',
        //         'numero_autorizacion'    => $respuesta['numeroAutorizacion'],
        //         'fecha_autorizacion_sri' => $fechaAutorizacion,
        //     ]);

        //     foreach ($sale->details as $detail) {

        //         $product = $detail->product;

        //         if (!$product) {
        //             continue;
        //         }

        //         $inventory = Inventory::where('id_product', $product->id)
        //             ->where('id_branch', $id_branch)
        //             ->first();

        //         if (!$inventory) {
        //             \Log::error('Inventario no encontrado', [
        //                 'product_id' => $product->id,
        //                 'branch_id' => $id_branch
        //             ]);
        //             continue;
        //         }

        //         // 🔥 forma correcta
        //         $inventory->decrement('stock', $detail->quantity);
        //     }
        // }

        // return response()->json($respuesta);
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {  
        $sale = Sale::with(['customer', 
                            'receivables', 
                            'details.product'
                          ])->find($id);

        if (!$sale) {
            return response()->json(['message' => 'No existe la venta solicitada.'], 404);
        }
        
        return response()->json(SaleResource::make($sale));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sale = Sale::where('id','=',$id)->first();
                
        if(!$sale){
            $data = ['code'=> 404,
                     'message' => 'Sale not found'];
        }else{
            if($sale->state == true){
                $sale->state = false;
                $message='Sale annulled';
            }
            $sale->update();
            $data = ['code'=> 200,
                     'message' => $message];
        }
        return response()->json([$data['code'], 
                                 $data['message']]);
    }

    
}
