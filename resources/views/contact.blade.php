<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Contact Us') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2">

                    <!-- Información de Contacto -->
                    <div class="p-6 sm:p-12 bg-[#284961] text-white flex flex-col justify-center">
                        <h3 class="text-2xl font-bold mb-6">{{ __('Get in Touch') }}</h3>
                        <p class="mb-8 text-gray-300">
                            {{ __('Have questions about buying or selling a car? We are here to help. Fill out the form or contact us directly.') }}
                        </p>

                        <div class="space-y-6">
                            <div class="flex items-start space-x-4">
                                <svg class="w-6 h-6 text-[#B35F12] mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <div>
                                    <h4 class="font-semibold">{{ __('Address') }}</h4>
                                    <p class="text-gray-300">Calle Principal 123, Madrid, España</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4">
                                <svg class="w-6 h-6 text-[#B35F12] mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <div>
                                    <h4 class="font-semibold">{{ __('Email') }}</h4>
                                    <p class="text-gray-300">pacopatata119@gmail.com</p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-4">
                                <svg class="w-6 h-6 text-[#B35F12] mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 00.948.684L10.5 4l-2 3m0 0l-2 3m2-3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <h4 class="font-semibold">{{ __('Phone') }}</h4>
                                    <p class="text-gray-300">+34 912 345 678</p>
                                </div>
                            </div>
                            <div class="mb-8 flex justify-center">
                                <img src="{{ asset('storage/logos/logo_mini_trans_blanco.png') }}" alt="TradeMyCar Logo" class="rounded-2xl h-20 lg:h-48 2xl:h-48 w-auto  transition-all duration-300">
                            </div>
                        </div>
                    </div>

                    <!-- Formulario -->
                    <div class="p-6 sm:p-12 bg-[#4C86B3] text-white">
                        <form action="#" method="POST" onsubmit="event.preventDefault(); alert('{{ __('Message sent successfully!') }}');">
                            <div class="mb-6">
                                <label for="name" class="block text-sm font-medium  mb-2">{{ __('Name') }}</label>
                                <input type="text" id="name" name="name" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#B35F12] focus:ring focus:ring-[#B35F12] focus:ring-opacity-50" required>
                            </div>

                            <div class="mb-6">
                                <label for="email" class="block text-sm font-medium  mb-2">{{ __('Email') }}</label>
                                <input type="email" id="email" name="email" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#B35F12] focus:ring focus:ring-[#B35F12] focus:ring-opacity-50" required>
                            </div>

                            <div class="mb-6">
                                <label for="message" class="block text-sm font-medium  mb-2">{{ __('Message') }}</label>
                                <textarea id="message" name="message" rows="4" class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#B35F12] focus:ring focus:ring-[#B35F12] focus:ring-opacity-50" required></textarea>
                            </div>

                            <button type="submit" class="w-full bg-[#B35F12] hover:bg-[#9A5210] text-white font-bold py-3 px-4 rounded-md shadow transition duration-150 ease-in-out">
                                {{ __('Send Message') }}
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
