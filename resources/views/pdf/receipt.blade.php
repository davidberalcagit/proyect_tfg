<!DOCTYPE html>
<html>
<head>
    <title>Sale Receipt</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .details { width: 100%; border-collapse: collapse; }
        .details th, .details td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .details th { background-color: #f2f2f2; }
        .total { margin-top: 20px; text-align: right; font-size: 1.2em; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sale Receipt</h1>
        <p>Date: {{ now()->format('d/m/Y') }}</p>
    </div>

    <h3>Car Details</h3>
    <table class="details">
        <tr>
            <th>Brand</th>
            <td>{{ $offer->car->marca->nombre }}</td>
        </tr>
        <tr>
            <th>Model</th>
            <td>{{ $offer->car->modelo->nombre }}</td>
        </tr>
        <tr>
            <th>Year</th>
            <td>{{ $offer->car->anyo_matri }}</td>
        </tr>
        <tr>
            <th>License Plate</th>
            <td>{{ $offer->car->matricula }}</td>
        </tr>
    </table>

    <h3>Transaction Details</h3>
    <table class="details">
        <tr>
            <th>Seller</th>
            <td>{{ $offer->seller->nombre_contacto }}</td>
        </tr>
        <tr>
            <th>Buyer</th>
            <td>{{ $offer->buyer->nombre_contacto }}</td>
        </tr>
    </table>

    <div class="total">
        Total Price: {{ $offer->cantidad }}€
    </div>
</body>
</html>
