<!DOCTYPE html>
<html>
<head>
    <title>Venta Procesada</title>
</head>
<body>
    <h1>¡Venta Procesada!</h1>
    <p>La venta del vehículo <strong>{{ $sale->vehiculo->title }}</strong> se ha completado correctamente.</p>

    <p><strong>Detalles:</strong></p>
    <ul>
        <li>Precio Final: {{ number_format($sale->precio, 2) }} €</li>
        <li>Fecha: {{ $sale->created_at->format('d/m/Y') }}</li>
    </ul>

    <p>Gracias por confiar en nosotros.</p>
</body>
</html>
