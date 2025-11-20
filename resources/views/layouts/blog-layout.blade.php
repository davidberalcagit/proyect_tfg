<!doctype html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0,
          maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $metaTitle ?? 'Default title' }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex h-screen flex-col bg-slate-100 selection:bg-sky-600 selection:text-sky-50">
@include('layouts.blog-navigation')

@session('status')
<div class="bg-green-600 p-4 text-xl text-green-50">
    {{ $value }}
</div>
@endsession

<main>
    {{ $slot }}
</main>

<footer class="py-10 px-4">
    <div
        class="mx-auto flex max-w-6xl flex-col items-center space-y-4 md:flex-row md:justify-between md:space-y-0"
    >
        <div
            class="text-center text-sm text-slate-600"
        >
            ® 2025 2DAW All Rights Reserved.
        </div>
    </div>
</footer>

</body>
</html>
