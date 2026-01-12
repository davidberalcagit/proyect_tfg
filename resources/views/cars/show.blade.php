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
                    @if ($car->image)
                        <div class="flex justify-center">
                            <img src="{{ Storage::url($car->image) }}" alt="{{ $car->title }}" class="h-80 w-auto object-cover">
                        </div>
                    @endif
                    <div class="mt-6">
                        <h1 class="text-2xl font-semibold text-gray-800">{{ $car->title }}</h1>
                        <p><strong>Price:</strong> {{ $car->precio }}</p>
                        <p><strong>Year:</strong> {{ $car->anyo_matri }}</p>
                        <p><strong>KM:</strong> {{ $car->km }}</p>
                        <p><strong>Status:</strong> {{ $car->status->nombre }}</p>
                    </div>
                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('cars.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            {{ __('Back to list') }}
                        </a>

                        @auth
                            {{-- Logic for Offers: Only if not owner and car is for sale --}}
                            @if(Auth::user()->customer && Auth::user()->customer->id !== $car->id_vendedor)
                                @if($car->id_estado === 1) {{-- 1 = En venta --}}
                                    @if(Auth::user()->can('buy cars')) {{-- Check if user has permission to buy --}}
                                        <form action="{{ route('offers.store', $car) }}" method="POST" class="ml-4 flex items-center">
                                            @csrf
                                            <input type="number" name="cantidad" value="{{ $car->precio }}" class="form-input rounded-md shadow-sm mr-2 w-32" required>
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
                                                {{ __('Make Offer') }}
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <span class="ml-4 px-4 py-2 bg-gray-400 text-white rounded-md font-semibold text-xs uppercase">
                                        {{ __('Sold') }}
                                    </span>
                                @endif
                            @endif

                            {{-- Logic for Edit/Delete: If has global permission OR (has own permission AND is owner) --}}
                            @if(Auth::user()->can('crud all cars') || (Auth::user()->can('crud own cars') && Auth::user()->customer && Auth::user()->customer->id === $car->id_vendedor))
                                <a href="{{ route('cars.edit', $car) }}" class="ml-4 inline-flex items-center px-4 py-2 bg-blue-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                                    {{ __('Edit') }}
                                </a>
                                <form action="{{ route('cars.destroy', $car) }}" method="POST" class="ml-4">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring focus:ring-red-300 disabled:opacity-25 transition">
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
</x-app-layout>
