<!DOCTYPE html>
<html>
<head>
    <title>Bienvenido</title>
</head>
<body>
    <h1>¡Hola {{ $user->name }}!</h1>
    <p>Gracias por registrarte en nuestra plataforma de compra, venta y alquiler de vehículos.</p>

    <p>Ahora puedes:</p>
    <ul>
        <li>Publicar tus coches para vender o alquilar.</li>
        <li>Buscar y comprar vehículos.</li>
        <li>Gestionar tus transacciones fácilmente.</li>
    </ul>

    <p>
        <a href="{{ route('dashboard') }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            Ir al Panel de Control
        </a>
    </p>

    <p>¡Esperamos que disfrutes de la experiencia!</p>
</body>
</html>
