<?php

namespace App\Jobs;

use App\Models\Sale;
use App\Models\PointsOfSale;
use App\Models\Inventory;
use App\Services\SriFacturaService;
use App\Services\FacturaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ConsultarAutorizacionSriJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;
    public int $timeout = 60;

    private const LIMITE_REENVIO  = 20 * 60;
    private const LIMITE_ABANDONO = 6 * 60 * 60;
    private const MAX_REENVIOS    = 3;

    public function __construct(
        private readonly string $claveAcceso,
        private readonly string $ambiente,
        private readonly int    $saleId,
        private readonly int    $creadoEn    = 0,
        private readonly int    $reenvios    = 0,
        private readonly int    $ultimoEnvio = 0,
    ) {}

    public function handle(SriFacturaService $sri): void
    {
        $sale = Sale::with('customer')->find($this->saleId);

        if (!$sale) {
            Log::error('SRI Job: Sale no encontrada', ['id' => $this->saleId]);
            return;
        }

        $ahora = now()->timestamp;

        $creadoEn    = $this->creadoEn    ?: $ahora;
        $ultimoEnvio = $this->ultimoEnvio ?: $ahora;

        $transcurridos    = $ahora - $creadoEn;
        $desdeUltimoEnvio = $ahora - $ultimoEnvio;

        $respuesta = $sri->autorizarSri($this->claveAcceso, $this->ambiente);

        // 🔥 VALIDACIÓN RESPONSE
        if (!isset($respuesta['estado'])) {
            Log::error('Respuesta inválida SRI', $respuesta);

            $this->redespachar(60, $creadoEn, $ultimoEnvio);
            return;
        }

        Log::info('SRI Job', [
            'clave'        => $this->claveAcceso,
            'estado'       => $respuesta['estado'],
            'tiempo_total' => $transcurridos . 's',
            'desde_envio'  => $desdeUltimoEnvio . 's',
            'reenvios'     => $this->reenvios,
        ]);

        // ================= AUTORIZADO =================
        if ($respuesta['estado'] === 'AUTORIZADO') {
            $this->procesarAutorizado($respuesta, $sale);
            return;
        }

        // ================= NO AUTORIZADO =================
        if ($respuesta['estado'] === 'NO AUTORIZADO') {
            $this->procesarNoAutorizado($respuesta, $sale);
            return;
        }

        // ================= ERROR SRI =================
        if ($respuesta['estado'] === 'ERROR') {

            if ($transcurridos >= self::LIMITE_ABANDONO) {
                $sale->update(['estado_sri' => 'ERROR']);
                return;
            }

            Log::warning('SRI Job: error conexión, retry 30s');

            $this->redespachar(30, $creadoEn, $ultimoEnvio);
            return;
        }

        // ================= TIEMPO MÁXIMO =================
        if ($transcurridos >= self::LIMITE_ABANDONO) {

            Log::error('Tiempo máximo alcanzado', [
                'clave' => $this->claveAcceso
            ]);

            $sale->update(['estado_sri' => 'EXPIRADO']);
            return;
        }

        // ================= REENVÍO =================
        if ($desdeUltimoEnvio >= self::LIMITE_REENVIO && $this->reenvios < self::MAX_REENVIOS) {

            $xmlPath = storage_path('app/facturas/firmados/' . $this->claveAcceso . '.xml');

            if (!file_exists($xmlPath)) {
                Log::error('XML no encontrado', ['clave' => $this->claveAcceso]);
                $sale->update(['estado_sri' => 'ERROR']);
                return;
            }

            Log::warning('Reenviando al SRI', [
                'reenvio' => $this->reenvios + 1
            ]);

            $xml = file_get_contents($xmlPath);
            $recepcion = $sri->enviarComprobanteSri($xml, $this->ambiente);

            $ahora = now()->timestamp;

            // 🔥 VALIDAR RESPUESTA
            if (!isset($recepcion['estado'])) {
                Log::error('Respuesta inválida en reenvío', $recepcion);

                $this->redespachar(60, $creadoEn, $ultimoEnvio);
                return;
            }

            // 🔥 VALIDAR DEVUELTA 70
            if ($recepcion['estado'] === 'DEVUELTA') {

                $mensajes = $recepcion['mensajes'] ?? [];

                $es70 = collect($mensajes)
                    ->contains(fn($m) => ($m['identificador'] ?? '') === '70');

                if (!$es70) {
                    Log::error('DEVUELTA inválida', $mensajes);

                    $sale->update(['estado_sri' => 'NO AUTORIZADO']);
                    return;
                }
            }

            if (in_array($recepcion['estado'], ['RECIBIDA', 'DEVUELTA'])) {

                $sale->update(['estado_sri' => 'PENDIENTE']);

                self::dispatch(
                    $this->claveAcceso,
                    $this->ambiente,
                    $this->saleId,
                    $creadoEn,
                    $this->reenvios + 1,
                    $ahora
                )->delay(now()->addSeconds(20));

                return;
            }

            Log::error('Reenvío fallido', $recepcion);

            self::dispatch(
                $this->claveAcceso,
                $this->ambiente,
                $this->saleId,
                $creadoEn,
                $this->reenvios,
                $ahora
            )->delay(now()->addSeconds(120));

            return;
        }

        // ================= MÁXIMO REENVÍOS =================
        if ($this->reenvios >= self::MAX_REENVIOS && $desdeUltimoEnvio >= self::LIMITE_REENVIO) {

            Log::error('Máximo de reenvíos alcanzado');

            $sale->update(['estado_sri' => 'EXPIRADO']);
            return;
        }

        // ================= RETRY PROGRESIVO =================
        $delay = match (true) {
            $transcurridos < 300    => 20,
            $transcurridos < 1800   => 60,
            $transcurridos < 7200   => 120,
            $transcurridos < 14400  => 300,
            default                 => 600,
        };

        Log::info("Reintentando en {$delay}s");

        $this->redespachar($delay, $creadoEn, $ultimoEnvio);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('SRI Job fallo', [
            'error' => $e->getMessage()
        ]);

        Sale::where('id', $this->saleId)
            ->update(['estado_sri' => 'ERROR']);
    }

    private function redespachar(int $delay, int $creadoEn, int $ultimoEnvio): void
    {
        self::dispatch(
            $this->claveAcceso,
            $this->ambiente,
            $this->saleId,
            $creadoEn,
            $this->reenvios,
            $ultimoEnvio
        )->delay(now()->addSeconds($delay));
    }

    // private function procesarAutorizado(array $respuesta, Sale $sale): void
    // {
    //     $user        = auth()->user();
    //     $pointOfSale = $user->pointsOfSale()->with('branch')->first();
     
    //     if (!$pointOfSale) {
    //         return response()->json(['error' => 'El usuario no tiene puntos de venta asignados'], 403);
    //     }
     
    //     $id_branch = $pointOfSale->id_branch;
    //     if (!$id_branch) {
    //         return response()->json(['error' => 'Usuario no tiene sucursal asignada'], 403);
    //     }


    //     $sale = Sale::findOrFail($this->saleId);


    //     foreach ($sale->details as $detail) {
    //         $product = $detail->product;
    //         $inventory = Inventory::where('id_product', $product->id)
    //                                 ->where('id_branch', $id_branch)
    //                                 ->first();
    //         $inventory->update(['stock' => $inventory->stock - $detail->quantity]);
    
    //     }




    //     $fechaAutorizacionRaw = $respuesta['fechaAutorizacion'] ?? null;

    //     $fechaAutorizacion = $fechaAutorizacionRaw
    //         ? Carbon::parse($fechaAutorizacionRaw)->format('Y-m-d H:i:s')
    //         : now()->format('Y-m-d H:i:s');


    //     $sale->update([
    //         'estado_sri'             => 'AUTORIZADO',
    //         'numero_autorizacion'    => $respuesta['numeroAutorizacion'],
    //         'fecha_autorizacion_sri' => $fechaAutorizacion,
    //     ]);

    //     if (!empty($respuesta['xml'])) {
    //         $ruta = storage_path('app/facturas/autorizados/' . $this->claveAcceso . '.xml');
    //         @mkdir(dirname($ruta), 0755, true);
    //         file_put_contents($ruta, $respuesta['xml']);
    //     }

    //     Log::info('AUTORIZADO', ['clave' => $this->claveAcceso]);

    //     try {
    //         $email = $sale->customer->email ?? null;

    //         if (!$email) {
    //             Log::warning('Cliente sin email', ['sale_id' => $sale->id]);
    //             return;
    //         }

    //         app(FacturaService::class)->sendFacturaPdfXml(
    //             $this->claveAcceso,
    //             $email
    //         );

    //     } catch (\Throwable $e) {
    //         Log::error('Error enviando factura', [
    //             'error' => $e->getMessage()
    //         ]);
    //     }
    // }


    private function procesarAutorizado(array $respuesta, Sale $sale): void
    {
        if ($sale->estado_sri === 'AUTORIZADO') {
            return;
        }

        $sale->load('details.product', 'customer');
        $point_of_sale = PointsOfSale::where('id', $sale->id_point_of_sale)->first();

        if (!$point_of_sale) {
            Log::error('Punto de venta no encontrado', [
                'sale_id' => $sale->id,
                'id_point_of_sale' => $sale->id_point_of_sale
            ]);
            return;
        }


        foreach ($sale->details as $detail) 
        {
            $product = $detail->product;
            if (!$product) {
                continue;
            }

            $inventory = Inventory::where('id_product', $product->id)
                                    ->where('id_branch', $point_of_sale->id_branch)
                                    ->first();
            if (!$inventory) {
                Log::error('Inventario no encontrado', [
                    'product_id' => $product->id,
                    'branch_id' => $point_of_sale->id_branch
                ]);
                continue;
            }

            $inventory->decrement('stock', $detail->quantity);
        }

        $fechaAutorizacionRaw = $respuesta['fechaAutorizacion'] ?? null;
        $fechaAutorizacion = $fechaAutorizacionRaw
            ? Carbon::parse($fechaAutorizacionRaw)->format('Y-m-d H:i:s')
            : now()->format('Y-m-d H:i:s');
        $sale->update([
            'estado_sri'             => 'AUTORIZADO',
            'numero_autorizacion'    => $respuesta['numeroAutorizacion'],
            'fecha_autorizacion_sri' => $fechaAutorizacion,
        ]);
        if (!empty($respuesta['xml'])) {
            $ruta = storage_path('app/facturas/autorizados/' . $this->claveAcceso . '.xml');
            @mkdir(dirname($ruta), 0755, true);
            file_put_contents($ruta, $respuesta['xml']);
        }
        Log::info('AUTORIZADO', ['clave' => $this->claveAcceso]);
        try {
            $email = $sale->customer->email ?? null;

            if (!$email) {
                Log::warning('Cliente sin email', ['sale_id' => $sale->id]);
                return;
            }

            app(FacturaService::class)->sendFacturaPdfXml(
                $this->claveAcceso,
                $email
            );

        } catch (\Throwable $e) {
            Log::error('Error enviando factura', [
                'error' => $e->getMessage()
            ]);
        }
    }


    private function procesarNoAutorizado(array $respuesta, Sale $sale): void
    {
        $sale->update(['estado_sri'         => 'NO AUTORIZADO',
                       'error_no_autorizada'=> $respuesta['mensajes']  
                      ]);

        $ruta = storage_path('app/facturas/no_autorizados/' . $this->claveAcceso . '.xml');
        @mkdir(dirname($ruta), 0755, true);
        file_put_contents($ruta, $respuesta['xml']);
        Log::warning('NO AUTORIZADO', $respuesta['mensajes']);
    }


}