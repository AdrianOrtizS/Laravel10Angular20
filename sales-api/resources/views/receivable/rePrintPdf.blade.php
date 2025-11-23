<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Recibo de Pago - Parabrisas Libertadores</title>
  <style>
    @page { margin: 40px; }

    body {
      font-family: Arial, Helvetica, sans-serif;
      color: #212529;
      margin: 0;
      position: relative;
    }

    /* ✅ Marca de agua encima del contenido */
    .watermark {
      position: fixed;
      top: 35%;
      left: 50%;
      transform: translate(-50%, -50%) rotate(-30deg);
      font-size: 150px;
      color: rgba(0, 0, 0, 0.08);
      font-weight: bold;
      text-transform: uppercase;
      z-index: 9999; /* 🔼 Encima de todo */
      pointer-events: none;
      white-space: nowrap;
    }

    .receipt {
      position: relative;
      background: #fff;
      padding: 20px;
      border: 1px solid #e9ecef;
      border-radius: 6px;
      z-index: 1;
    }

    .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: 18px;
    }
    .company-info { font-size: 14px; line-height: 1.2; color: #222; }
    .company-info .small { color: #6c757d; font-size: 13px; }

    h1.title { font-size: 20px; margin: 0; color: #0d6efd; }
    .meta { text-align: right; font-size: 13px; color: #6c757d; }

    .grid {
      display: flex;
      gap: 16px;
      margin-bottom: 12px;
      flex-wrap: wrap;
    }
    .box {
      flex: 1 1 240px;
      border: 1px solid #e9ecef;
      padding: 12px;
      border-radius: 6px;
      background: #fafbfc;
      min-width: 220px;
    }
    .box h4 { margin: 0 0 6px 0; font-size: 13px; color: #333; }
    .box p { margin: 0; font-size: 14px; }

    .signature {
      display: flex;
      justify-content: space-between;
      margin-top: 30px;
    }
    .sig-box {
      width: 45%;
      border-top: 1px solid #ccc;
      text-align: center;
      padding-top: 8px;
      font-size: 13px;
      color: #6c757d;
    }

    .notes { margin-top: 14px; font-size: 13px; color: #6c757d; }

    @media print {
      body { background: #fff; }
    }
  </style>
</head>
<body>

  <!-- 💧 Marca de agua al frente -->
  <div class="watermark">COPIA</div>

  <div class="receipt">
    <div class="header">
      <div class="company-info">
        <div><strong>Parabrisas Libertadores</strong></div>
        <div class="small">Av. Los Libertadores OE4-131 y Pasaje Viracocha</div>
        <div class="small">Tel: 2659205 | parabrisaslibertadores@hotmail.com</div>
      </div>

      <div class="meta">
        <h1 class="title">RECIBO DE PAGO</h1>
        <div># Recibo: <strong>{{ $receivable->num_comprobante_abono }}</strong></div>
        <div>Fecha: {{ \Carbon\Carbon::parse($receivable->created_at)->format('d/m/Y H:i') }}</div>
      </div>
    </div>

    <div class="grid">
      <div class="box">
        <h4>Cliente</h4>
        <p><strong>{{ $receivable->sale->customer->name }}</strong></p>
        <p>{{ $receivable->sale->customer->num_identificador }}</p>
        <p class="small">{{ $receivable->sale->customer->address }}</p>
      </div>

      <div class="box">
        <h4>Factura</h4>
        <p>N°: <strong>{{ $receivable->sale->numero_factura }}</strong></p>
        <p>Fecha factura: {{ \Carbon\Carbon::parse($receivable->sale->created_at)->format('d/m/Y') }}</p>
        <p>Total factura: <strong>${{ number_format($receivable->sale->total, 2) }}</strong></p>
      </div>

      <div class="box">
        <h4>Detalle del pago</h4>
        <p>Monto pagado: <strong>${{ number_format($receivable->valor_abono, 2) }}</strong></p>
        <p>Total abonado: <strong>${{ number_format($valor_abono_total, 2) }}</strong></p>
        <p>Saldo: <strong>${{ number_format($receivable->sale->total - $valor_abono_total, 2) }}</strong></p>
      </div>
    </div>

    <div class="notes">
      <strong>Observaciones:</strong>
      <p>{{ $receivable->observacion }}</p>
    </div>

    <div class="signature">
      <div class="sig-box">
        Recibido por:<br>
        <strong>{{ $user->name }}</strong>
      </div>
    </div>

    <p style="text-align:center; font-size:12px; color:#6c757d; margin-top:12px;">
      Este recibo sirve como comprobante de pago. Emisión: {{ \Carbon\Carbon::parse($receivable->created_at)->format('d/m/Y H:i') }}
    </p>
  </div>
</body>
</html>
