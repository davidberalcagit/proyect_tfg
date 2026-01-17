<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rent Car') }}: {{ $car->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <x-validation-errors class="mb-4" />

                <form action="{{ route('rentals.store', $car) }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-label for="fecha_inicio" value="{{ __('Start Date') }}" />
                            <x-date name="fecha_inicio" id="fecha_inicio" class="mt-1 block w-full" required min="{{ date('Y-m-d') }}" />
                        </div>

                        <div>
                            <x-label for="fecha_fin" value="{{ __('End Date') }}" />
                            <x-date name="fecha_fin" id="fecha_fin" class="mt-1 block w-full" required min="{{ date('Y-m-d') }}" />
                        </div>
                    </div>

                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <p class="text-gray-700">{{ __('Daily Price') }}: <span class="font-bold">{{ $car->precio }}€</span></p>
                        <p class="text-gray-700 mt-2">{{ __('Total Estimated') }}: <span id="total_price" class="font-bold text-xl text-indigo-600">0.00€</span></p>
                    </div>

                    <!-- Checkbox de términos (Uso de x-checkbox) -->
                    <div class="mt-4 block">
                        <label for="terms" class="flex items-center">
                            <x-checkbox id="terms" name="terms" required />
                            <span class="ml-2 text-sm text-gray-600">{{ __('I agree to the rental terms and conditions') }}</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('cars.show', $car) }}" class="text-gray-600 hover:text-gray-900 mr-4">{{ __('Cancel') }}</a>
                        <x-button>
                            {{ __('Confirm Rental') }}
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const startDateInput = document.getElementById('fecha_inicio');
        const endDateInput = document.getElementById('fecha_fin');
        const totalPriceSpan = document.getElementById('total_price');
        const dailyPrice = {{ $car->precio }};

        function calculateTotal() {
            const start = new Date(startDateInput.value);
            const end = new Date(endDateInput.value);

            if (start && end && end > start) {
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                const total = diffDays * dailyPrice;
                totalPriceSpan.textContent = total.toFixed(2) + '€';
            } else {
                totalPriceSpan.textContent = '0.00€';
            }
        }

        startDateInput.addEventListener('change', calculateTotal);
        endDateInput.addEventListener('change', calculateTotal);
    </script>
    @endpush
</x-app-layout>
