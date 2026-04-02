<?php
 
namespace App\Services;
 
use DOMDocument;
use DOMXPath;
use Exception;
use DateTime;
use Illuminate\Support\Facades\Mail;
use App\Mail\FacturaCustomerPdfXmlMail;
 
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

}