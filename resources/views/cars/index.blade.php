<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">
            {{ __('Cars') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">

            <div class="flex flex-col lg:flex-row gap-6">

                <!-- Sidebar (Izquierda) - Solo Marcas -->
                <div class="w-full lg:w-1/5">
                    <div class="bg-white p-6 rounded-lg shadow sticky top-24 border border-custom-border max-h-[calc(100vh_-_8rem)] overflow-y-auto overscroll-y-contain">
                        <h3 class="text-lg font-bold text-[#284961] mb-4 border-b pb-2 sticky top-0 bg-white z-10">{{ __('Brands & Models') }}</h3>

                        <div x-data="{ showAll: false }">
                            <ul class="space-y-4">
                                @foreach($brands as $index => $brand)
                                    <li x-show="showAll || {{ $index }} < 100" class="border-b border-gray-100 last:border-0 pb-2">
                                        <div x-data="{ open: false }">
                                            <button @click="open = !open" class="flex justify-between items-center w-full text-left font-medium text-gray-700 hover:text-[#B35F12] transition">
                                                <span>{{ $brand->nombre }}</span>
                                                <svg class="w-4 h-4 transform transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </button>

                                            <ul x-show="open" x-collapse class="mt-2 pl-4 space-y-1 text-sm text-gray-600">
                                                <li>
                                                    <a href="{{ route('cars.index', array_merge(request()->all(), ['brand' => $brand->id])) }}" class="hover:text-[#B35F12] transition block py-1 font-semibold text-[#284961]">
                                                        {{ __('All') }} {{ $brand->nombre }}
                                                    </a>
                                                </li>
                                                @foreach($brand->models as $model)
                                                    <li>
                                                        <a href="{{ route('cars.index', ['search' => $model->nombre]) }}" class="hover:text-[#B35F12] transition block py-1">
                                                            {{ $model->nombre }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Contenido Principal (Derecha) - Reemplazado por Livewire -->
                <div class="w-full lg:w-4/5">
                    @livewire('car-filter')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
