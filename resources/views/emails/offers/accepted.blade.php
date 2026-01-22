<!DOCTYPE html>
<html>
<head>
    <title>Oferta Aceptada</title>
</head>
<body>
    <h1>¡Buenas noticias!</h1>
    <p>El vendedor ha aceptado tu oferta por el vehículo <strong>{{ $offer->car->title }}</strong>.</p>

    <p><strong>Cantidad Acordada:</strong> {{ number_format($offer->cantidad, 2) }} €</p>

    <p>Para completar la compra y asegurar el vehículo, por favor realiza el pago lo antes posible.</p>

    <p>
        <a href="{{ route('sales.index') }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            Ir a Pagar
        </a>
    </p>

    <p>Si no realizas el pago, el vehículo seguirá disponible para otros compradores.</p>

    <p>Gracias,<br>
    {{ config('app.name') }}</p>
</body>
</html>
