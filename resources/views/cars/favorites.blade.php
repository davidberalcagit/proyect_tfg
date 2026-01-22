<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Favorites') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                @if($cars->isEmpty())
                    <div class="text-center py-10">
                        <p class="text-gray-500 text-lg">{{ __('You have no favorite cars yet.') }}</p>
                        <a href="{{ route('cars.index') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                            {{ __('Browse Cars') }}
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($cars as $car)
                            <div class="bg-white shadow-lg rounded-lg overflow-hidden flex flex-col relative group border border-gray-100 h-full">

                                <!-- Imagen Container - Altura fija -->
                                <div class="relative h-56 w-full flex-shrink-0">
                                    @if($car->image)
                                        <img src="{{ Storage::url($car->image) }}" alt="{{ $car->title }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="bg-gray-200 h-full w-full flex items-center justify-center">
                                            <span class="text-gray-500">{{__('No Image')}}</span>
                                        </div>
                                    @endif

                                    <!-- Overlay Flexbox -->
                                    <div class="absolute inset-0 p-2 flex flex-col justify-between items-end z-10 pointer-events-none">

                                        <!-- Badge -->
                                        <div class="pointer-events-auto">
                                            @if($car->id_estado == 1)
                                                <span class="px-3 py-1 text-sm font-bold text-white bg-green-600 rounded-full shadow-md">
                                                    {{ __('For Sale') }}
                                                </span>
                                            @elseif($car->id_estado == 3)
                                                <span class="px-3 py-1 text-sm font-bold text-white bg-blue-600 rounded-full shadow-md">
                                                    {{ __('For Rent') }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Botón Favorito (Quitar) -->
                                        <div class="pointer-events-auto">
                                            <form action="{{ route('favorites.toggle', $car) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="p-2 rounded-full bg-white bg-opacity-90 hover:bg-opacity-100 transition shadow-lg transform hover:scale-110" title="{{ __('Remove from favorites') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600 fill-current" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <a href="{{ route('cars.show', $car) }}" class="block hover:bg-gray-50 transition duration-150 ease-in-out flex-grow flex flex-col">
                                    <div class="p-4 flex-grow">
                                        <h3 class="text-xl text-gray-900 font-bold mb-2">{{ $car->title }}</h3>
                                        <p class="text-gray-600 text-sm line-clamp-3">"{{ $car->descripcion }}"</p>
                                    </div>
                                    <div class="p-4 border-t border-gray-200 bg-gray-50">
                                        <div class="flex justify-between items-center">
                                            <span class="text-2xl font-bold text-indigo-600">{{ number_format($car->precio, 0) }}€</span>
                                            <div class="text-xs text-gray-500 text-right">
                                                <div>{{ $car->anyo_matri }}</div>
                                                <div>{{ number_format($car->km) }} km</div>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6">
                        {{ $cars->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
