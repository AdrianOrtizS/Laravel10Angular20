<?php

namespace App\Jobs;

use App\Models\Sale;
use App\Models\Inventory;
use App\Models\PointsOfSale;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcesarAutorizadoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $saleId,
        private array $respuesta
    ) {}

    public function handle(): void
    {
        $sale = Sale::with('details')->find($this->saleId);

        if (!$sale) return;

        $point = PointsOfSale::find($sale->id_point_of_sale);

        if (!$point) return;

        // 🔥 INVENTARIO OPTIMIZADO (sin N+1)
        $inventories = Inventory::whereIn('id_product', $sale->details->pluck('id_product'))
            ->where('id_branch', $point->id_branch)
            ->get()
            ->keyBy('id_product');

        foreach ($sale->details as $detail) {
            $inv = $inventories[$detail->id_product] ?? null;
            if ($inv) {
                $inv->decrement('stock', $detail->quantity);
            }
        }

        // 🔥 XML
        if (!empty($this->respuesta['xml'])) {
            $path = storage_path("app/facturas/autorizados/{$sale->clave_acceso}.xml");
            @mkdir(dirname($path), 0755, true);
            file_put_contents($path, $this->respuesta['xml']);
        }

        // 🔥 DISPARAR SIGUIENTES JOBS
        GenerarPdfJob::dispatch($sale->id);
        EnviarFacturaEmailJob::dispatch($sale->id);
    }
}