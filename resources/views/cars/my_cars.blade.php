<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-[#284961] leading-tight">
                {{ __('My Cars') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('cars.create', ['type' => 'sale']) }}" class="inline-flex items-center px-4 py-2 bg-[#B35F12] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#9A5210] active:bg-[#9A5210] focus:outline-none focus:border-[#9A5210] focus:ring focus:ring-orange-300 disabled:opacity-25 transition shadow-md">
                    {{ __('Publish Car for Sale') }}
                </a>
                <a href="{{ route('cars.create', ['type' => 'rent']) }}" class="inline-flex items-center px-4 py-2 bg-[#284961] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#1c3344] active:bg-[#1c3344] focus:outline-none focus:border-[#1c3344] focus:ring focus:ring-blue-300 disabled:opacity-25 transition shadow-md">
                    {{ __('Publish Car for Rent') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[95%] 2xl:max-w-[90%] mx-auto sm:px-6 lg:px-8 transition-all duration-300">

            @if($cars->isEmpty())
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-10 text-center border border-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="text-gray-500 text-lg mb-6">{{ __('You have not listed any cars yet.') }}</p>
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('cars.create', ['type' => 'sale']) }}" class="inline-flex items-center px-6 py-3 bg-[#B35F12] border border-transparent rounded-full font-bold text-sm text-white uppercase tracking-widest hover:bg-[#9A5210] transition shadow-lg transform hover:scale-105">
                            {{ __('Publish Car for Sale') }}
                        </a>
                        <a href="{{ route('cars.create', ['type' => 'rent']) }}" class="inline-flex items-center px-6 py-3 bg-[#284961] border border-transparent rounded-full font-bold text-sm text-white uppercase tracking-widest hover:bg-[#1c3344] transition shadow-lg transform hover:scale-105">
                            {{ __('Publish Car for Rent') }}
                        </a>
                    </div>
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
                                         onerror="this.style.display='none'; document.getElementById('fallback-my-{{ $car->id }}').classList.remove('hidden');">

                                    <div id="fallback-my-{{ $car->id }}" class="hidden bg-gray-200 h-full w-full flex items-center justify-center text-gray-400 absolute inset-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @else
                                    <div class="bg-gray-200 h-full w-full flex items-center justify-center text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 sm:h-12 sm:w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif

                                <div class="absolute top-2 right-2 z-20">
                                    <span class="px-2 py-1 text-[10px] sm:text-xs font-bold text-white rounded shadow-sm
                                        @if($car->id_estado == 1) bg-green-600
                                        @elseif($car->id_estado == 2) bg-gray-600
                                        @elseif($car->id_estado == 3) bg-blue-600
                                        @elseif($car->id_estado == 4) bg-orange-500
                                        @elseif($car->id_estado == 5) bg-red-600
                                        @elseif($car->id_estado == 6) bg-indigo-600
                                        @endif">
                                        {{ $car->status->nombre ?? 'Unknown' }}
                                    </span>
                                </div>
                            </div>

                            <h3 class="font-bold text-sm sm:text-lg mb-1 truncate text-gray-900 relative z-10 pointer-events-none" title="{{ $car->title }}">{{ $car->title }}</h3>
                            <p class="text-[#4C86B3] font-semibold text-base sm:text-xl mb-2 sm:mb-3 relative z-10 pointer-events-none">{{ number_format($car->precio, 0) }}€</p>

                            <div class="text-[10px] sm:text-xs text-gray-500 mb-2 sm:mb-4 flex flex-wrap justify-center items-center gap-1 sm:gap-2 relative z-10 pointer-events-none">
                                <span class="bg-gray-100 px-1.5 py-0.5 sm:px-2 sm:py-1 rounded">{{ $car->anyo_matri }}</span>
                                <span class="hidden sm:inline">|</span>
                                <span class="bg-gray-100 px-1.5 py-0.5 sm:px-2 sm:py-1 rounded">{{ number_format($car->km) }} km</span>
                            </div>

                            <div class="mt-auto relative z-20 flex flex-col gap-2">
                                <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                                    @if(!in_array($car->id_estado, [2, 5]))
                                        <a href="{{ route('cars.edit', $car) }}" class="text-[#284961] hover:text-[#1c3344] text-xs font-bold uppercase tracking-wide">{{ __('Edit') }}</a>
                                    @else
                                        <span class="text-gray-400 text-xs font-bold uppercase tracking-wide cursor-not-allowed">{{ __('Edit') }}</span>
                                    @endif

                                    <form action="{{ route('cars.destroy', $car) }}" method="POST" class="inline action-form" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold uppercase tracking-wide">{{ __('Delete') }}</button>
                                    </form>
                                </div>

                                @if(in_array($car->id_estado, [1, 3]))
                                    @if($car->id_estado != 1)
                                        <form action="{{ route('cars.status.sale', $car) }}" method="POST" class="action-form">
                                            @csrf
                                            <button type="submit" class="w-full inline-flex justify-center items-center px-2 py-1 bg-green-600 border border-transparent rounded text-[10px] sm:text-xs font-bold text-white uppercase tracking-widest hover:bg-green-700 transition">
                                                {{ __('Switch to Sale') }}
                                            </button>
                                        </form>
                                    @endif

                                    @if($car->id_estado != 3)
                                        <form action="{{ route('cars.status.rent', $car) }}" method="POST" class="action-form">
                                            @csrf
                                            <button type="submit" class="w-full inline-flex justify-center items-center px-2 py-1 bg-[#284961] border border-transparent rounded text-[10px] sm:text-xs font-bold text-white uppercase tracking-widest hover:bg-[#1c3344] transition">
                                                {{ __('Switch to Rent') }}
                                            </button>
                                        </form>
                                    @endif
                                @elseif($car->id_estado == 6)
                                    <div class="w-full text-center px-2 py-1 bg-indigo-100 text-indigo-800 rounded text-[10px] sm:text-xs font-bold uppercase tracking-widest">
                                        {{ __('Rented') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $cars->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
