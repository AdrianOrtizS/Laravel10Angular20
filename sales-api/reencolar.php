<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$ambienteCode = env('SRI_AMBIENTE', '1');
$ambiente = $ambienteCode === '1' ? 'pruebas' : 'produccion';

$ahora = now()->timestamp;

// 🔥 PROCESAR EN CHUNKS (NO MATAR MEMORIA)
\App\Models\Sale::where('estado_sri', 'EXPIRADO')
    ->orderBy('id')
    ->chunk(50, function ($ventas) use ($ambiente, $ahora) {

        $sri = app(\App\Services\SriFacturaService::class);

        foreach ($ventas as $sale) {

            $xmlPath = storage_path('app/facturas/firmados/' . $sale->clave_acceso . '.xml');

            if (!file_exists($xmlPath)) {
                echo "❌ XML no existe: {$sale->clave_acceso}\n\n";
                continue;
            }

            echo "Clave: {$sale->clave_acceso}\n";

            try {

                $xml = file_get_contents($xmlPath);
                $recepcion = $sri->enviarComprobanteSri($xml, $ambiente);

                // 🔥 VALIDAR RESPUESTA
                if (!isset($recepcion['estado'])) {
                    echo "❌ Respuesta inválida del SRI\n\n";
                    continue;
                }

                echo "Recepción: {$recepcion['estado']}\n";

                // ================= DEVUELTA =================
                if ($recepcion['estado'] === 'DEVUELTA') {

                    $mensajes = $recepcion['mensajes'] ?? [];

                    $es70 = collect($mensajes)
                        ->contains(fn($m) => ($m['identificador'] ?? '') === '70');

                    if (!$es70) {
                        echo "❌ DEVUELTA inválida\n\n";

                        $sale->update(['estado_sri' => 'NO AUTORIZADO']);
                        continue;
                    }
                }

                // ================= ERROR =================
                if ($recepcion['estado'] === 'ERROR') {
                    echo "⚠️ Error conexión SRI\n\n";
                    continue;
                }

                // ================= VALIDACIÓN =================
                if (!in_array($recepcion['estado'], ['RECIBIDA', 'DEVUELTA'])) {
                    echo "❌ No se pudo reenviar\n\n";
                    continue;
                }

                // ================= OK =================
                $sale->update(['estado_sri' => 'PENDIENTE']);

                $creadoEnOriginal = $sale->created_at
                    ? strtotime($sale->created_at)
                    : $ahora;

                \App\Jobs\ConsultarAutorizacionSriJob::dispatch(
                    $sale->clave_acceso,
                    $ambiente,
                    $sale->id,
                    $creadoEnOriginal,
                    0,
                    $ahora
                )->delay(now()->addSeconds(rand(5, 15)));

                echo "✅ Reenviada y reencolada\n\n";

                // 🔥 ANTI-SPAM SRI (clave en producción)
                usleep(300000); // 0.3 segundos

            } catch (\Throwable $e) {

                echo "❌ Error: {$e->getMessage()}\n\n";
            }
        }
    });

echo "Listo.\n";