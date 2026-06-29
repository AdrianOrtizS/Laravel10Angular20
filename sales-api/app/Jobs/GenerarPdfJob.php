<?php

namespace App\Jobs;

use App\Models\Sale;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\FacturaService;

class GenerarPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private int $saleId) {}

    public function handle(FacturaService $facturaService): void
    {
        $sale = Sale::find($this->saleId);

        if (!$sale) return;

        app(\App\Services\FacturaService::class)
            ->generarPdf($sale->id);
    }
}