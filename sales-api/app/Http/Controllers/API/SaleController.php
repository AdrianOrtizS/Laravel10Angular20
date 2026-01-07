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

// use App\Http\Controllers\API\Exception;

use App\Mail\FacturaCustomerPdfXmlMail;
use Illuminate\Support\Facades\Mail;

class SaleController extends Controller
{

    protected $sri;
    protected $iva;
    protected $ruc;
    protected $razonSocial;
    protected $nombreComercial;

    public function __construct(SriFacturaService $sri)
    {
        $this->sri  =   $sri;
        $this->getConfig();
    }

    public function getConfig()
    {
        $this->iva      =   Configuration::where('name',    'iva')->first();
        $this->ruc      =   Configuration::where('name',    'ruc')->first();
        $this->razonSocial = Configuration::where('name',   'razonSocial')->first();
        $this->nombreComercial = Configuration::where('name','nombreComercial')->first();
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
        $type_receivable   = $request->type_receivable;

        // Consulta base
        $query = Sale::where('id_point_of_sale', $pointOfSale->id);

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

        if($type_receivable)
        {
            $query->where('type_receivable', (string)$type_receivable);
        }   

        // Orden y paginación
        $sales = $query->orderBy('created_at', 'desc')
                      ->paginate($pageSize);

        return response()->json([
            'code'  => 200,
            'total' => $sales->total(),
            'Sales'  => SaleCollection::make($sales),
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
            [   'code'  => 200,
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
                    \DB::raw('COALESCE(inventories.stock_min, 0) as stock_min'))
                ->leftJoin('inventories', function($join) use ($id_branch) {
                    $join->on('products.id', '=', 'inventories.id_product')
                        ->where('inventories.id_branch', $id_branch);
                });

        // Filtros
        $search = $request->searchProduct;
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('products.name', 'LIKE', "%{$search}%")
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
        $user = auth()->user();
        
        $pointOfSale = $user->pointsOfSale()->with('branch')->first();
        $id_branch = $pointOfSale->id_branch;
                
        if(!$pointOfSale) {
            return response()->json([
                'error' => 'El usuario no tiene puntos de venta asignados'
            ], 403);
        }
        if (!$id_branch) {
            return response()->json([
                'error' => 'Usuario no tiene sucursal asignada'
            ], 403);
        }

        $ultimoSecuencial = $pointOfSale->secuencial_actual;

        $nuevoSecuencial = $ultimoSecuencial ? $ultimoSecuencial + 1 : 1;
        $numeroConCeros = str_pad($nuevoSecuencial, 9, '0', STR_PAD_LEFT);

        $num_establecimiento = $pointOfSale?->branch?->num_establecimiento;        

        $validator = Validator::make($request->all(), [
            'id_customer'   => 'required|exists:customers,id',
            'items'         => 'required|array|min:1',
            'items.*.id_product' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['code' => 403, 'message' => $validator->errors()]);
        }

        try{
            DB::beginTransaction();

            $codigoNumerico = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);

            // Crear el pedido
            $sale = Sale::create([
                'id_customer'   => $request->id_customer,
                'id_point_of_sale' =>   $pointOfSale->id,
                'type_receivable' => $request->type_receivable,
                'establecimiento' => $num_establecimiento,  // 1er 001
                'punto_emision'   => $pointOfSale->codigo_punto_emision, //2do 001
                'secuencial'      => $numeroConCeros,   // 0000000023
                'numero_factura'  => $num_establecimiento.'-'.$pointOfSale->codigo_punto_emision.'-'.$numeroConCeros, // 001-001-0000000023
                'iva'           => $request->iva,
                'subtotal'      => $request->subtotal,
                'total'         => $request->total,
                'discount'      => $request->discount,
                'clave_acceso'  => $this->sri->generarClaveAcceso( 
                                        date('d-m-Y'),                      // 8 digitos
                                        '01',                               // 01 Factura 
                                        $this->ruc['value'],                // $ruc emisor,
                                        '1',                                // ambiente 1=pruebas, 2=produccion
                                        $num_establecimiento,
                                        $pointOfSale->codigo_punto_emision, // '001'    $serie 001001
                                        $numeroConCeros,                    // 000000123 
                                        $codigoNumerico,                         // $codigoNumerico,
                                        '1'                                 // $tipoEmision    
                                    )
            ]);

            // Agregar detalles del pedido
            foreach ($request->items as $item) {
                $product = Product::find($item['id_product']);

                SaleDetail::create([
                    'id_sale'   => $sale->id,
                    'id_product'=> $item['id_product'],
                    'cod_pro'   => $item['cod_pro'],
                    'quantity'  => $item['quantity'],
                    'price'     => $item['price'],
                    'subtotal'  => $item['subtotal'],
                    'discount'  => $item['discount']
                ]);

                $this->updateStockSale( $item['id_product'], 
                                        $id_branch, 
                                        $item['quantity']);

                $this->updateSecuencialPointSale( 
                                        $id_branch, 
                                        $pointOfSale->codigo_punto_emision);
            
            }

            $resp = $this->generarFirmarEnviar($sale);
            
            if($resp['code'] === 500 || $resp['code'] === 405){
                DB::rollBack();
                return response()->json([ 'resp' => $resp ]);            
            }else{
                DB::commit();
                
                return response()->json([ 'sale' => $sale, 
                                          'resp' => $resp ]);
            }

        }catch (\Exception $e) {
            DB::rollBack();
            // Log::error('Error creating sale: ' . $e->getMessage());
            return [
                'code'  => 405,
                'status' => 'error',
                'message' => 'Error en el proceso',
                'errors' => $e->getMessage()
            ];
        }
    }

    
    public function generarFirmarEnviar($sale)
    {   
        // Validación básica de datos requeridos
        if (!$sale || !$sale->customer) {
                return [
                    'code' => 405,
                    'status' => 'error',
                    'message' => 'Datos de venta o cliente incompletos',
                    'errors' => 'Datos de venta o cliente incompletos'
                ];
            throw new \Exception('Datos de venta o cliente incompletos');
        }

        // Cálculos previos para evitar repetir operaciones
        $subtotalMenosDescuento = $sale['subtotal'] - $sale['discount'];
        $fechaEmision = \Carbon\Carbon::parse($sale['created_at'])->format('d/m/Y');

        $data = [
            'version'   => '2.1.0',         // versión vigente del XSD
            'id'        => 'comprobante',   // atributo requerido en el nodo <factura>
            'infoTributaria' => [
                'ambiente'         => '1',  // 1 = pruebas, 2 = producción
                'tipoEmision'      => '1',  // 1 = Normal (lo habitual) -- 2 = Indisponibilidad del sistema 
                'razonSocial'      => $this->razonSocial['value'] ?? '',
                'nombreComercial'  => $this->nombreComercial['value'] ?? '', // opcional pero recomendado
                'ruc'              => $this->ruc['value'] ?? '',
                'claveAcceso'      => $sale['clave_acceso'], // 49 dígitos generados
                'codDoc'           => '01',                  // 01 = Factura
                'estab'            => $sale['establecimiento'],
                'ptoEmi'           => $sale['punto_emision'],
                'secuencial'       => $sale['secuencial'],
                'dirMatriz'        => 'Av. Los Libertadores Oe4-131 y pasaje Viracocha',
            ],
            'infoFactura' => [
                'fechaEmision'       => $fechaEmision,
                'dirEstablecimiento' => 'Av. Los Libertadores Oe4-131 y pasaje Viracocha',
                // 'contribuyenteEspecial' => '5368',          // opcional
                // 'obligadoContabilidad' => 'NO',             // obligatorio en muchos casos

                'tipoIdentificacionComprador' => $this->obtenerTipoIdentificacion($sale->customer['num_identificador']),      
                                                // 05=cédula, 04=RUC, 06=pasaporte, 07=consumidorFinal, 08=ident del exterior
                
                'razonSocialComprador'      => $sale->customer['name'],
                'identificacionComprador'   => $sale->customer['num_identificador'],
                // 'direccionComprador'     => 'Direccion comprador',
                'totalSinImpuestos'     => number_format($sale['subtotal'], 2, '.', ''),
                'totalDescuento'        => number_format($sale['discount'], 2, '.', ''),
                // 'totalConImpuestos'     => [
                //     [
                //         'codigo'            => '2',  // IVA
                //         'codigoPorcentaje'  => '4',  // 15%
                //         'baseImponible'     => number_format($subtotalMenosDescuento, 2, '.', ''),
                //         'valor'             => number_format($sale['iva'], 2, '.', ''),    // valor de iva
                //     ]
                // ],

                'totalConImpuestos' => [
                    [
                        'codigo'           => '2',
                        'codigoPorcentaje' => '4',
                        'baseImponible'    => number_format($subtotalMenosDescuento, 2, '.', ''),
                        'valor'            => number_format($sale['iva'], 2, '.', ''),
                    ]
                ],

                
                'propina'       => '0.00',
                'importeTotal'  =>  number_format($sale['total'], 2, '.', ''),
                'moneda'        => 'DOLAR',
                'pagos' => [
                    [
                        'pago' => [
                                    //01    sin utilizar sistema financiero
                                    //16    tarjeta de debito
                                    //17    dinero electronico
                                    //18    tarjeta prepago
                                    //19    tarjeta de credito
                                    //20    otros con utilizacion del sistema financiero
                            'formaPago' => '01',     
                            'total'     => number_format($sale['total'], 2, '.', ''),       // igual a importeTotal o suma de pagos
                            'plazo'     => '0',      // opcional
                            'unidadTiempo' => 'dias' // opcional: dias | meses | años
                        ]
                    ]
                ],
                
            ],
            'detalles' => $sale->details->map(function($detail){
                
                $baseImponible = $detail['subtotal'] - $detail['discount'];
                $ivaCalculado = round(($baseImponible * ($this->iva['value'] ?? 15)) / 100, 2);

                return [
                    'codigoPrincipal'   => $detail['product']->cod_pro ?? '001',
                    'descripcion'       => $detail['product']->name ?? 'Producto sin nombre',
                    'cantidad'          => number_format($detail['quantity'], 2, '.', ''),
                    'precioUnitario'    => number_format($detail['price'], 2, '.', ''),
                    'descuento'         => number_format($detail['discount'], 2, '.', ''),
                    'precioTotalSinImpuesto' => number_format($baseImponible, 2, '.', ''),
                    'impuestos' => [
                        [
                            'codigo'            => '2',  //  IVA
                            'codigoPorcentaje'  => '4',  //  4 -> 15%
                            'tarifa'            => '15',
                            'baseImponible'     => number_format($baseImponible, 2, '.', ''),
                            'valor'             => number_format($ivaCalculado, 2, '.', ''),
                        ]
                    ]
                ];
            }),
            'infoAdicional' => [
                
                    // 'nombre' => 'email',
                    // 'valor' => 'cliente@correo.com'
                    ['nombre' => 'Email',     'valor' => $sale->customer->email ?? 'cliente@correo.com'],
                    ['nombre' => 'Telefono',  'valor' => $sale->customer->phone ?? '0999999999']
                
            ]
        ];
        

        try {
            // 1) Generar XML
            $xml = $this->sri->generarXml($data);
            
            if (!$xml) {
                return [
                    'code' => 405,
                    'status' => 'error',
                    'message' => 'error al generar xml',
                    'errors' => 'Error al generar xml'
                ];
                throw new \Exception('Error al generar XML');
            }

            $nombreArchivo = $data['infoTributaria']['claveAcceso'] . '.xml';

            // 2) Guardar XML original
            $rutaGenerados = storage_path('app/facturas/generados/' . $nombreArchivo);
            $this->guardarArchivo($rutaGenerados, $xml);

            // 3) Firmar XML
            // $certificadoPath = storage_path('app/certificados/firma_prueba.p12'); // Usar separador correcto
            // $claveCertificado = env('CERTIFICADO_CLAVE', '123456'); // Usar variable de entorno
            $certificadoPath = storage_path('app/certificados/firma_juel/uanataca_aes.p12'); // Usar separador correcto
            $claveCertificado = env('CERTIFICADO_CLAVE', 'Parabrisas26'); // Usar variable de entorno



            $xmlFirmado = $this->sri->firmarXml($xml, $certificadoPath, $claveCertificado);
            
            if (!$xmlFirmado) {
                return [
                    'code' => 405,
                    'status' => 'error',
                    'message' => 'error al firmar xml',
                    'errors' => 'Error al firmar XML'
                ];
                throw new \Exception('Error al firmar XML');
            }

            // 4) Validar contra XSD
            $resultado = $this->sri->validarContraXsd($xmlFirmado);
            if (!$resultado['valid']) {
                return [
                    'code' => 405,
                    'status' => 'error',
                    'message' => 'error al validar contra xsd',
                    'errors' => $resultado['errors']
                ];
            }

            // 5) Guardar XML firmado
            $rutaFirmados = storage_path('app/facturas/firmados/' . $nombreArchivo);
            $this->guardarArchivo($rutaFirmados, $xmlFirmado);

            // 6) Enviar al SRI
            // $ambiente = 1;
            $ambiente = env('SRI_AMBIENTE', '1') === '1' ? 'pruebas' : 'produccion';

            // Enviar comprobante
            $respuesta = $this->sri->enviarComprobanteSri($xmlFirmado, $ambiente);

            if ($respuesta['estado'] === 'RECIBIDA') {
                
                // 7) Guardar XML enviado y recibido por el sri
                $rutaEnviados = storage_path('app/facturas/enviados/' . $nombreArchivo);
                $this->guardarArchivo($rutaEnviados, $xmlFirmado);
                
                // 8) Autorizar comprobante



$intentos = 8;
$respuestaAutorizacion = null;

for ($i = 0; $i < $intentos; $i++) {
    sleep(5);

    $respuestaAutorizacion = $this->sri->autorizarSri(
        $data['infoTributaria']['claveAcceso'],
        $ambiente
    );

    if (in_array($respuestaAutorizacion['estado'], ['AUTORIZADO', 'NO AUTORIZADO'])) {
        break;
    }
}


                // $respuestaAutorizacion = $this->sri
                //                             ->autorizarSri(
                //                                 $data['infoTributaria']['claveAcceso'], 
                //                                 $ambiente);
                
                if($respuestaAutorizacion['estado'] === 'AUTORIZADO'){
                    return [
                        'code'      => 200,
                        'status'    => 'success',
                        'message'   => 'Comprobante generado y enviado correctamente',
                        'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                        'respuesta' => $respuestaAutorizacion
                    ];
                }else{
                    return [
                        'code'      => 505,
                        'status'    => 'error',
                        'message'   => $respuestaAutorizacion['estado'],
                        'clave_acceso' => $data['infoTributaria']['claveAcceso'],
                        'respuesta' => $respuestaAutorizacion
                    ];
                }
                                            



            } else {
                return [
                    'code'      => 405,
                    'status'    => 'error',
                    'message'   => 'SRI no recibió el XML firmado',
                    'respuesta' => $respuesta
                ];
            }

        } catch (\Exception $e) {
            return [
                'code'  => 500,
                'status' => 'error',
                'message' => 'Error en el proceso: ' . $e->getMessage()
            ];
        }
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

    

    public function sendFacturaPdfXml(Request $request, $clave, $mailCustomerSale)
    {
        $numero_factura = $request->numero_factura;

        // rutas de tus archivos ya generados
        $pdfPath = storage_path("app/facturas/pdfs/{$clave}.pdf");
        $xmlPath = storage_path("app/facturas/enviados/{$clave}.xml");

        if (!file_exists($pdfPath)) {
            error_log("No existe: " . $pdfPath);
        }
        if (!file_exists($xmlPath)) {
            error_log("No existe: " . $xmlPath);
        }

        Mail::to("adrian-2222@hotmail.com")->send(new FacturaCustomerPdfXmlMail($pdfPath, $xmlPath, $numero_factura));

        return response()->json(['code'=>200, 'message'=>'Factura enviada con éxito.']);
    }


    public function pdf($id)
    {
        // Limpiar buffers
        if (ob_get_length()) ob_clean();
        
        $sale = Sale::with(['customer','details'])->findOrFail($id);
        $pdf = Pdf::loadView('factura.pdf', compact('sale'))
                                ->setPaper('a4');

        $filename = $sale->clave_acceso . '.pdf';
        $path = storage_path("app/facturas/pdfs/{$filename}");

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

    public function rePrintFacturaPdf($id)
    {
        // Limpiar buffers
        if (ob_get_length()) ob_clean();
        
        $sale = Sale::with(['customer','details'])->findOrFail($id);
        $pdf = Pdf::loadView('factura.rePrintPdf', compact('sale'))
                                ->setPaper('a4');

        // Mostrar en el navegador (abre el PDF directamente)
        return $pdf->stream($sale->clave_acceso . '.pdf');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {  
        $sale = Sale::with(['customer', 'receivables','details.product'])->find($id);

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
