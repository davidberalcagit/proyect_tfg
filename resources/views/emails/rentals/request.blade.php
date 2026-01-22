<!DOCTYPE html>
<html>
<head>
    <title>Nueva Solicitud de Alquiler</title>
</head>
<body>
    <h1>¡Tienes una nueva solicitud de alquiler!</h1>
    <p>El usuario <strong>{{ $rental->customer->nombre_contacto }}</strong> quiere alquilar tu coche <strong>{{ $rental->car->title }}</strong>.</p>

    <p><strong>Detalles:</strong></p>
    <ul>
        <li>Fecha Inicio: {{ $rental->fecha_inicio->format('d/m/Y') }}</li>
        <li>Fecha Fin: {{ $rental->fecha_fin->format('d/m/Y') }}</li>
        <li>Precio Total: {{ number_format($rental->precio_total, 2) }} €</li>
    </ul>

    <p>Por favor, revisa la solicitud en tu panel de transacciones.</p>

    <p>
        <a href="{{ route('sales.index') }}">Ir a Mis Transacciones</a>
    </p>
</body>
</html>
