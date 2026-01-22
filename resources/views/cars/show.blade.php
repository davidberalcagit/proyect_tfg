<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $car->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Imagen -->
                        <div class="relative">
                            @if ($car->image)
                                <img src="{{ Storage::url($car->image) }}" alt="{{ $car->title }}" class="w-full h-auto rounded-lg shadow-md object-cover">
                            @else
                                <div class="bg-gray-200 w-full h-64 flex items-center justify-center rounded-lg">
                                    <span class="text-gray-500">{{ __('No Image') }}</span>
                                </div>
                            @endif

                            <!-- Botón Favorito (Solo si NO es el dueño) -->
                            @auth
                                @if(!Auth::user()->customer || Auth::user()->customer->id !== $car->id_vendedor)
                                    <div class="absolute top-2 right-2 z-10">
                                        <form action="{{ route('favorites.toggle', $car) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="p-2 rounded-full bg-white bg-opacity-75 hover:bg-opacity-100 transition shadow-md">
                                                @if(Auth::user()->favorites->contains($car->id))
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-500 fill-current" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400 hover:text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </div>

                        <!-- Detalles -->
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $car->title }}</h1>

                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="col-span-2">
                                    <span class="text-gray-500 block">{{ __('Price') }}</span>
                                    <span class="text-2xl font-bold text-indigo-600">{{ number_format($car->precio, 2) }} €</span>
                                </div>

                                <div>
                                    <span class="text-gray-500 block">{{ __('Brand') }}</span>
                                    <span class="font-medium">
                                        {{ $car->marca->nombre ?? ($car->temp_brand ? $car->temp_brand . ' (New)' : '-') }}
                                    </span>
                                </div>

                                <div>
                                    <span class="text-gray-500 block">{{ __('Model') }}</span>
                                    <span class="font-medium">
                                        {{ $car->modelo->nombre ?? ($car->temp_model ? $car->temp_model . ' (New)' : '-') }}
                                    </span>
                                </div>

                                <div>
                                    <span class="text-gray-500 block">{{ __('Year') }}</span>
                                    <span class="font-medium">{{ $car->anyo_matri }}</span>
                                </div>

                                <div>
                                    <span class="text-gray-500 block">{{ __('KM') }}</span>
                                    <span class="font-medium">{{ number_format($car->km) }} km</span>
                                </div>

                                <div>
                                    <span class="text-gray-500 block">{{ __('Fuels') }}</span>
                                    <span class="font-medium">{{ $car->combustible->nombre ?? '-' }}</span>
                                </div>

                                <div>
                                    <span class="text-gray-500 block">{{ __('Gear') }}</span>
                                    <span class="font-medium">{{ $car->marcha->tipo ?? '-' }}</span>
                                </div>

                                <div>
                                    <span class="text-gray-500 block">{{ __('Color') }}</span>
                                    <span class="font-medium">
                                        {{ $car->color->nombre ?? ($car->temp_color ? $car->temp_color . ' (New)' : '-') }}
                                    </span>
                                </div>

                                <div>
                                    <span class="text-gray-500 block">{{ __('Matricula') }}</span>
                                    <span class="font-medium uppercase">{{ $car->matricula }}</span>
                                </div>

                                <div>
                                    <span class="text-gray-500 block">{{ __('Status') }}</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($car->id_estado == 1) bg-green-100 text-green-800
                                        @elseif($car->id_estado == 2) bg-gray-100 text-gray-800
                                        @elseif($car->id_estado == 3) bg-blue-100 text-blue-800
                                        @elseif($car->id_estado == 4) bg-orange-100 text-orange-800
                                        @elseif($car->id_estado == 5) bg-red-100 text-red-800
                                        @elseif($car->id_estado == 6) bg-indigo-100 text-indigo-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $car->status->nombre ?? 'Unknown' }}
                                    </span>
                                </div>
                            </div>

                            <div class="mt-6">
                                <span class="text-gray-500 block mb-1">{{ __('Descripcion') }}</span>
                                <p class="text-gray-700 bg-gray-50 p-3 rounded-md text-sm leading-relaxed">
                                    {{ $car->descripcion }}
                                </p>
                            </div>

                            <!-- Acciones -->
                            <div class="mt-8 flex flex-wrap gap-3 items-end">
                                <a href="javascript:history.back()" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition h-10">
                                    {{ __('Back to list') }}
                                </a>

                                @auth
                                    {{-- Ofertas (Solo si está en venta) --}}
                                    @if(Auth::user()->customer && Auth::user()->customer->id !== $car->id_vendedor)
                                        @if($car->id_estado === 1)
                                            @if(Auth::user()->can('buy cars'))
                                                <form action="{{ route('offers.store', $car) }}" method="POST" class="flex flex-col gap-2">
                                                    @csrf
                                                    <div class="flex items-center gap-2">
                                                        <input type="number" name="cantidad" value="{{ $car->precio }}" class="form-input rounded-md shadow-sm w-32 text-sm h-10" required min="1">
                                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition h-10">
                                                            {{ __('Make Offer') }}
                                                        </button>
                                                    </div>
                                                    <label class="flex items-center mt-1">
                                                        <x-checkbox name="terms" required />
                                                        <span class="ml-2 text-xs text-gray-600">
                                                            {{ __('I agree to the') }}
                                                            <a href="{{ route('sales.terms') }}" target="_blank" class="text-indigo-600 hover:text-indigo-900 underline">
                                                                {{ __('purchase terms') }}
                                                            </a>
                                                        </span>
                                                    </label>
                                                </form>
                                            @endif
                                        @elseif($car->id_estado === 3)
                                            {{-- Alquiler (Solo si está en alquiler) --}}
                                            <a href="{{ route('rentals.create', $car) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition h-10">
                                                {{ __('Rent Car') }}
                                            </a>
                                        @endif
                                    @endif

                                    {{-- Editar/Borrar (Admin siempre, Dueño solo si pendiente) --}}
                                    @if(Auth::user()->hasRole('admin') || (Auth::user()->customer && Auth::user()->customer->id === $car->id_vendedor && $car->id_estado == 4))
                                        <a href="{{ route('cars.edit', $car) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition h-10">
                                            {{ __('Edit') }}
                                        </a>
                                        <form action="{{ route('cars.destroy', $car) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition h-10">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
