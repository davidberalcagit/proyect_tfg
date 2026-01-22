<!DOCTYPE html>
<html>
<head>
    <title>{{ __('Approval Certificate') }}</title>
    <style>
        body { font-family: sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .content { margin: 20px; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('Approval Certificate') }}</h1>
        <p>{{ __('Vehicle Platform') }}</p>
    </div>

    <div class="content">
        <p>{{ __('This document certifies that the vehicle:') }}</p>

        <h3>{{ $car->title }}</h3>
        <p><strong>{{ __('Matricula') }}:</strong> {{ $car->matricula }}</p>
        <p><strong>{{ __('Price') }}:</strong> {{ number_format($car->precio, 2) }} €</p>

        <p>{{ __('Has been reviewed and approved by our supervision team on') }} {{ now()->format('d/m/Y') }}.</p>

        <p>{{ __('The vehicle is now available for') }} {{ $car->listingType->nombre ?? 'Venta/Alquiler' }}.</p>
    </div>

    <div class="footer">
        <p>{{ __('This is an automatically generated document.') }}</p>
    </div>
</body>
</html>
