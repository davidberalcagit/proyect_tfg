<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Cars') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('cars.create', ['type' => 'sale']) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
                    {{ __('Publish Car for Sale') }}
                </a>
                <a href="{{ route('cars.create', ['type' => 'rent']) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                    {{ __('Publish Car for Rent') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">

                    @if($cars->isEmpty())
                        <div class="text-center py-10">
                            <p class="text-gray-500 text-lg">{{ __('You have not listed any cars yet.') }}</p>
                            <div class="mt-6 flex justify-center space-x-4">
                                <a href="{{ route('cars.create', ['type' => 'sale']) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                                    {{ __('Publish Car for Sale') }}
                                </a>
                                <a href="{{ route('cars.create', ['type' => 'rent']) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                                    {{ __('Publish Car for Rent') }}
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($cars as $car)
                                <div class="bg-white shadow-lg rounded-lg overflow-hidden flex flex-col relative group border border-gray-100 h-full">

                                    <!-- Imagen -->
                                    <div class="relative h-48 w-full">
                                        @if($car->image)
                                            <img src="{{ Storage::url($car->image) }}" alt="{{ $car->title }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="bg-gray-200 h-full w-full flex items-center justify-center">
                                                <span class="text-gray-500">{{__('No Image')}}</span>
                                            </div>
                                        @endif

                                        <!-- Badge de Estado -->
                                        <div class="absolute top-2 right-2 z-10">
                                            <span class="px-2 py-1 text-xs font-bold text-white rounded-full shadow-sm
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

                                    <div class="p-4 flex-grow">
                                        <h3 class="text-lg text-gray-800 font-semibold mb-2">{{ $car->title }}</h3>
                                        <p class="text-gray-600 text-sm mb-1"><strong>{{ __('Price') }}:</strong> {{ number_format($car->precio, 2) }}€</p>

                                        @if($car->id_estado == 4)
                                            <p class="text-xs text-orange-600 mt-2 bg-orange-50 p-2 rounded border border-orange-200">
                                                {{ __('Pending Review') }}
                                            </p>
                                        @elseif($car->id_estado == 5)
                                            <p class="text-xs text-red-600 mt-2 bg-red-50 p-2 rounded border border-red-200">
                                                <strong>{{ __('Rejected') }}:</strong> {{ $car->rejection_reason }}
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Acciones -->
                                    <div class="p-4 bg-gray-50 border-t border-gray-200 flex flex-col gap-2">
                                        <div class="flex justify-between">
                                            <a href="{{ route('cars.edit', $car) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">{{ __('Edit') }}</a>
                                            <form action="{{ route('cars.destroy', $car) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">{{ __('Delete') }}</button>
                                            </form>
                                        </div>

                                        <!-- Botones de Cambio de Estado -->
                                        @if(in_array($car->id_estado, [1, 3, 6]))
                                            <div class="mt-2 flex flex-col gap-2">
                                                @if($car->id_estado != 1)
                                                    <form action="{{ route('cars.status.sale', $car) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 active:bg-green-900 transition">
                                                            {{ __('Switch to Sale') }}
                                                        </button>
                                                    </form>
                                                @endif

                                                @if($car->id_estado != 3)
                                                    <form action="{{ route('cars.status.rent', $car) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 active:bg-blue-900 transition">
                                                            {{ __('Switch to Rent') }}
                                                        </button>
                                                    </form>
                                                @endif
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
        </div>
    </div>
</x-app-layout>
