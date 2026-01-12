<!DOCTYPE html>
<html>
<head>
    <title>Offer Accepted</title>
</head>
<body>
    <h1>Your offer has been accepted!</h1>
    <p>Hello {{ $offer->buyer->nombre_contacto }},</p>
    <p>Great news! The seller has accepted your offer for the car: <strong>{{ $offer->car->title }}</strong>.</p>

    <p><strong>Agreed Price:</strong> {{ $offer->cantidad }}€</p>

    <p>The sale has been processed successfully.</p>

    <p>Thanks,<br>
    {{ config('app.name') }}</p>
</body>
</html>
