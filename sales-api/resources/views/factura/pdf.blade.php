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
    width: 50%;
    float: left;
  }

  .empresa-info {
    width: 60%;
    float: right;
    border: 1px solid #000;
    padding: 20px;
    text-align: center;
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
    <img src="{{ public_path('logo.png') }}" height="80">
    <p>
      <strong>MI EMPRESA S.A..........</strong><br>
      RUC: 0999999999001<br>
      Dir: Av. Siempre Viva<br>
      Obligado a llevar contabilidad: SI
    </p>
  </div>

  <div class="empresa-info">
    <strong>RUC: 0999999999001</strong><br><br>

    <strong>FACTURA</strong><br>
    No: {{ $sale->numero_factura }}<br><br>

    <strong>NÚMERO DE AUTORIZACIÓN</strong><br>
    {{ $sale->clave_acceso }}<br><br>

    <strong>FECHA Y HORA DE AUTORIZACIÓN</strong><br>
    {{ $sale->fecha_autorizacion ?? '-' }}<br><br>

    <strong>AMBIENTE:</strong> PRODUCCIÓN<br>
    <strong>EMISIÓN:</strong> NORMAL
  </div>

  <div class="clear"></div>
</div>

<!-- CLIENTE -->
<div class="box">
  <table class="no-border">
    <tr>
      <td><strong>Razón Social:</strong> {{ $sale->customer->name }}</td>
      <td><strong>RUC/CI:</strong> {{ $sale->customer->num_identificador }}</td>
    </tr>
    <tr>
      <td><strong>Fecha Emisión:</strong> {{ $sale->created_at->format('d/m/Y') }}</td>
      <td><strong>Teléfono:</strong> {{ $sale->customer->phone }}</td>
    </tr>
    <tr>
      <td colspan="2"><strong>Dirección:</strong> {{ $sale->customer->address }}</td>
    </tr>
  </table>
</div>

<!-- DETALLE -->
<table>
  <thead>
    <tr>
      <th>Código</th>
      <th>Descripción</th>
      <th class="center">Cantidad</th>
      <th class="right">Precio Unit.</th>
      <th class="right">Descuento</th>
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
          <td>IVA 15%</td>
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
      <td>CONTADO</td>
      <td class="right">{{ number_format($sale->total, 2) }}</td>
    </tr>
  </table>
</div>

<!-- FOOTER -->
<div style="margin-top:20px;">
  <strong>CLAVE DE ACCESO:</strong><br>
  {{ $sale->clave_acceso }}
</div>

</body>
</html>