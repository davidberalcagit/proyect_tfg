<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#284961] leading-tight">
            {{ __('My Favorites') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[95%] 2xl:max-w-[90%] mx-auto sm:px-6 lg:px-8 transition-all duration-300">

            @if($cars->isEmpty())
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-10 text-center border border-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                    <p class="text-gray-500 text-lg mb-6">{{ __('You have no favorite cars yet.') }}</p>
                    <a href="{{ route('cars.index') }}" class="inline-flex items-center px-6 py-3 bg-[#B35F12] border border-transparent rounded-full font-bold text-sm text-white uppercase tracking-widest hover:bg-[#9A5210] active:bg-[#9A5210] focus:outline-none focus:border-[#9A5210] focus:ring focus:ring-orange-300 disabled:opacity-25 transition shadow-lg transform hover:scale-105">
                        {{ __('Browse Cars') }}
                    </a>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 sm:gap-6">
                    @foreach ($cars as $car)
                        <div class="bg-white p-2 sm:p-4 text-center rounded shadow hover:shadow-xl transition relative flex flex-col h-full border border-custom-border group">
                            <a href="{{ route('cars.show', $car) }}" class="absolute inset-0 z-10"></a>

                            <div class="relative mb-2 sm:mb-4 w-full aspect-square overflow-hidden rounded bg-gray-100">
                                @if($car->image)
                                    <img src="{{ Str::startsWith($car->image, 'http') ? $car->image : Storage::url($car->image) }}"
                                         alt="{{ $car->title }}"
                                         class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
                                         onerror="this.onerror=null; this.src='{{ asset('storage/images/hero-car.jpg') }}'; this.parentElement.innerHTML='<div class=\'flex items-center justify-center h-full text-gray-400\'><svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-12 w-12\' fill=\'none\' viewBox=\'0 0 24 24\' stroke=\'currentColor\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\' /></svg></div>'">
                                @else
                                    <div class="bg-gray-200 h-full w-full flex items-center justify-center text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 sm:h-12 sm:w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif

                                <div class="absolute top-2 left-2 z-20">
                                    @if($car->id_listing_type == 2)
                                        <span class="bg-orange-500 text-white text-[10px] sm:text-xs font-bold px-2 py-1 rounded shadow">
                                            {{ __('For Rent') }}
                                        </span>
                                    @else
                                        <span class="bg-green-600 text-white text-[10px] sm:text-xs font-bold px-2 py-1 rounded shadow">
                                            {{ __('For Sale') }}
                                        </span>
                                    @endif
                                </div>

                                <div class="absolute bottom-1 right-1 sm:bottom-2 sm:right-2 z-20">
                                    @livewire('toggle-favorite', ['car' => $car], key('fav-'.$car->id))
                                </div>
                            </div>

                            <h3 class="font-bold text-sm sm:text-lg mb-1 truncate text-gray-900 relative z-10 pointer-events-none" title="{{ $car->title }}">{{ $car->title }}</h3>
                            <p class="text-[#4C86B3] font-semibold text-base sm:text-xl mb-2 sm:mb-3 relative z-10 pointer-events-none">{{ number_format($car->precio, 0) }}€</p>

                            <div class="text-[10px] sm:text-xs text-gray-500 mb-2 sm:mb-4 flex flex-wrap justify-center items-center gap-1 sm:gap-2 relative z-10 pointer-events-none">
                                <span class="bg-gray-100 px-1.5 py-0.5 sm:px-2 sm:py-1 rounded">{{ $car->anyo_matri }}</span>
                                <span class="hidden sm:inline">|</span>
                                <span class="bg-gray-100 px-1.5 py-0.5 sm:px-2 sm:py-1 rounded">{{ number_format($car->km) }} km</span>
                                <span class="hidden sm:inline">|</span>
                                <span class="bg-gray-100 px-1.5 py-0.5 sm:px-2 sm:py-1 rounded truncate max-w-[60px] sm:max-w-none">{{ $car->combustible->nombre ?? '-' }}</span>
                            </div>

                            <div class="mt-auto relative z-10">
                                <span class="m-1 sm:m-2 bg-[#B35F12] text-white px-2 py-1 sm:px-4 sm:py-2 rounded hover:scale-110 transition inline-block pointer-events-none text-xs sm:text-sm font-semibold w-full sm:w-auto">
                                    {{ __('View') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $cars->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
