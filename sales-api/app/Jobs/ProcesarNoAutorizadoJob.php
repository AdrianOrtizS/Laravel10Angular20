<?php

namespace App\Jobs;

use App\Models\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcesarNoAutorizadoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $saleId,
        private array $respuesta
    ) {}

    public function handle(): void
    {
        $sale = Sale::find($this->saleId);

        if (!$sale) return;

        $sale->update([
            'estado_sri' => 'NO AUTORIZADO',
            'error_no_autorizada' => $this->respuesta['mensajes'] ?? null,
        ]);
    }
}