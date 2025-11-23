<!DOCTYPE html>
<html>
<head>
    <title>Recuperacion de clave de acceso</title>
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
    
    <p>Hola {{ $user->name }},</p>

    
    <p>Gracias por comunicarte con nuestros servicios. Por favor, haz clic en el botón siguiente para poder recuperar tu contraseña.</p>
        
    <!-- {{env("URL_FRONT")."update_password?code=".$user->code_verified}} -->
    <a href="{{env("URL_FRONT")."#/update_password?code=".$user->code_verified}}" target="_blank" class="link c-white" style="display: block; padding: 15px 35px; text-decoration:none; color:#ffffff;">
        <span class="link c-white" style="text-decoration:none; color:#0b57d0;">
            Click aqui para actualizar contraseña.
        </span>
    </a>

    <!-- <p>Si no creaste una cuenta, no es necesario realizar ninguna acción.</p> -->
    
    
</body>
</html>