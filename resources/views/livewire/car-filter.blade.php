<div>
    <!-- Barra de Herramientas -->
    <div class="bg-white p-3 rounded-lg shadow border border-custom-border mb-6">
        <div class="flex flex-col xl:flex-row justify-between items-center gap-3">
            <h3 class="text-base font-medium text-gray-900 whitespace-nowrap hidden md:block">{{ __('Available Vehicles') }}</h3>

            <div class="flex flex-col md:flex-row gap-2 w-full xl:w-auto items-center flex-wrap justify-end text-sm">
                <!-- Buscador -->
                <div class="relative w-full md:w-40">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="{{ __('Search...') }}" class="w-full text-xs py-1.5 border-custom-border rounded-md focus:border-[#B35F12] focus:ring-[#B35F12]" />
                </div>

                <!-- Marca -->
                <div class="w-full md:w-32">
                    <select wire:model.live="brand" class="w-full text-xs py-1.5 border-gray-300 rounded-md shadow-sm focus:border-[#B35F12] focus:ring-[#B35F12]">
                        <option value="">{{ __('All Brands') }}</option>
                        @foreach($brands as $b)
                            <option value="{{ $b->id }}">{{ $b->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Rango de Precio -->
                <div class="flex items-center gap-1">
                    <input wire:model.live.debounce.500ms="min_price" type="number" placeholder="Min €" class="w-16 text-xs py-1.5 border-gray-300 rounded-md shadow-sm focus:border-[#B35F12] focus:ring-[#B35F12]">
                    <span class="text-gray-400">-</span>
                    <input wire:model.live.debounce.500ms="max_price" type="number" placeholder="Max €" class="w-16 text-xs py-1.5 border-gray-300 rounded-md shadow-sm focus:border-[#B35F12] focus:ring-[#B35F12]">
                </div>

                <!-- Ordenar -->
                <div class="w-full md:w-28">
                    <select wire:model.live="sort" class="w-full text-xs py-1.5 border-gray-300 rounded-md shadow-sm focus:border-[#B35F12] focus:ring-[#B35F12]">
                        <option value="">{{ __('Random') }}</option>
                        <option value="recent">{{ __('Newest') }}</option>
                        <option value="cheap">{{ __('Price: Low') }}</option>
                        <option value="expensive">{{ __('Price: High') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid de Productos -->
    <div wire:loading.class="opacity-50" class="transition-opacity duration-200">
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse ($cars as $car)
                <div class="bg-white p-4 text-center rounded shadow hover:shadow-xl transform hover:scale-105 transition relative flex flex-col h-full border border-custom-border group">
                    <a href="{{ route('cars.show', $car) }}" class="absolute inset-0 z-0"></a>

                    <!-- Imagen -->
                    <div class="relative mb-4 w-full aspect-square overflow-hidden rounded">
                        @if($car->image)
                            <img src="{{ Storage::url($car->image) }}" alt="{{ $car->title }}" class="h-full w-full object-cover">
                        @else
                            <div class="bg-gray-200 h-full w-full flex items-center justify-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif

                        <!-- Componente ToggleFavorite (Botón Me Gusta) -->
                        @auth
                            <div class="absolute bottom-2 right-2 z-10">
                                @livewire('toggle-favorite', ['car' => $car], key('fav-'.$car->id))
                            </div>
                        @endauth
                    </div>

                    <h3 class="font-bold text-lg mb-1 truncate text-gray-900 relative z-10 pointer-events-none">{{ $car->title }}</h3>
                    <p class="text-[#4C86B3] font-semibold text-xl mb-2 relative z-10 pointer-events-none">{{ number_format($car->precio, 0) }}€</p>

                    <div class="mt-auto relative z-10">
                        <span class="m-2 bg-[#B35F12] text-white px-4 py-2 rounded hover:scale-110 transition inline-block pointer-events-none">
                            {{ __('View Details') }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-10 text-[#6B7280]">
                    {{ __('No cars found matching your criteria.') }}
                </div>
            @endforelse
        </section>

        <div class="mt-8">
            {{ $cars->links() }}
        </div>
    </div>
</div>
