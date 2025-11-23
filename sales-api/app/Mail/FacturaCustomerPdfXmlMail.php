<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FacturaCustomerPdfXmlMail extends Mailable 
{
    use Queueable, SerializesModels;

    public $subject = "Factura Electrónica ";

    protected $pdfPath;
    protected $xmlPath;
    protected $numero_factura;

    /**
     * Recibe las rutas de los archivos
     */
    public function __construct($pdfPath, $xmlPath, $numero_factura)
    {
        $this->pdfPath = $pdfPath;
        $this->xmlPath = $xmlPath;
        $this->numero_factura = $numero_factura;
    }

    public function build()
    {
        $name_pdf = 'factura_'.$this->numero_factura.'.pdf';
        $name_xml = 'factura_'.$this->numero_factura.'.xml';

        return $this->view('emails.factura') // tu vista de correo
            ->subject($this->subject.' '.$this->numero_factura)
            ->attach($this->pdfPath, [
                'as' => $name_pdf,
                'mime' => 'application/pdf',
            ])
            ->attach($this->xmlPath, [
                'as' => $name_xml,
                'mime' => 'application/xml',
            ]);
    }
    
}
