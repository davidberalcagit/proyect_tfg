<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Car') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <x-validation-errors class="mb-4" />

                    <form action="{{ route('cars.update', $car) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Incluimos el formulario parcial -->
                        @include('cars._form', ['car' => $car])

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
