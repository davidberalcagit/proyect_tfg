<!DOCTYPE html>
<html>
<head>
    <title>Coche Rechazado</title>
</head>
<body>
    <h1>Lo sentimos</h1>
    <p>Tu coche <strong>{{ $car->title }}</strong> no ha pasado el proceso de revisión.</p>

    <p><strong>Razón del rechazo:</strong></p>
    <blockquote>
        {{ $reason }}
    </blockquote>

    <p>Puedes editar tu coche para corregir los problemas y volver a enviarlo a revisión, o contactar con soporte si tienes dudas.</p>

    <p>
        <a href="{{ route('cars.edit', $car) }}">Editar coche</a>
    </p>

    <p>Atentamente,<br>El equipo de supervisión.</p>
</body>
</html>
