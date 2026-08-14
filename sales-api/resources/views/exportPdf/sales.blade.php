<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">

    <style>
        body{
            font-family: DejaVu Sans;
            font-size:11px;
        }

        table{
            width:100%;
            border-collapse: collapse;
        }

        th{
            background:#4472C4;
            color:white;
            padding:5px;
            border:1px solid #000;
        }

        td{
            padding:4px;
            border:1px solid #ccc;
        }

        h2{
            text-align:center;
        }
    </style>

</head>

<body>

<h2>Reporte de Ventas</h2>

<table>

<thead>
<tr>
    <th>Fecha</th>
    <th>Cliente</th>
    <th>Factura</th>
    <th>Total</th>
    <th>Estado SRI</th>
    <th>Forma pago</th>
    <th>Plazo</th>
    <th>Tiempo</th>
</tr>
</thead>

<tbody>

@foreach($sales as $venta)

<tr>
    <td>{{ $venta->created_at }}</td>
    <td>{{ optional($venta->customer)->name }}</td>
    <td>{{ $venta->numero_factura }}</td>
    <td>${{ number_format($venta->total,2) }}</td>
    <td>{{ $venta->estado_sri }}</td>
    <td>{{ match($venta->form_pay) {
                '01' => 'Sin utilizacion del sistema financiero',
                '15' => 'Compensación de deudas',
                '16' => 'Tarjeta de débito',
                '17' => 'Dinero electrónico',
                '18' => 'Tarjeta prepago',
                '19' => 'Tarjeta de crédito',
                '20' => 'Otros con utilizacion del sistema financiero',
                '21' => 'Endoso de títulos',
                default => $venta->form_pay,
            }
        }}</td>
    <td>{{ $venta->plazo }}</td>
    <td>{{ $venta->unidadTiempo }}</td>
</tr>

@endforeach

</tbody>

</table>

</body>

</html>