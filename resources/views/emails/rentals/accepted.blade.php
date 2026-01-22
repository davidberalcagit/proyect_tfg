<!DOCTYPE html>
<html>
<head>
    <title>Alquiler Aceptado</title>
</head>
<body>
    <h1>¡Buenas noticias!</h1>
    <p>El dueño ha aceptado tu solicitud de alquiler para el vehículo <strong>{{ $rental->car->title }}</strong>.</p>

    <p><strong>Detalles:</strong></p>
    <ul>
        <li>Fecha Inicio: {{ $rental->fecha_inicio->format('d/m/Y') }}</li>
        <li>Fecha Fin: {{ $rental->fecha_fin->format('d/m/Y') }}</li>
        <li>Precio Total: {{ number_format($rental->precio_total, 2) }} €</li>
    </ul>

    <p>Para confirmar la reserva, por favor realiza el pago lo antes posible.</p>

    <p>
        <a href="{{ route('sales.index') }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            Ir a Pagar
        </a>
    </p>

    <p>Gracias por usar nuestra plataforma.</p>
</body>
</html>
