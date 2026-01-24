<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            {{ __('Seller Profile') }}: {{ $customer->nombre_contacto }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Info Vendedor -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6 border border-custom-border">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-20 w-20">
                        <div class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center text-2xl font-bold text-gray-500">
                            {{ substr($customer->nombre_contacto, 0, 1) }}
                        </div>
                    </div>
                    <div class="ml-6">
                        <h3 class="text-2xl font-bold text-[#284961]">{{ $customer->nombre_contacto }}</h3>
                        <p class="text-sm text-gray-500">
                            {{ $customer->entityType->nombre ?? 'Individual' }}
                            @if($customer->dealership)
                                - {{ $customer->dealership->nombre_empresa }}
                            @endif
                        </p>

                        @if($customer->dealership)
                            <div class="mt-2 text-sm text-gray-600 space-y-1">
                                <p class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-[#4C86B3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    {{ $customer->telefono }}
                                </p>
                                <p class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-[#4C86B3]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $customer->dealership->direccion }}
                                </p>
                            </div>
                        @endif

                        <p class="text-sm text-gray-500 mt-2">
                            {{ __('Member since') }} {{ $customer->created_at->format('F Y') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Coches del Vendedor -->
            <h3 class="text-lg font-medium text-[#284961] mb-4 px-2">{{ __('Cars for Sale/Rent by this Seller') }}</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse ($cars as $car)
                    <div class="bg-white p-4 text-center rounded shadow hover:shadow-xl transform hover:scale-105 transition relative flex flex-col h-full border border-custom-border">

                        <!-- Imagen -->
                        <div class="relative mb-4 w-full aspect-square overflow-hidden rounded">
                            <a href="{{ route('cars.show', $car) }}" class="block h-full w-full">
                                @if($car->image)
                                    <img src="{{ Storage::url($car->image) }}" alt="{{ $car->title }}" class="h-full w-full object-cover">
                                @else
                                    <div class="bg-gray-200 h-full w-full flex items-center justify-center text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </a>

                            <!-- Badge Estado -->
                            <div class="absolute top-2 right-2">
                                @if($car->id_estado == 1)
                                    <span class="bg-[#284961] text-white text-xs font-bold px-2 py-1 rounded shadow">
                                        {{ __('For Sale') }}
                                    </span>
                                @elseif($car->id_estado == 3)
                                    <span class="bg-[#4C86B3] text-white text-xs font-bold px-2 py-1 rounded shadow">
                                        {{ __('For Rent') }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Contenido -->
                        <h3 class="font-bold text-lg mb-1 truncate text-gray-900" title="{{ $car->title }}">{{ $car->title }}</h3>
                        <p class="text-[#4C86B3] font-semibold text-xl mb-2">{{ number_format($car->precio, 0) }}€</p>

                        <div class="text-sm text-[#6B7280] mb-4 flex justify-center space-x-4">
                            <span>{{ $car->anyo_matri }}</span>
                            <span>|</span>
                            <span>{{ number_format($car->km) }} km</span>
                        </div>

                        <div class="mt-auto">
                            <a href="{{ route('cars.show', $car) }}" class="m-2 bg-[#B35F12] text-white px-4 py-2 rounded hover:scale-110 transition inline-block">
                                {{ __('View Details') }}
                            </a>
                        </div>

                    </div>
                @empty
                    <div class="col-span-full text-center py-10 text-[#6B7280]">
                        {{ __('No cars found.') }}
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $cars->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
