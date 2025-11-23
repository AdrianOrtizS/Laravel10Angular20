<!DOCTYPE html>
<html>
<head>
    <title>Verifica tu dirección de correo electrónico</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3490dc;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <h2>Verifica tu dirección de correo electrónico</h2>
    
    <p>Hola {{ $user->name }},</p>
    
    <p>Gracias por registrarte en nuestro servicio. Por favor, haz clic en el botón siguiente para verificar tu dirección de correo electrónico.</p>
        
    <!-- {{env("URL_FRONT")."#/login?code=".$user->uniqid}} -->
    <a href="{{env("URL_FRONT")."#/login?code=".$user->uniqid}}" target="_blank" class="link c-white" 
             style="
                text-decoration: none;
                padding: 10px 20px;
                border-radius: 8px;
                font-family: Monospace;
                text-align: center;
                color: white;
                background-color: lightblue; 
                ">
        
        <span class="link c-white" style="text-decoration:none; color:black;">
            Da click aqui e ingresa al sistema
        </span>
    </a>

    <p>Si no creaste una cuenta, no es necesario realizar ninguna acción.</p>
    
    <div class="footer">
        <p>Saludos,<br>El equipo de {{ config('app.name') }}</p>
        <p>Si tienes problemas al hacer clic en el botón "Verificar Correo Electrónico", copia y pega la siguiente URL en tu navegador web:<br>
        </p>
        {{env("URL_FRONT")."#/login?code=".$user->uniqid}}
        <!-- {{env("URL_FRONT")."login?code=".$user->uniqid}} -->
<!--         <a href="{{env("URL_FRONT")."#/login?code=".$user->uniqid}}" target="_blank" class="link c-white" style="display: block; padding: 15px 35px; text-decoration:none; color:black;">
            <span class="link c-white" style="text-decoration:none; color:black;">
                Verify Now
            </span>
        </a> -->
    </div>
</body>
</html>