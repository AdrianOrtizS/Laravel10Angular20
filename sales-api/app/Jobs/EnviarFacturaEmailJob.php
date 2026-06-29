<?php

namespace App\Jobs;

use App\Models\Sale;
use App\Services\FacturaService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EnviarFacturaEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private int $saleId) {}

    public function handle(FacturaService $service): void
    {
        $sale = Sale::with('customer')->find($this->saleId);

        if (!$sale || !$sale->customer?->email) return;

        $email = 'adrian-2222@hotmail.com';

        $service->sendFacturaPdfXml(
            $sale->clave_acceso,
            $email
        );
    }
}