<!DOCTYPE html>
<html>
<head>
    <title>Coche Aprobado</title>
</head>
<body>
    <h1>¡Felicidades!</h1>
    <p>Tu coche <strong>{{ $car->title }}</strong> ha sido aprobado por nuestro equipo de supervisión.</p>
    <p>Ya está visible para todos los usuarios en nuestra plataforma.</p>

    <p>
        <a href="{{ route('cars.show', $car) }}">Ver mi coche</a>
    </p>

    <p>Gracias por confiar en nosotros.</p>
</body>
</html>
