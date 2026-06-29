<?php
 
namespace App\Services;
 
use DOMDocument;
use DOMXPath;
use Exception;
use DateTime;
use Illuminate\Support\Facades\Mail;
use App\Mail\FacturaCustomerPdfXmlMail;
use App\Models\Configuration;
use App\Models\Sale;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaService
{

    public function sendFacturaPdfXml($clave, $mailCustomerSale)
    {
        $numero_factura = $clave;

        // rutas de tus archivos ya generados
        $pdfPath = storage_path("app/facturas/pdfs/{$clave}.pdf");
        $xmlPath = storage_path("app/facturas/autorizados/{$clave}.xml");

        if (!file_exists($pdfPath)) {
            error_log("No existe: " . $pdfPath);
            return false;
        }
        if (!file_exists($xmlPath)) {
            error_log("No existe: " . $xmlPath);
            return false;
        }

        Mail::to("adrian-2222@hotmail.com")->send(new FacturaCustomerPdfXmlMail($pdfPath, $xmlPath, $numero_factura));

        return true;
    }


    public function pdf($id)
    {
        $sale = $this->generarPdf($id);
        $path = storage_path(
            'app/facturas/pdfs/' . $sale->clave_acceso . '.pdf'
        );
        return response()->download(
            $path,
            $sale->clave_acceso . '.pdf',
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }

    
    public function barcodeGeneratorPng($clave_acceso)
    {
        $generator = new BarcodeGeneratorPNG();

        $barcode = base64_encode(
            $generator->getBarcode(
                $clave_acceso,
                $generator::TYPE_CODE_128
            )
        );

        return $barcode;        
    }

    public function generarPdf($id)
    {
        if (ob_get_length()) {
            ob_clean();
        }
        $configs = Configuration::whereIn('name', [
            'version',
            'ambiente',
            'razonSocial',
            'nombreComercial',
            'ruc',
            'dirMatriz',
            'obligadoContabilidad',
            'iva',
            'logoPdf',
            'correo'
        ])->pluck('value', 'name');
        $sale = Sale::with(['customer', 'details'])->findOrFail($id);
        $barcode = $this->barcodeGeneratorPng($sale->clave_acceso);
        $pdf = Pdf::loadView('factura.pdf', [
            'sale' => $sale,
            'version' => $configs['version'] ?? '',
            'ambiente' => $configs['ambiente'] ?? '',
            'razonSocial' => $configs['razonSocial'] ?? '',
            'nombreComercial' => $configs['nombreComercial'] ?? '',
            'ruc' => $configs['ruc'] ?? '',
            'dirMatriz' => $configs['dirMatriz'] ?? '',
            'obligadoContabilidad' => $configs['obligadoContabilidad'] ?? '',
            'iva' => $configs['iva'] ?? '',
            'logoPdf' => $configs['logoPdf'] ?? '',
            'correo' => $configs['correo'] ?? '',
            'barcode' => $barcode,
        ])->setPaper('a4');

        $filename = $sale->clave_acceso . '.pdf';
        $path = storage_path("app/facturas/pdfs/{$filename}");
        $directory = dirname($path);

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }
        $pdf->save($path);
        // Devuelve la venta actualizada
        return Sale::with(['customer', 'details'])
            ->findOrFail($id);
    }


    public function rePrintFacturaPdf($clave)
    {
        $path = storage_path("app/facturas/pdfs/{$clave}.pdf");

        if (!file_exists($path)) {

            $sale = Sale::where('clave_acceso', $clave)->first();

            if (!$sale) {
                abort(404, 'Factura no encontrada');
            }

            // Regenera el PDF
            $sale = $this->generarPdf($sale->id);

            // Verifica que se haya generado
            if (!file_exists($path)) {
                abort(500, 'No fue posible generar el PDF');
            }
        }

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $clave . '.pdf"',
        ]);
    }



}