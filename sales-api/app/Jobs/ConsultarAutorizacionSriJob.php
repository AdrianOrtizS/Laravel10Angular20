<?php

namespace App\Jobs;

use App\Models\Sale;
use App\Services\SriFacturaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConsultarAutorizacionSriJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 1;
    public int $timeout = 60;

    // Tiempos en segundos
    private const LIMITE_REENVIO  = 10 * 60;  // 10 min sin respuesta → reenviar
    private const LIMITE_ABANDONO = 120 * 60; // 120 min total → abandonar

    private const MAX_REENVIOS    = 3;         // máximo 3 reenvíos antes de abandonar

    public function __construct(
        private readonly string $claveAcceso,
        private readonly string $ambiente,
        private readonly int    $saleId,
        private readonly int    $creadoEn    = 0,  // timestamp del primer despacho
        private readonly int    $reenvios    = 0,  // cuántas veces se ha reenviado al SRI
        private readonly int    $ultimoEnvio = 0,  // timestamp del último envío al SRI
    ) {}

    public function handle(SriFacturaService $sri): void
    {
        $sale = Sale::find($this->saleId);
        if (!$sale) {
            Log::error('SRI Job: Sale no encontrada', ['id' => $this->saleId]);
            return;
        }

        $respuesta          = $sri->autorizarSri($this->claveAcceso, $this->ambiente);
        $transcurridos      = now()->timestamp - $this->creadoEn;
        $desdeUltimoEnvio   = now()->timestamp - $this->ultimoEnvio;

        Log::info('SRI Job', [
            'clave'         => $this->claveAcceso,
            'estado'        => $respuesta['estado'],
            'tiempo_total'  => $transcurridos . 's',
            'desde_envio'   => $desdeUltimoEnvio . 's',
            'reenvios'      => $this->reenvios,
        ]);

        // ------------------------------------------------------------------
        // AUTORIZADO — estado final exitoso
        // ------------------------------------------------------------------
        if ($respuesta['estado'] === 'AUTORIZADO') {
            $this->procesarAutorizado($respuesta, $sale);
            return;
        }

        // ------------------------------------------------------------------
        // NO AUTORIZADO — estado final, el SRI rechazó el comprobante
        // ------------------------------------------------------------------
        if ($respuesta['estado'] === 'NO AUTORIZADO') {
            $this->procesarNoAutorizado($respuesta, $sale);
            return;
        }

        // ------------------------------------------------------------------
        // ERROR de red/conexión — reintentar pronto, no contar como reenvío
        // ------------------------------------------------------------------
        if ($respuesta['estado'] === 'ERROR') {
            Log::warning('SRI Job: error de conexión, reintentando en 30s', [
                'clave'   => $this->claveAcceso,
                'mensaje' => $respuesta['mensaje'] ?? null,
            ]);

            if ($transcurridos >= self::LIMITE_ABANDONO) {
                $sale->update(['estado_sri' => 'ERROR']);
                Log::error('SRI Job: abandonado por error de conexión persistente', ['clave' => $this->claveAcceso]);
                return;
            }

            $this->redespachar(30, $sale);
            return;
        }

        // ------------------------------------------------------------------
        // EN_PROCESO — SRI aún no tiene la clave o está procesando
        // ------------------------------------------------------------------

        // Límite total absoluto
        if ($transcurridos >= self::LIMITE_ABANDONO) {
            Log::error('SRI Job: tiempo límite absoluto alcanzado', [
                'clave'    => $this->claveAcceso,
                'reenvios' => $this->reenvios,
            ]);
            $sale->update(['estado_sri' => 'PENDIENTE']);
            return;
        }

        // ¿Han pasado 10 minutos desde el último envío sin respuesta? → REENVIAR
        if ($desdeUltimoEnvio >= self::LIMITE_REENVIO && $this->reenvios < self::MAX_REENVIOS) {

            $xmlPath = storage_path('app/facturas/firmados/' . $this->claveAcceso . '.xml');

            if (!file_exists($xmlPath)) {
                Log::error('SRI Job: XML firmado no encontrado para reenvío', [
                    'clave' => $this->claveAcceso,
                    'path'  => $xmlPath,
                ]);
                $sale->update(['estado_sri' => 'ERROR']);
                return;
            }

            Log::warning('SRI Job: reenviando comprobante al SRI', [
                'clave'         => $this->claveAcceso,
                'reenvio_num'   => $this->reenvios + 1,
                'desde_envio'   => $desdeUltimoEnvio . 's',
            ]);

            $xmlFirmado = file_get_contents($xmlPath);
            $recepcion  = $sri->enviarComprobanteSri($xmlFirmado, $this->ambiente);

            Log::info('SRI Job: resultado reenvío', [
                'clave'  => $this->claveAcceso,
                'estado' => $recepcion['estado'],
            ]);

            $ahora = now()->timestamp;

            if (in_array($recepcion['estado'], ['RECIBIDA', 'DEVUELTA'])) {
                // Reenvío exitoso — reiniciar ciclo de consulta
                $sale->update(['estado_sri' => 'PENDIENTE']);
                self::dispatch(
                    $this->claveAcceso,
                    $this->ambiente,
                    $this->saleId,
                    $this->creadoEn,
                    $this->reenvios + 1,
                    $ahora  // resetear timestamp de último envío
                )->delay(now()->addSeconds(20));
                return;
            }

            // Reenvío falló — reintentar reenvío más tarde
            Log::error('SRI Job: reenvío fallido', [
                'clave'   => $this->claveAcceso,
                'estado'  => $recepcion['estado'],
                'mensajes' => $recepcion['mensajes'] ?? [],
            ]);

            // Esperar 2 minutos y volver a intentar el reenvío
            self::dispatch(
                $this->claveAcceso,
                $this->ambiente,
                $this->saleId,
                $this->creadoEn,
                $this->reenvios,    // NO incrementar — el reenvío no fue exitoso
                $this->ultimoEnvio  // mantener timestamp para volver a intentar pronto
            )->delay(now()->addSeconds(120));
            return;
        }

        // Si ya se alcanzó el máximo de reenvíos sin éxito
        if ($this->reenvios >= self::MAX_REENVIOS && $desdeUltimoEnvio >= self::LIMITE_REENVIO) {
            Log::error('SRI Job: máximo de reenvíos alcanzado sin autorización', [
                'clave'    => $this->claveAcceso,
                'reenvios' => $this->reenvios,
            ]);
            $sale->update(['estado_sri' => 'PENDIENTE']);
            return;
        }

        // Delay progresivo normal mientras espera la autorización
        $delay = match (true) {
            $transcurridos < 60  => 15,
            $transcurridos < 180 => 25,
            $transcurridos < 600 => 40,
            default              => 60,
        };

        Log::warning('SRI Job: reintentando consulta', [
            'clave'  => $this->claveAcceso,
            'delay'  => $delay . 's',
            'tiempo' => $transcurridos . 's',
        ]);

        $this->redespachar($delay, $sale);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('SRI Job: excepción no capturada', [
            'clave' => $this->claveAcceso,
            'error' => $e->getMessage(),
        ]);
        Sale::where('id', $this->saleId)->update(['estado_sri' => 'ERROR']);
    }

    // ------------------------------------------------------------------
    // HELPERS
    // ------------------------------------------------------------------

    private function redespachar(int $delay, Sale $sale): void
    {
        self::dispatch(
            $this->claveAcceso,
            $this->ambiente,
            $this->saleId,
            $this->creadoEn,
            $this->reenvios,
            $this->ultimoEnvio
        )->delay(now()->addSeconds($delay));
    }

    private function procesarAutorizado(array $respuesta, Sale $sale): void
    {
        $sale->update([
            'estado_sri'             => 'AUTORIZADO',
            'numero_autorizacion'    => $respuesta['numeroAutorizacion'] ?? null,
            'fecha_autorizacion_sri' => $respuesta['fechaAutorizacion']  ?? null,
        ]);

        if (!empty($respuesta['xml'])) {
            $ruta = storage_path('app/facturas/autorizados/' . $this->claveAcceso . '.xml');
            @mkdir(dirname($ruta), 0755, true);
            file_put_contents($ruta, $respuesta['xml']);
        }

        Log::info('SRI Job: AUTORIZADO', [
            'clave'            => $this->claveAcceso,
            'num_autorizacion' => $respuesta['numeroAutorizacion'] ?? null,
            'fecha'            => $respuesta['fechaAutorizacion']  ?? null,
            'reenvios'         => $this->reenvios,
        ]);

        // Descomentar cuando estén listos:
        // try {
        //     app(\App\Http\Controllers\API\SaleController::class)->pdf($this->saleId);
        //     app(\App\Http\Controllers\API\SaleController::class)->sendFacturaPdfXml(
        //         $this->claveAcceso,
        //         $sale->customer->email ?? ''
        //     );
        // } catch (\Throwable $e) {
        //     Log::warning('PDF/Email falló: ' . $e->getMessage());
        // }
    }

    private function procesarNoAutorizado(array $respuesta, Sale $sale): void
    {
        $sale->update(['estado_sri' => 'NO AUTORIZADO']);

        Log::warning('SRI Job: NO AUTORIZADO', [
            'clave'    => $this->claveAcceso,
            'mensajes' => $respuesta['mensajes'] ?? [],
        ]);
    }
}