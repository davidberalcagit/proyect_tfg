<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cars') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Barra de Herramientas y Buscador -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 px-6 gap-4">
                <h3 class="text-lg font-medium text-gray-900">{{ __('Available Vehicles') }}</h3>

                <!-- Buscador -->
                <form method="GET" action="{{ route('cars.index') }}" class="flex w-full md:w-auto gap-2">
                    <x-input type="text" name="search" placeholder="{{ __('Search cars...') }}" value="{{ request('search') }}" class="w-full md:w-64" />
                    <x-button>{{ __('Search') }}</x-button>
                    @if(request('search'))
                        <a href="{{ route('cars.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                            {{ __('Clear') }}
                        </a>
                    @endif
                </form>

                @auth
                    @if(Auth::user()->can('create cars'))
                        <a href="{{ route('cars.my_cars') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition whitespace-nowrap">
                            {{ __('Manage My Cars') }}
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Grid de Productos -->
            <section id="productos" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6 p-6">
                @forelse ($cars as $car)
                    <div class="bg-white p-4 text-center rounded shadow hover:shadow-xl transform hover:scale-105 transition relative flex flex-col h-full">

                        <!-- Imagen (Cuadrada y Uniforme) -->
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

                            <!-- Badge Estado (Top Right) -->
                            <div class="absolute top-2 right-2">
                                @if($car->id_estado == 1)
                                    <span class="bg-green-500 text-white text-xs font-bold px-2 py-1 rounded shadow">
                                        {{ __('For Sale') }}
                                    </span>
                                @elseif($car->id_estado == 3)
                                    <span class="bg-blue-500 text-white text-xs font-bold px-2 py-1 rounded shadow">
                                        {{ __('For Rent') }}
                                    </span>
                                @endif
                            </div>

                            <!-- Botón Favorito (Bottom Right) -->
                            @auth
                                @if(!Auth::user()->customer || Auth::user()->customer->id !== $car->id_vendedor)
                                    <div class="absolute bottom-2 right-2">
                                        <form action="{{ route('favorites.toggle', $car) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-2 bg-white rounded-full shadow hover:scale-110 transition text-gray-400 hover:text-red-500 focus:outline-none">
                                                @if(Auth::user()->favorites->contains($car->id))
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-500 fill-current" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </div>

                        <!-- Contenido -->
                        <h3 class="font-bold text-lg mb-1 truncate" title="{{ $car->title }}">{{ $car->title }}</h3>
                        <p class="text-blue-600 font-semibold text-xl mb-2">{{ number_format($car->precio, 0) }}€</p>

                        <div class="text-sm text-gray-500 mb-4 flex justify-center space-x-4">
                            <span>{{ $car->anyo_matri }}</span>
                            <span>|</span>
                            <span>{{ number_format($car->km) }} km</span>
                        </div>

                        <div class="mt-auto">
                            <a href="{{ route('cars.show', $car) }}" class="m-2 bg-green-500 text-white px-4 py-2 rounded hover:scale-110 transition inline-block">
                                {{ __('View Details') }}
                            </a>

                            <!-- Admin Actions -->
                            @auth
                                @if(Auth::user()->hasRole('admin') || (Auth::user()->customer && Auth::user()->customer->id === $car->id_vendedor && $car->id_estado == 4))
                                    <div class="mt-2 flex justify-center space-x-2 text-xs">
                                        <a href="{{ route('cars.edit', $car) }}" class="text-blue-600 hover:underline">{{ __('Edit') }}</a>
                                        <form action="{{ route('cars.destroy', $car) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">{{ __('Delete') }}</button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </div>

                    </div>
                @empty
                    <div class="col-span-full text-center py-10 text-gray-500">
                        {{ __('No cars found.') }}
                    </div>
                @endforelse
            </section>

            @if($cars->hasPages())
                <div class="mt-8 px-6">
                    {{ $cars->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
