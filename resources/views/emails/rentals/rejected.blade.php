<!DOCTYPE html>
<html>
<head>
    <title>Alquiler Rechazado</title>
</head>
<body>
    <h1>Lo sentimos</h1>
    <p>El dueño ha rechazado tu solicitud de alquiler para el vehículo <strong>{{ $rental->car->title }}</strong>.</p>

    <p><strong>Detalles de la solicitud:</strong></p>
    <ul>
        <li>Fecha Inicio: {{ $rental->fecha_inicio->format('d/m/Y') }}</li>
        <li>Fecha Fin: {{ $rental->fecha_fin->format('d/m/Y') }}</li>
    </ul>

    <p>Puedes intentar buscar otro vehículo disponible en nuestra plataforma.</p>

    <p>
        <a href="{{ route('cars.index') }}">Ver Coches</a>
    </p>
</body>
</html>
