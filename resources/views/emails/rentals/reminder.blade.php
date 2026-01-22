<!DOCTYPE html>
<html>
<head>
    <title>Recordatorio de Devolución</title>
</head>
<body>
    <h1>¡Hoy es el último día!</h1>
    <p>Hola {{ $rental->customer->nombre_contacto }},</p>

    <p>Te recordamos que el alquiler del vehículo <strong>{{ $rental->car->title }}</strong> finaliza <strong>HOY, {{ $rental->fecha_fin->format('d/m/Y') }}</strong>.</p>

    <p>Por favor, asegúrate de devolver el vehículo antes de que finalice el día para evitar cargos adicionales por retraso.</p>

    <p>Si ya has devuelto el vehículo, por favor ignora este mensaje.</p>

    <p>Gracias por confiar en nosotros.</p>
</body>
</html>
