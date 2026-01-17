<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Cars') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="flex justify-end">
                        <a href="{{ route('cars.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                            {{ __('Create Car') }}
                        </a>
                    </div>
                    <div class="mt-6">
                        @if($cars->isEmpty())
                            <p class="text-gray-500">{{ __('You have not listed any cars yet.') }}</p>
                        @else
                            @if($cars->hasPages())
                                <div class="mb-6">
                                    {{ $cars->links() }}
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach ($cars as $car)
                                    <div class="bg-white shadow-lg rounded-lg overflow-hidden flex flex-col">
                                        <a href="{{ route('cars.show', $car) }}" class="block hover:bg-gray-50 transition duration-150 ease-in-out flex-grow">
                                            @if($car->image)
                                                <img src="{{ Storage::url($car->image) }}" alt="{{ $car->title }}" class="h-48 w-full object-cover">
                                            @else
                                                <div class="bg-gray-200 h-48 w-full flex items-center justify-center">
                                                    <span class="text-gray-500">{{__('No Image')}}</span>
                                                </div>
                                            @endif
                                            <div class="p-4">
                                                <h3 class="text-lg text-gray-800 font-semibold">{{ $car->title }}</h3>
                                                <div class="mt-2">
                                                    @if($car->id_estado == 1)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            {{ __('For Sale') }}
                                                        </span>
                                                    @elseif($car->id_estado == 2)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                            {{ __('Sold') }}
                                                        </span>
                                                    @elseif($car->id_estado == 3)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                            {{ __('For Rent') }}
                                                        </span>
                                                    @elseif($car->id_estado == 4)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                                            {{ __('Pending Review') }}
                                                        </span>
                                                    @elseif($car->id_estado == 5)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                            {{ __('Rejected') }}
                                                        </span>
                                                    @elseif($car->id_estado == 6)
                                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                                            {{ __('Rented') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="p-4 border-t border-gray-200">
                                                <p class="text-gray-600"><strong>{{ __('Price') }}:</strong> {{ $car->precio }}€</p>
                                                <p class="text-gray-600"><strong>{{ __('Year') }}:</strong> {{ $car->anyo_matri }}</p>
                                                <p class="text-gray-600"><strong>{{ __('KM') }}:</strong> {{ $car->km }}</p>
                                            </div>
                                        </a>
                                        <div class="p-4 bg-gray-50 border-t border-gray-200 text-right mt-auto">

                                            {{-- Solo permitir editar y borrar si está PENDIENTE (4) --}}
                                            @if($car->id_estado == 4)
                                                <a href="{{ route('cars.edit', $car) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Edit') }}</a>

                                                <form action="{{ route('cars.destroy', $car) }}" method="POST" class="inline ml-4" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Delete') }}</button>
                                                </form>
                                            @else
                                                <span class="text-gray-400 text-xs cursor-not-allowed" title="{{ __('Cannot modify processed cars') }}">
                                                    {{ __('Locked') }}
                                                </span>
                                            @endif

                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($cars->hasPages())
                                <div class="mt-6">
                                    {{ $cars->links() }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
