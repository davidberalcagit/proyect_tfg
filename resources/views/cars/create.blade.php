<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Car') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 shadow-md rounded-r" role="alert">
                <p class="font-bold">{{ __('Important Information') }}</p>
                <p>{{ __('The acceptance process for your vehicle may take some time. During this period, your car will appear as') }} <strong>"{{ __('Pending Review') }}"</strong>.</p>
                <p class="mt-2 text-sm">{{ __('You can edit your vehicle data while it is pending.') }} <span class="font-bold text-red-600">{{ __('Once confirmed and published, you will not be able to make changes.') }}</span></p>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <x-validation-errors class="mb-4" />

                    <form action="{{ route('cars.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Incluimos el formulario parcial -->
                        <!-- Pasamos un objeto Cars vacío para que el parcial funcione -->
                        @include('cars._form', [
                            'car' => new \App\Models\Cars(),
                            'listingType' => $listingType
                        ])

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
