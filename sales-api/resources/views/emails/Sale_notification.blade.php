<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Cobro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #2c3e50;
        }
        .details {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }
        .detail-label {
            font-weight: bold;
            width: 200px;
        }
        .footer {
            margin-top: 30px;
            font-size: 14px;
            text-align: center;
            color: #7f8c8d;
        }
        .payment-methods {
            margin-top: 20px;
            padding: 15px;
            background-color: #f0f8ff;
            border-radius: 5px;
        }
        .due-date {
            color: #e74c3c;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <!-- Reemplaza con tu logo -->
        <img src="logo_empresa.png" alt="Logo" class="logo">
        <div class="title">NOTIFICACIÓN DE COBRO</div>
    </div>

    <div class="details">
        <div class="detail-row">
            <div class="detail-label">Número de factura:</div>
            <div>FAC-2023-00125</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Cliente:</div>
            <div>Juan Pérez García</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Fecha de emisión:</div>
            <div>15 de octubre de 2023</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Fecha de vencimiento:</div>
            <div class="due-date">30 de octubre de 2023</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Concepto:</div>
            <div>Servicios de consultoría - Octubre 2023</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Monto total:</div>
            <div style="font-size: 18px; font-weight: bold;">$1,250.00 USD</div>
        </div>
        <div class="detail-row">
            <div class="detail-label">Estado:</div>
            <div style="color: #e74c3c; font-weight: bold;">Pendiente de pago</div>
        </div>
    </div>

    <div>
        <p>Estimado cliente,</p>
        <p>Le recordamos que según nuestros registros, el pago de la factura mencionada se encuentra pendiente. 
        Agradeceríamos que realice el abono correspondiente a la brevedad posible.</p>
        
        <div class="payment-methods">
            <h3>Métodos de pago disponibles:</h3>
            <ul>
                <li>Transferencia bancaria (solicitar datos por este medio)</li>
                <li>Depósito en cuenta</li>
                <li>Pago con tarjeta (enlace seguro disponible)</li>
                <li>Efectivo en nuestras oficinas</li>
            </ul>
        </div>

        <p>Para cualquier consulta o aclaración sobre este cobro, no dude en contactarnos.</p>
        <p>Atentamente,</p>
        <p><strong>Departamento de Cobranzas</strong><br>
        Empresa XYZ S.A.<br>
        Teléfono: (555) 123-4567<br>
        Email: cobranzas@empresaxyz.com</p>
    </div>

    <div class="footer">
        <p>Este es un mensaje automático, por favor no responda a este correo.</p>
        <p>© 2023 Empresa XYZ S.A. Todos los derechos reservados.</p>
    </div>
</body>
</html>