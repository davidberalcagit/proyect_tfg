<!DOCTYPE html>
<html>
<head>
    <title>Sale Receipt</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .details { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .details th, .details td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .details th { background-color: #f2f2f2; }
        .total { margin-top: 20px; text-align: right; font-size: 1.2em; font-weight: bold; }
        .car-image { text-align: center; margin-bottom: 20px; }
        .car-image img { max-width: 300px; max-height: 200px; border: 1px solid #ddd; padding: 5px; }
        .footer { margin-top: 50px; text-align: center; font-size: 0.8em; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sale Receipt</h1>
        <p>Date: {{ now()->format('d/m/Y') }}</p>
        <p>Receipt ID: #{{ str_pad($offer->id, 6, '0', STR_PAD_LEFT) }}</p>
    </div>

    @if($offer->car->image)
        <div class="car-image">
            {{-- Use public_path for PDF generation --}}
            <img src="{{ public_path('storage/' . $offer->car->image) }}" alt="Car Image">
        </div>
    @endif

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
            <th>Kilometers</th>
            <td>{{ number_format($offer->car->km, 0, ',', '.') }} km</td>
        </tr>
        <tr>
            <th>Fuel Type</th>
            <td>{{ $offer->car->combustible->nombre }}</td>
        </tr>
        <tr>
            <th>Gearbox</th>
            <td>{{ $offer->car->marcha->tipo }}</td>
        </tr>
        <tr>
            <th>Color</th>
            <td>{{ $offer->car->color->nombre }}</td>
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
        <tr>
            <th>Payment Method</th>
            <td>Bank Transfer (Pending)</td>
        </tr>
    </table>

    <div class="total">
        Total Price: {{ number_format($offer->cantidad, 2, ',', '.') }}€
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>{{ config('app.name') }} - Official Receipt</p>
    </div>
</body>
</html>
