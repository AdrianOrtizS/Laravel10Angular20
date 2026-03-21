<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ambiente = env('SRI_AMBIENTE', '1') === '1' ? 'pruebas' : 'produccion';
$ahora    = now()->timestamp;

$ventas = \App\Models\Sale::where('estado_sri', 'PENDIENTE')->get();

echo "Ventas PENDIENTE: " . count($ventas) . "\n\n";

foreach ($ventas as $sale) {
    $xmlPath = storage_path('app/facturas/firmados/' . $sale->clave_acceso . '.xml');
    $existe  = file_exists($xmlPath);

    echo "Clave: {$sale->clave_acceso}\n";
    echo "XML:   " . ($existe ? "✅ existe" : "❌ NO existe — {$xmlPath}") . "\n";

    if (!$existe) {
        echo "→ Saltando\n\n";
        continue;
    }

    // Pasar ultimoEnvio = ahora - 11 minutos para forzar reenvío inmediato
    \App\Jobs\ConsultarAutorizacionSriJob::dispatch(
        $sale->clave_acceso,
        $ambiente,
        $sale->id,
        $ahora,
        0,
        $ahora - (11 * 60)  // hace 11 min → fuerza reenvío en el primer intento
    )->delay(now()->addSeconds(rand(5, 20)));

    echo "→ Encolada\n\n";
}

echo "Listo.\n";