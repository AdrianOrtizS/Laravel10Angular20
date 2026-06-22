<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
<style>
  body {
    font-family: DejaVu Sans, sans-serif;
    font-size: 12px;
    margin: 20px;
    color: #000;
  }

  /* 🔥 MARCA DE AGUA REAL PARA DOMPDF */
  .watermark {
    position: fixed;
    top: 35%;
    left: 5%;
    width: 100%;
    text-align: center;
    font-size: 140px;
    color: #bbbbbb;
    opacity: 0.15;
    transform: rotate(-30deg);
    z-index: -1000;
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
      <strong>{{$nombreComercial}}</strong><br>
      RUC: {{$ruc}}<br>
      DIRECCION: {{$dirMatriz}}<br>
      OBLIGADO A LLEVAR CONTABILIDAD: {{$obligadoContabilidad}}
    </p>
  </div>

  <div class="empresa-info">
    <strong>RUC: {{$ruc}}</strong><br><br>
    <strong>FACTURA</strong><br>
    No: {{ $sale->numero_factura }}<br><br>
    <strong>NÚMERO DE AUTORIZACIÓN</strong><br>
    {{ $sale->clave_acceso }}<br><br>
    <strong>AMBIENTE:</strong> {{$ambiente == '1' ? 'PRUEBAS' : 'NORMAL'}}<br>
    <strong>EMISIÓN:</strong> NORMAL
  </div>

  <div class="clear"></div>
</div>


<h1>COPIA</h1>

<!-- CLIENTE -->
<div class="box">
  <table class="no-border">
    <tr>
      <td><strong>RAZON SOCIAL:</strong> {{ $sale->customer->name }}</td>
      <td><strong>RUC/CI:</strong> {{ $sale->customer->num_identificador }}</td>
    </tr>
    <tr>
      <td><strong>FECHA EMISION:</strong> {{ $sale->created_at->format('d/m/Y') }}</td>
      <td><strong>TELEFONO:</strong> {{ $sale->customer->phone }}</td>
    </tr>
    <tr>
      <td colspan="2"><strong>DIRECCION:</strong> {{ $sale->customer->address }}</td>
    </tr>
  </table>
</div>

<!-- DETALLE -->
<table>
  <thead>
    <tr>
      <th>CODIGO</th>
      <th>DESCRIPCION</th>
      <th class="center">CANTIDAD</th>
      <th class="right">PRECIO UNIT.</th>
      <th class="right">DESCUENTO</th>
      <th class="right">TOTAL</th>
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
          <td>SUBTOTAL 0%</td>
          <td class="right">{{ number_format($sale->subtotal, 2) }}</td>
        </tr>
        <tr>
          <td>DESCUENTO</td>
          <td class="right">{{ number_format($sale->discount, 2) }}</td>
        </tr>
        <tr>
          <td>IVA 0%</td>
          <td class="right">{{ number_format($sale->iva0, 2) }}</td>
        </tr>
        <tr>
          <td>IVA {{$iva}}%</td>
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
<div style="margin-top:20px;">
  <strong>CLAVE DE ACCESO:</strong><br>
  <!-- {{ $sale->clave_acceso }} -->
</div>
<div>
  <h2>COPIA</h2>
</div>

</body>
</html>