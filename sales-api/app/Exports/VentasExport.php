<?php

namespace App\Exports;


use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class VentasExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithEvents
{                             
    private $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    public function query()
    {
        return $this->query;
    }


    public function map($sale): array
    {
        $formasPago = [
            '01' => 'Sin utilización del sistema financiero',
            '15' => 'Compensación de deudas',
            '16' => 'Tarjeta de débito',
            '17' => 'Dinero electrónico',
            '18' => 'Tarjeta prepago',
            '19' => 'Tarjeta de crédito',
            '20' => 'Otros con utilización del sistema financiero',
            '21' => 'Endoso de títulos',
        ];

        $ambiente = [
            '1' => 'Prueba',
            '2' => 'Produccion'
        ];

        return [
            $sale->created_at,
            optional($sale->customer)->name,
            $sale->numero_factura,
            $sale->iva,
            $sale->iva0,
            $sale->ice,
            $sale->subtotal,
            $sale->discount,
            $sale->total,
            $sale->clave_acceso,
            $sale->estado_sri,
            $sale->numero_autorizacion,
            $sale->fecha_autorizacion_sri,
            $sale->error_no_autorizada,
            $sale->establecimiento,
            $sale->punto_emision,
            $sale->secuencial,
            $ambiente[$sale->ambiente] ?? $sale->ambiente,
            $formasPago[$sale->form_pay] ?? $sale->form_pay,
            $sale->plazo,
            $sale->unidadTiempo,
        ];
    }
    public function headings(): array
    {
        return [
            'Fecha creacion',
            'Cliente',
            '# Factura',
            'Iva',
            'Iva 0',
            'Ice',
            'Subtotal',
            'Descuento',
            'Total',
            'Clave Acceso',
            'Estado Sri',
            'Numero Autorizacion',
            'Fecha Autorizacion_sri',
            'Error No Autorizada',
            'Establecimiento',
            'Punto Emision',
            'Secuencial',
            'Ambiente',
            'Forma de pago',
            'Plazo',
            'Unidad de tiempo',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Estilo para la fila de encabezados
        $sheet->getStyle('A1:U1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => [
                    'rgb' => 'FFFFFF',
                ],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '4472C4',
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ]);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->freezePane('A2');
            },
        ];
    }
}