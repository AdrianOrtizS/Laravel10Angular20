<?php

namespace App\Jobs;

use App\Models\Sale;
use App\Services\SriFacturaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ConsultarAutorizacionSriJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 30;

    public function __construct(
        private string $claveAcceso,
        private string $ambiente,
        private int $saleId
    ) {}

    public function handle(SriFacturaService $sri): void
    {
        $sale = Sale::find($this->saleId);

        if (!$sale) return;

        $respuesta = $sri->autorizarSri($this->claveAcceso, $this->ambiente);

        if (!isset($respuesta['estado'])) {
            self::dispatch($this->claveAcceso, $this->ambiente, $this->saleId)
                ->delay(now()->addSeconds(30));
            return;
        }

        if ($respuesta['estado'] === 'AUTORIZADO') {

            $sale->update([
                'estado_sri' => 'AUTORIZADO',
                'numero_autorizacion' => $respuesta['numeroAutorizacion'] ?? null,
                'fecha_autorizacion_sri' => now(),
            ]);

            ProcesarAutorizadoJob::dispatch($sale->id, $respuesta);

            return;
        }

        if ($respuesta['estado'] === 'NO AUTORIZADO') {

            $sale->update([
                'estado_sri' => 'NO AUTORIZADO',
                'error_no_autorizada' => $respuesta['mensajes'] ?? null,
            ]);

            ProcesarNoAutorizadoJob::dispatch($sale->id, $respuesta);

            return;
        }

        $sale->update(['estado_sri' => 'PENDIENTE']);

        self::dispatch($this->claveAcceso, $this->ambiente, $this->saleId)
            ->delay(now()->addSeconds(20));
    }
}