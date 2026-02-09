<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="{{ asset('storage/logos/logo_mini_trans.png') }}" type="image/png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-gray-100">
        <div class="min-h-screen flex flex-col justify-between">

            <div class="fixed w-full z-50 top-0">
                @livewire('navigation-menu')
            </div>

            <header class="relative h-[85vh] flex items-center justify-center overflow-hidden mt-16">
                <video autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover z-0">
                    <source src="{{ asset('storage/184734-873923034_small.mp4') }}" type="video/mp4">
                </video>

                <div class="absolute inset-0 bg-black opacity-50 z-10"></div>

                <div class="relative z-20 text-center text-white p-6 max-w-4xl 2xl:max-w-6xl mx-auto">
                    <h1 class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl xl:text-7xl 2xl:text-8xl font-extrabold leading-tight mb-6 drop-shadow-xl animate-fade-in-up">
                        {{ __('Find Your Dream Car Today') }}
                    </h1>

                    <p class="text-lg sm:text-xl md:text-2xl lg:text-2xl xl:text-3xl 2xl:text-4xl mb-10 drop-shadow-md font-light">
                        {{ __('Explore a wide selection of new and used cars, compare prices, and connect with sellers.') }}
                    </p>

                    <a href="{{ route('cars.index') }}" aria-label="{{ __('Browse our full catalog of cars') }}">
                        <x-button class="!bg-[#B35F12] !hover:bg-[#9A5210] !text-white !font-bold !py-3 !px-8 sm:!py-4 sm:!px-10 !rounded-full text-lg sm:text-xl 2xl:text-2xl !shadow-2xl transform hover:scale-105 transition duration-300">
                            {{ __('Browse Cars') }}
                        </x-button>
                    </a>
                </div>
            </header>

            <section class="py-16 sm:py-20 2xl:py-32 bg-white">
                <div class="max-w-7xl 2xl:max-w-[90%] mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h2 class="text-3xl sm:text-4xl 2xl:text-5xl font-bold text-[#284961] mb-12 2xl:mb-20">{{ __('Why Choose Us?') }}</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 xl:gap-10 2xl:gap-16">

                        <div class="p-8 2xl:p-12 shadow-lg rounded-xl border border-gray-200 hover:shadow-2xl transition duration-300 transform hover:-translate-y-1 bg-gray-50">
                            <div class="text-[#B35F12] mb-6 2xl:mb-8">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 sm:h-16 sm:w-16 2xl:h-20 2xl:w-20 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl sm:text-2xl 2xl:text-3xl font-semibold text-[#284961] mb-4">{{ __('Wide Selection') }}</h3>
                            <p class="text-gray-600 leading-relaxed text-base 2xl:text-xl">{{ __('Discover thousands of vehicles from various brands and models tailored to your needs.') }}</p>
                        </div>

                        <div class="p-8 2xl:p-12 shadow-lg rounded-xl border border-gray-200 hover:shadow-2xl transition duration-300 transform hover:-translate-y-1 bg-gray-50">
                            <div class="text-[#B35F12] mb-6 2xl:mb-8">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 sm:h-16 sm:w-16 2xl:h-20 2xl:w-20 mx-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-xl sm:text-2xl 2xl:text-3xl font-semibold text-[#284961] mb-4">{{ __('Best Prices') }}</h3>
                            <p class="text-gray-600 leading-relaxed text-base 2xl:text-xl">{{ __('Compare and find the most competitive prices in the market with transparent deals.') }}</p>
                        </div>

                        <div class="p-8 2xl:p-12 shadow-lg rounded-xl border border-gray-200 hover:shadow-2xl transition duration-300 transform hover:-translate-y-1 bg-gray-50 md:col-span-2 lg:col-span-1">
                            <div class="text-[#B35F12] mb-6 2xl:mb-8">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-12 w-12 sm:h-16 sm:w-16 2xl:h-20 2xl:w-20 mx-auto">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                                </svg>

                            </div>
                            <h3 class="text-xl sm:text-2xl 2xl:text-3xl font-semibold text-[#284961] mb-4">{{ __('Easy Process') }}</h3>
                            <p class="text-gray-600 leading-relaxed text-base 2xl:text-xl">{{ __('Simple and secure steps to buy or sell your vehicle without the hassle.') }}</p>
                        </div>

                    </div>
                </div>
            </section>

            <section class="bg-[#284961] py-20 2xl:py-32 text-white text-center">
                <div class="max-w-4xl 2xl:max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                    <h2 class="text-3xl sm:text-4xl 2xl:text-6xl font-bold mb-6 2xl:mb-10">{{ __('Ready to Sell Your Car?') }}</h2>
                    <p class="text-lg sm:text-xl 2xl:text-3xl mb-10 2xl:mb-16 opacity-90">{{ __('List your vehicle today and reach thousands of potential buyers instantly.') }}</p>

                    <a href="{{ route('cars.create') }}" aria-label="{{ __('Start selling your car now') }}">
                        <x-button class="!bg-[#B35F12] !hover:bg-[#9A5210] !text-white !font-bold !py-4 !px-10 !rounded-full text-xl 2xl:text-3xl !shadow-lg transform hover:scale-105 transition duration-300">
                            {{ __('Sell Your Car') }}
                        </x-button>
                    </a>
                </div>
            </section>

            <footer class="bg-gray-900 text-gray-400 py-10 2xl:py-16">
                <div class="max-w-7xl 2xl:max-w-[90%] mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm 2xl:text-lg">
                    <p class="mb-4">&copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.</p>
                    <div class="space-x-6">
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
