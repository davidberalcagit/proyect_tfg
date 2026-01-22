<!DOCTYPE html>
<html>
<head>
    <title>Alquiler Procesado</title>
</head>
<body>
    <h1>¡Alquiler Confirmado y Pagado!</h1>
    <p>El alquiler del vehículo <strong>{{ $rental->car->title }}</strong> se ha procesado correctamente.</p>

    <p><strong>Periodo:</strong> {{ $rental->fecha_inicio->format('d/m/Y') }} - {{ $rental->fecha_fin->format('d/m/Y') }}</p>
    <p><strong>Total Pagado:</strong> {{ number_format($rental->precio_total, 2) }} €</p>

    <p>Adjunto encontrarás el recibo.</p>

    <p>Gracias por confiar en nosotros.</p>
</body>
</html>
