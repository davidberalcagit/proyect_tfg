<!DOCTYPE html>
<html>
<head>
    <title>Oferta Rechazada</title>
</head>
<body>
    <h1>Lo sentimos</h1>
    <p>El vendedor ha rechazado tu oferta por el vehículo <strong>{{ $offer->car->title }}</strong>.</p>

    <p><strong>Tu oferta:</strong> {{ number_format($offer->cantidad, 2) }} €</p>

    <p>Puedes intentar hacer una nueva oferta si el coche sigue disponible.</p>

    <p>
        <a href="{{ route('cars.show', $offer->car) }}">Ver Coche</a>
    </p>
</body>
</html>
