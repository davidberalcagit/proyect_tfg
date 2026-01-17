<!DOCTYPE html>
<html>
<head>
    <title>New Offer Received</title>
</head>
<body>
    <h1>You have received a new offer!</h1>
    <p>Hello {{ $offer->seller->nombre_contacto }},</p>
    <p>A buyer has made an offer for your car: <strong>{{ $offer->car->title }}</strong>.</p>

    <p><strong>Offer Amount:</strong> {{ $offer->cantidad }}€</p>
    <p><strong>Buyer:</strong> {{ $offer->buyer->nombre_contacto }}</p>

    <p>You can review, accept, or reject this offer in your dashboard:</p>
    <p>
        <a href="{{ route('offers.index') }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">
            View Received Offers
        </a>
    </p>

    <p>Thanks,<br>
    {{ config('app.name') }}</p>
</body>
</html>
