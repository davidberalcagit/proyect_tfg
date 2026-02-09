<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="{{ asset('storage/logos/logo_mini_trans_blanco.png') }}" type="image/png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        <x-banner />

        <div id="global-loading-overlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-[9999] hidden flex items-center justify-center cursor-wait transition-opacity duration-300">
            <div class="bg-white p-5 rounded-lg shadow-xl flex flex-col items-center transform transition-transform duration-300 scale-100">
                <svg class="animate-spin h-10 w-10 text-[#B35F12] mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-700 font-semibold">{{ __('Processing...') }}</span>
            </div>
        </div>

        <div class="min-h-screen bg-gray-50">
            @livewire('navigation-menu')

            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const overlay = document.getElementById('global-loading-overlay');

                const forms = document.querySelectorAll('form.action-form, form.admin-action-form');
                forms.forEach(form => {
                    form.addEventListener('submit', function () {
                        overlay.classList.remove('hidden');
                        document.body.classList.add('cursor-wait');
                    });
                });

                if (typeof Livewire !== 'undefined') {
                    Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                        if (component.name === 'toggle-favorite') {
                            return;
                        }

                        overlay.classList.remove('hidden');
                        document.body.classList.add('cursor-wait');

                        succeed(() => {
                            overlay.classList.add('hidden');
                            document.body.classList.remove('cursor-wait');
                        });

                        fail(() => {
                            overlay.classList.add('hidden');
                            document.body.classList.remove('cursor-wait');
                        });
                    });
                }
            });
        </script>

        @stack('scripts')
    </body>
</html>
