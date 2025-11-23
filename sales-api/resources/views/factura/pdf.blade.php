<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Factura #{{ $sale->id }}</title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
    .header { text-align: center; margin-bottom: 20px; }
    .header img { max-height: 60px; }
    .empresa { font-size: 14px; font-weight: bold; }
    .cliente, .totales { margin-top: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    table th, table td { border: 1px solid #ddd; padding: 6px; text-align: left; }
    table th { background: #f2f2f2; }
    .right { text-align: right; }
    .center { text-align: center; }
  </style>
</head>
<body>

  <!-- Encabezado -->
  <div class="header">
    <img src="{{ public_path('logo.png') }}" alt="Logo">
    <div class="empresa">Mi Empresa S.A.</div>
    <div>RUC: 0999999999001</div>
    <div>Dirección: Av. Siempre Viva 123, Quito - Ecuador</div>
    <p>
      <strong>Factura No:</strong> {{ $sale->numero_factura }}
    </p>
    <p>
      <strong>Clave de acceso:</strong> {{ $sale->clave_acceso }}
    </p>
  </div>

  <!-- Datos del Cliente -->
  <div class="cliente">
    <p><strong>Fecha:</strong> {{ $sale->created_at->format('d/m/Y') }}</p>
    <p><strong>Nombre:</strong> {{ $sale->customer->name }}</p>
    <p><strong>Cédula/RUC:</strong> {{ $sale->customer->num_identificador }}</p>
    <p><strong>Dirección:</strong> {{ $sale->customer->address }}</p>
    <p><strong>Telefono:</strong> {{ $sale->customer->phone }}</p>
  </div>

  <!-- Detalles de Productos -->
  <h4>Detalles</h4>
  <table>
    <thead>
      <tr>
        <th>Descripción</th>
        <th class="right">P. Unitario</th>
        <th class="center">Cantidad</th>
        <th class="right">Subtotal</th>
        <th class="right">Descuento</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($sale->details as $p)
      <tr>
        <td>{{ $p->product->name }}</td>
        <td class="right">{{ number_format($p->price, 2) }}</td>
        <td class="center">{{ $p->quantity }}</td>
        <td class="right">{{ number_format($p->subtotal, 2) }}</td>
        <td class="right">{{ number_format($p->discount, 2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <!-- Totales -->
  <div class="totales">
    <table>
      <tr>
        <td class="right"><strong>Subtotal:</strong></td>
        <td class="right">{{ number_format($sale->subtotal, 2) }}</td>
      </tr>
      
      <tr>
        <td class="right"><strong>Descuento:</strong></td>
        <td class="right">{{ number_format($sale->discount, 2) }}</td>
      </tr>

      <tr>
        <td class="right"><strong>Iva:</strong></td>
        <td class="right">{{ number_format($sale->iva, 2) }}</td>
      </tr>

      <tr>
        <td class="right"><strong>Total:</strong></td>
        <td class="right"><strong>{{ number_format($sale->total, 2) }}</strong></td>
      </tr>
    </table>
  </div>

  <p style="margin-top:40px; text-align:center;">¡Gracias por su compra!</p>

</body>
</html>
