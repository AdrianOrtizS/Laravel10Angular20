<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
  body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
    margin: 20px;
    color: #000;
  }

  .header {
    width: 100%;
  }

  .logo {
    width: 35%;
    float: left;
  }

  .empresa-info {
    width: 55%;
    float: right;
    border: 1px solid #000;
    padding: 20px;
    text-align: left;
  }

  .clear {
    clear: both;
  }

  .box {
    border: 1px solid #000;
    padding: 8px;
    margin-top: 10px;
  }

  .title {
    font-weight: bold;
    margin-bottom: 5px;
  }

  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
  }

  th, td {
    border: 1px solid #000;
    padding: 5px;
    font-size: 11px;
  }

  th {
    background: #eee;
  }

  .right {
    text-align: right;
  }

  .center {
    text-align: center;
  }

  .no-border td {
    border: none;
  }
</style>
</head>
<body>

<!-- HEADER -->
<div class="header">
  <div class="logo">
    <img src="{{ storage_path('app/public/' . $logoPdf) }}" height="80">
    <p>
      <strong>{{$nombreComercial}}</strong><br>
      <!-- <strong>Ruc:</strong> {{$ruc}}<br> -->
      <strong>Direccion:</strong> {{$dirMatriz}}<br>
      <strong>Correo:</strong> {{$correo}}<br>
      
      <strong>Obligado a llevar contabilidad:</strong> {{$obligadoContabilidad}}
    </p>
  </div>

  <div class="empresa-info">
    <strong>Ruc:</strong> {{$ruc}}<br>
    <strong>Factura No:</strong> {{ $sale->numero_factura }}<br>
    <strong>Numero de autorizacion</strong> {{ $sale->clave_acceso }}<br>

    <div style="display:flex; justify-content:space-between; gap:20px;">
        @if($sale->created_at)
        <div>
            <strong>Fecha de creación</strong> {{ \Carbon\Carbon::parse($sale->created_at)->format('d/m/Y H:i:s') }}
        </div>
        @endif
        @if($sale->fecha_autorizacion_sri)
        <div>
            <strong>Fecha de autorización</strong> {{ \Carbon\Carbon::parse($sale->fecha_autorizacion_sri)->format('d/m/Y H:i:s') }}
        </div>
        @endif
    </div><br>

    <strong>Ambiente:</strong> {{$ambiente == '1' ? 'Pruebas' : 'Normal'}}<br>
    <strong>Emision:</strong> Normal
  </div>

  <div class="clear"></div>
</div>

<!-- CLIENTE -->
<div class="box">
  <table class="no-border">
    <tr>
      <td><strong>Razon social:</strong> {{ $sale->customer->name }}</td>
      <td><strong>Ruc/CI:</strong> {{ $sale->customer->num_identificador }}</td>
    </tr>
    <tr>
      <td><strong>Fecha emision:</strong> {{ $sale->created_at->format('d/m/Y') }}</td>
      <td><strong>Telefono:</strong> {{ $sale->customer->phone }}</td>
    </tr>
    <tr>
      <td colspan="2"><strong>Direccion:</strong> {{ $sale->customer->address }}</td>
    </tr>
  </table>
</div>

<!-- DETALLE -->
<table>
  <thead>
    <tr>
      <th>Codigo</th>
      <th>Descripcion</th>
      <th class="center">Cantidad</th>
      <th class="right">P. Unitario</th>
      <th class="right">Descuento</th>
      <th class="right">Ice</th>
      <th class="right">Iva</th>
      <th class="right">Total</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($sale->details as $p)
    <tr>
      <td>{{ $p->product->cod_pro ?? '-' }}</td>
      <td>{{ $p->product->name }}</td>
      <td class="center">{{ $p->quantity }}</td>
      <td class="right">{{ number_format($p->price, 2) }}</td>
      <td class="right">{{ number_format($p->discount, 2) }}</td>
      <td class="right">{{ number_format($p->ice, 2) }}</td>
      <td class="right">{{ number_format($p->iva, 2) }}</td>
      <td class="right">{{ number_format($p->subtotal, 2) }}</td>
    </tr>
    @endforeach
  </tbody>
</table>
<!-- TOTALES -->
<table style="margin-top:15px;">
  <tr>
    <td style="width:70%"></td>
    <td>
      <table>
        <tr>
          <td>Subtotal 0%</td>
          <td class="right">{{ number_format($sale->subtotal, 2) }}</td>
        </tr>
        <tr>
          <td>Descuento</td>
          <td class="right">{{ number_format($sale->discount, 2) }}</td>
        </tr>
        <tr>
          <td>Ice</td>
          <td class="right">{{ number_format($sale->ice, 2) }}</td>
        </tr>
        <tr>
          <td>Iva 0%</td>
          <td class="right">{{ number_format($sale->iva0, 2) }}</td>
        </tr>        <tr>
          <td>Iva {{$iva}}%</td>
          <td class="right">{{ number_format($sale->iva, 2) }}</td>
        </tr>
        <tr>
          <td><strong>Total</strong></td>
          <td class="right"><strong>{{ number_format($sale->total, 2) }}</strong></td>
        </tr>
      </table>
    </td>
  </tr>
</table>

<!-- FORMA DE PAGO -->
<div class="box">
  <table>
    <tr>
      <th>Forma de Pago</th>
      <th>Valor</th>
    </tr>
    <tr>
      <td>
        @php
        $formasPago = [
          '01' => 'SIN UTILIZACIÓN DEL SISTEMA FINANCIERO',
          '15' => 'COMPENSACIÓN DE DEUDAS',
          '16' => 'TARJETA DE DÉBITO',
          '17' => 'DINERO ELECTRÓNICO',
          '18' => 'TARJETA PREPAGO',
          '19' => 'TARJETA DE CRÉDITO',
          '20' => 'OTROS CON UTILIZACIÓN DEL SISTEMA FINANCIERO',
          '21' => 'ENDOSO DE TÍTULOS',
        ];
        @endphp

        {{ $formasPago[$sale->form_pay] ?? 'NO DEFINIDO' }}
      </td>
      <td class="right">{{ number_format($sale->total, 2) }}</td>
    </tr>
  </table>
</div>

<!-- FOOTER -->
<!-- FOOTER -->
<div style="margin-top:20px; text-align:center;">
    <strong>Clave de acceso:</strong><br>
    <div style="margin:5px 0;">
        <img src="data:image/png;base64,{{ $barcode }}" width="350" style="display:block; margin:0 auto;">
    </div>
    <div style="font-size:12px; letter-spacing:1px;">
        {{ $sale->clave_acceso }}
    </div>
</div>

</body>
</html>