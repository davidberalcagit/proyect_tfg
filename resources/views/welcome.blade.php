<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('storage/logos/logo_mini_trans.png') }}" type="image/png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="min-h-screen flex flex-col justify-between">

            <!-- Navigation Menu (Standard) -->
            <div class="fixed w-full z-50 top-0">
                @livewire('navigation-menu')
            </div>

            <!-- Hero Section con Video -->
            <header class="relative h-[85vh] flex items-center justify-center overflow-hidden mt-16">
                <!-- Video de Fondo -->
                <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover z-0">
                    <source src="{{ asset('storage/184734-873923034_small.mp4') }}" type="video/mp4">
                </video>

                <!-- Capa oscura -->
                <div class="absolute inset-0 bg-black opacity-50 z-10"></div>

                <!-- Contenido -->
                <div class="relative z-20 text-center text-white p-6 max-w-4xl mx-auto">
                    <h1 class="text-5xl sm:text-6xl lg:text-7xl font-extrabold leading-tight mb-6 drop-shadow-xl animate-fade-in-up">
                        {{ __('Find Your Dream Car Today') }}
                    </h1>
                    <p class="text-xl sm:text-2xl mb-10 drop-shadow-md font-light">
                        {{ __('Explore a wide selection of new and used cars, compare prices, and connect with sellers.') }}
                    </p>

                    <a href="{{ route('cars.index') }}">
                        <x-button class="!bg-[#B35F12] !hover:bg-[#9A5210] !text-white !font-bold !py-4 !px-10 !rounded-full !text-xl !shadow-2xl transform hover:scale-105 transition duration-300">
                            {{ __('Browse Cars') }}
                        </x-button>
                    </a>
                </div>
            </header>

            <!-- Features Section -->
            <section class="py-20 bg-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h2 class="text-3xl font-bold text-[#284961] mb-12">{{ __('Why Choose Us?') }}</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                        <div class="p-8 shadow-lg rounded-xl border border-gray-100 hover:shadow-2xl transition duration-300 transform hover:-translate-y-1 bg-gray-50">
                            <div class="text-[#B35F12] mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 00.948.684L10.5 4l-2 3m0 0l-2 3m2-3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-semibold text-[#284961] mb-4">{{ __('Wide Selection') }}</h3>
                            <p class="text-gray-600 leading-relaxed">{{ __('Discover thousands of vehicles from various brands and models tailored to your needs.') }}</p>
                        </div>
                        <div class="p-8 shadow-lg rounded-xl border border-gray-100 hover:shadow-2xl transition duration-300 transform hover:-translate-y-1 bg-gray-50">
                            <div class="text-[#B35F12] mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-semibold text-[#284961] mb-4">{{ __('Best Prices') }}</h3>
                            <p class="text-gray-600 leading-relaxed">{{ __('Compare and find the most competitive prices in the market with transparent deals.') }}</p>
                        </div>
                        <div class="p-8 shadow-lg rounded-xl border border-gray-100 hover:shadow-2xl transition duration-300 transform hover:-translate-y-1 bg-gray-50">
                            <div class="text-[#B35F12] mb-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h2a2 2 0 002-2V9.66a1 1 0 00-.325-.75L15.375 4.21a2 2 0 00-1.5-.71H9.125a2 2 0 00-1.5.71L3.325 8.91a1 1 0 00-.325.75V18a2 2 0 002 2h2M7 10v4h10v-4M7 10a2 2 0 012-2h6a2 2 0 012 2M7 10h10" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-semibold text-[#284961] mb-4">{{ __('Easy Process') }}</h3>
                            <p class="text-gray-600 leading-relaxed">{{ __('Simple and secure steps to buy or sell your vehicle without the hassle.') }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Call to Action Section -->
            <section class="bg-[#284961] py-20 text-white text-center">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="text-4xl font-bold mb-6">{{ __('Ready to Sell Your Car?') }}</h2>
                    <p class="text-xl mb-10 opacity-90">{{ __('List your vehicle today and reach thousands of potential buyers instantly.') }}</p>

                    <a href="{{ route('cars.create') }}">
                        <x-button class="!bg-[#B35F12] !hover:bg-[#9A5210] !text-white !font-bold !py-4 !px-10 !rounded-full !text-xl !shadow-lg transform hover:scale-105 transition duration-300">
                            {{ __('Sell Your Car') }}
                        </x-button>
                    </a>
                </div>
            </section>

            <!-- Footer -->
            <footer class="bg-gray-900 text-gray-400 py-10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm">
                    <p class="mb-4">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
                    <div class="space-x-6">
                        <a href="{{ route('about') }}" class="hover:text-white transition">{{ __('About Us') }}</a>
                        <a href="{{ route('contact') }}" class="hover:text-white transition">{{ __('Contact') }}</a>
                        <a href="#" class="hover:text-white transition">{{ __('Privacy Policy') }}</a>
                    </div>
                </div>
            </footer>
        </div>

        @stack('modals')
        @livewireScripts
        @stack('scripts')
    </body>
</html>
