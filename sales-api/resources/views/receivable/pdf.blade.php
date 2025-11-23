<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Recibo de Pago - Parabrisas Libertadores </title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    /* Estilos básicos y amigables para impresión */
    :root{
      --accent:#0d6efd;
      --muted:#6c757d;
      --border:#e9ecef;
      --paper-width:800px;
    }
    body{
      font-family: Arial, Helvetica, sans-serif;
      color:#212529;
      margin:0;
      padding:20px;
      background:#f6f7fb;
    }
    .receipt {
      max-width: var(--paper-width);
      margin: 0 auto;
      background:#fff;
      padding:20px;
      border-radius:6px;
      box-shadow:0 2px 8px rgba(0,0,0,0.06);
      border:1px solid var(--border);
    }
    .header{
      display:flex;
      align-items:center;
      justify-content:space-between;
      gap:16px;
      margin-bottom:18px;
    }
    .brand {
      display:flex;
      gap:12px;
      align-items:center;
    }
    .brand img{ height:56px; object-fit:contain; border-radius:4px; }
    .company-info { font-size:14px; line-height:1.2; color:#222; }
    .company-info .small { color:var(--muted); font-size:13px; }

    h1.title { font-size:20px; margin:0; color:var(--accent); }
    .meta { text-align:right; font-size:13px; color:var(--muted); }

    .grid {
      display:flex;
      gap:16px;
      margin-bottom:12px;
      flex-wrap:wrap;
    }
    .box {
      flex:1 1 240px;
      border:1px solid var(--border);
      padding:12px;
      border-radius:6px;
      background:#fafbfc;
      min-width:220px;
    }
    .box h4 { margin:0 0 6px 0; font-size:13px; color:#333; }
    .box p { margin:0; font-size:14px; }

    table {
      width:100%;
      border-collapse:collapse;
      margin-top:12px;
      margin-bottom:8px;
    }
    table thead th {
      text-align:left;
      padding:8px 10px;
      background:#f1f3f5;
      font-size:13px;
      border-bottom:1px solid var(--border);
    }
    table tbody td {
      padding:10px;
      font-size:14px;
      border-bottom:1px dashed var(--border);
    }

    .totals {
      display:flex;
      justify-content:flex-end;
      margin-top:12px;
      gap:12px;
    }
    .totals .line {
      min-width:260px;
      border-radius:6px;
      padding:10px;
      border:1px solid var(--border);
      background:#fff;
      font-size:14px;
    }
    .totals .line .row { display:flex; justify-content:space-between; padding:6px 0; }
    .amount { font-weight:700; color:#111; }

    .notes { margin-top:14px; font-size:13px; color:var(--muted); }

    .signature {
      display:flex;
      justify-content:space-between;
      gap:16px;
      margin-top:26px;
      align-items:flex-end;
    }
    .signature .sig-box {
      width:45%;
      border-top:1px solid var(--border);
      text-align:center;
      padding-top:8px;
      font-size:13px;
      color:var(--muted);
    }

    .actions { display:flex; gap:8px; justify-content:flex-end; margin-bottom:12px; }
    .btn {
      display:inline-block;
      padding:8px 12px;
      border-radius:6px;
      background:var(--accent);
      color:#fff;
      text-decoration:none;
      font-size:14px;
    }
    .btn.secondary { background:#6c757d; }

    /* Print */
    @media print {
      body { background: #fff; padding:0; }
      .actions { display:none; }
      .receipt { box-shadow:none; border:none; margin:0; padding:0; }
    }
  </style>
</head>
<body>
  <div class="receipt">
    <div class="header">
      <div class="brand">
        <div class="company-info">
          <div >Parabrisal Libertadores</div>
          <div class="small">Av. Los libertadores oe4-131 y pasaje Viracocha</div>
          <div class="small">Tel: 2659205 &nbsp; | &nbsp; parabrisaslibertadores@hotmail.com</div>
        </div>
      </div>

      <div class="meta">
        <h1 class="title">RECIBO DE PAGO</h1>
        <div># Recibo: <strong>{{$receivable->num_comprobante_abono}}</strong></div>
        <div>Fecha: {{$receivable->created_at}}</div>
      </div>
    </div>

    <div class="grid">
      <div class="box">
        <h4>Cliente</h4>
         <p><strong>{{$receivable->sale->customer->name}}</strong></p>
        <p>{{$receivable->sale->customer->num_identificador}}</p>
        <p  class="small">{{$receivable->sale->customer->address}}</p>
      </div>

      <div class="box">
        <h4>Factura</h4>
        <p>N°: <strong>{{$receivable->sale->numero_factura}}</strong></p>
        <p>Fecha factura: {{$receivable->sale->created_at}}</p>
        <p>Total factura: <strong>{{$receivable->sale->total}}</strong></p>
      </div>

      <div class="box">
        <h4>Detalle del pago</h4>
        <p>Monto pagado: <strong>{{$receivable->valor_abono}}</strong></p>
        <p>Total abonado: <strong>{{$valor_abono_total}}</strong></p>
        <p>Saldo: <strong>{{$receivable->sale->total - $valor_abono_total}}</strong></p>
        <!-- <p>Método: efectivo</p> -->
      </div>
    </div>

    <div class="notes" style="margin-top:14px;">
      <strong>Observaciones:</strong>
      <p>{{$receivable->observacion}}</p>
    </div>

    <div class="signature">
      <div class="sig-box">
        Recibido por:<br>
        <strong>{{ $user->name }}</strong>
      </div>

      <!-- <div class="sig-box">
        Firma cliente:<br>
        <span class="small">__________________________</span>
      </div> -->
    </div>

    <p style="text-align:center; font-size:12px; color:var(--muted); margin-top:12px;">
      Este recibo sirve como comprobante de pago. Emisión: {{$receivable->created_at}}
    </p>

  </div>
</body>
</html>
