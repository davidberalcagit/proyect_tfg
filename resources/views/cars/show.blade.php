<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-[#284961] leading-tight truncate">
            {{ $car->title }}
        </h2>
    </x-slot>

    <div class="py-6 lg:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-4 sm:p-6 lg:p-8 bg-white border-b border-gray-200">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                        <div class="relative w-full">
                            @if ($car->image)
                                <img src="{{ Str::startsWith($car->image, 'http') ? $car->image : Storage::url($car->image) }}"
                                     alt="{{ $car->title }}"
                                     class="w-full h-auto rounded-lg shadow-md object-cover aspect-video lg:aspect-auto"
                                     onerror="this.style.display='none'; document.getElementById('fallback-image-{{ $car->id }}').classList.remove('hidden');">

                                <div id="fallback-image-{{ $car->id }}" class="hidden bg-gray-200 w-full h-64 flex items-center justify-center rounded-lg shadow-md">
                                    <span class="text-gray-500 flex flex-col items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ __('Image Not Found') }}
                                    </span>
                                </div>
                            @else
                                <div class="bg-gray-200 w-full h-64 flex items-center justify-center rounded-lg shadow-md">
                                    <span class="text-gray-500 flex flex-col items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ __('No Image') }}
                                    </span>
                                </div>
                            @endif
                            <div class="absolute top-2 left-2 z-20">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold shadow-sm
                                    @if($car->id_estado == 1) bg-green-600 text-white
                                    @elseif($car->id_estado == 2) bg-gray-600 text-white
                                    @elseif($car->id_estado == 3) bg-blue-600 text-white
                                    @elseif($car->id_estado == 4) bg-orange-500 text-white
                                    @elseif($car->id_estado == 5) bg-red-600 text-white
                                    @elseif($car->id_estado == 6) bg-indigo-600 text-white
                                    @else bg-gray-600 text-white @endif">
                                    {{ $car->status->nombre ?? 'Unknown' }}
                                </span>
                            </div>

                            @auth
                                @if(!Auth::user()->customer || Auth::user()->customer->id !== $car->id_vendedor)
                                    <div class="absolute top-2 right-2 z-20">
                                        <livewire:toggle-favorite :car="$car" />
                                    </div>
                                @endif
                            @endauth
                        </div>


                        <div class="flex flex-col h-full">
                            <h1 class="text-2xl sm:text-3xl font-bold text-[#284961] mb-4">{{ $car->title }}</h1>

                            <div class="grid grid-cols-2 gap-4 text-sm sm:text-base mb-6">
                                <div class="col-span-2 flex justify-between items-end border-b pb-2">
                                    <span class="text-gray-500">{{ __('Price') }}</span>
                                    <span class="text-2xl sm:text-3xl font-bold text-[#284961] font-mono">{{ number_format($car->precio, 0) }} €</span>
                                </div>


                                <div class="col-span-2 sm:col-span-1">
                                    <span class="text-gray-500 block text-xs uppercase tracking-wide">{{ __('Seller') }}</span>
                                    <span class="font-medium">
                                        @if($car->vendedor)
                                            @if(Auth::check() && Auth::user()->can('view users') && $car->vendedor->user)
                                                <a href="{{ route('support.users.show', $car->vendedor->user) }}" class="text-[#B35F12] hover:underline font-bold">
                                                    {{ $car->vendedor->nombre_contacto }}
                                                </a>
                                            @else
                                                <a href="{{ route('seller.show', $car->vendedor) }}" class="text-[#B35F12] hover:underline font-bold">
                                                    {{ $car->vendedor->nombre_contacto }}
                                                </a>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </div>

                                <div>
                                    <span class="text-gray-500 block text-xs uppercase tracking-wide">{{ __('Brand') }}</span>
                                    <span class="font-medium">
                                        {{ $car->marca->nombre ?? ($car->temp_brand ? $car->temp_brand . ' (New)' : '-') }}
                                    </span>
                                </div>

                                <div>
                                    <span class="text-gray-500 block text-xs uppercase tracking-wide">{{ __('Model') }}</span>
                                    <span class="font-medium">
                                        {{ $car->modelo->nombre ?? ($car->temp_model ? $car->temp_model . ' (New)' : '-') }}
                                    </span>
                                </div>

                                <div>
                                    <span class="text-gray-500 block text-xs uppercase tracking-wide">{{ __('Year') }}</span>
                                    <span class="font-medium">{{ $car->anyo_matri }}</span>
                                </div>

                                <div>
                                    <span class="text-gray-500 block text-xs uppercase tracking-wide">{{ __('KM') }}</span>
                                    <span class="font-medium">{{ number_format($car->km) }} km</span>
                                </div>

                                <div>
                                    <span class="text-gray-500 block text-xs uppercase tracking-wide">{{ __('Fuel') }}</span>
                                    <span class="font-medium">{{ $car->combustible->nombre ?? '-' }}</span>
                                </div>

                                <div>
                                    <span class="text-gray-500 block text-xs uppercase tracking-wide">{{ __('Gear') }}</span>
                                    <span class="font-medium">{{ $car->marcha->tipo ?? '-' }}</span>
                                </div>

                                <div>
                                    <span class="text-gray-500 block text-xs uppercase tracking-wide">{{ __('Color') }}</span>
                                    <span class="font-medium">
                                        {{ $car->color->nombre ?? ($car->temp_color ? $car->temp_color . ' (New)' : '-') }}
                                    </span>
                                </div>

                                <div>
                                    <span class="text-gray-500 block text-xs uppercase tracking-wide">{{ __('Status') }}</span>
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

                            <div class="mt-4 bg-gray-50 p-4 rounded-lg border border-gray-100">
                                <span class="text-gray-500 block mb-2 text-xs uppercase tracking-wide">{{ __('Description') }}</span>
                                <p class="text-gray-700 text-sm leading-relaxed">
                                    {{ $car->descripcion }}
                                </p>
                            </div>

                            <div class="mt-8 flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-100">
                                <a href="javascript:history.back()" class="w-full sm:w-auto justify-center inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition h-10">
                                    {{ __('Back') }}
                                </a>

                                @auth
                                    @if(Auth::user()->customer && Auth::user()->customer->id !== $car->id_vendedor)
                                        @if($car->id_estado === 1)
                                            @if(Auth::user()->can('buy cars'))
                                                <div class="w-full sm:w-auto">
                                                    <livewire:make-offer :car="$car" />
                                                </div>
                                            @endif
                                        @elseif($car->id_estado === 3)
                                            <a href="{{ route('rentals.create', $car) }}" class="w-full sm:w-auto justify-center inline-flex items-center px-4 py-2 bg-[#B35F12] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#9A5210] focus:bg-[#9A5210] active:bg-[#9A5210] focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition h-10 shadow-md">
                                                {{ __('Rent Car') }}
                                            </a>
                                        @endif
                                    @endif

                                    @can('update', $car)
                                        <a href="{{ route('cars.edit', $car) }}" class="w-full sm:w-auto justify-center inline-flex items-center px-4 py-2 bg-[#284961] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#1c3344] focus:bg-[#1c3344] active:bg-[#1c3344] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition h-10 shadow-md">
                                            {{ __('Edit') }}
                                        </a>
                                    @endcan

                                    @can('delete', $car)
                                        <form action="{{ route('cars.destroy', $car) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure?') }}');" class="w-full sm:w-auto">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full justify-center inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition h-10 shadow-md">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    @endcan
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
