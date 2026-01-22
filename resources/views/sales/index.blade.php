<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Transactions') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ activeTab: 'offers' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'offers'"
                        :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'offers', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'offers' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        {{ __('Offers') }}
                    </button>

                    <button @click="activeTab = 'rentals'"
                        :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'rentals', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'rentals' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        {{ __('Rentals') }}
                    </button>

                    <button @click="activeTab = 'history'"
                        :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'history', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'history' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        {{ __('History') }}
                    </button>
                </nav>
            </div>

            <!-- Tab: Offers -->
            <div x-show="activeTab === 'offers'" class="space-y-8">
                <!-- 1. Ofertas Recibidas -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 bg-indigo-50">
                        <h3 class="text-lg font-medium text-indigo-900 mb-4">{{ __('Received Offers (Sales)') }}</h3>
                        @if($receivedOffers->isEmpty())
                            <p class="text-gray-500">{{ __('No offers received.') }}</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-indigo-100">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Car') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Buyer') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($receivedOffers as $offer)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <a href="{{ route('cars.show', $offer->car) }}" class="text-indigo-600 hover:text-indigo-900">{{ $offer->car->title }}</a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $offer->buyer->nombre_contacto }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($offer->cantidad, 2) }} €</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        @if($offer->estado == 'pending') bg-yellow-100 text-yellow-800
                                                        @elseif($offer->estado == 'accepted_by_seller') bg-blue-100 text-blue-800
                                                        @elseif($offer->estado == 'rejected') bg-red-100 text-red-800
                                                        @endif">
                                                        {{ ucfirst(str_replace('_', ' ', $offer->estado)) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    @if($offer->estado == 'pending')
                                                        <form action="{{ route('offers.accept', $offer) }}" method="POST" class="inline-block">
                                                            @csrf
                                                            <button type="submit" class="text-green-600 hover:text-green-900 mr-4">{{ __('Accept') }}</button>
                                                        </form>
                                                        <form action="{{ route('offers.reject', $offer) }}" method="POST" class="inline-block">
                                                            @csrf
                                                            <button type="submit" class="text-red-600 hover:text-red-900">{{ __('Reject') }}</button>
                                                        </form>
                                                    @elseif($offer->estado == 'accepted_by_seller')
                                                        <span class="text-gray-500 text-xs">{{ __('Waiting for payment') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 2. Ofertas Enviadas -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 bg-green-50">
                        <h3 class="text-lg font-medium text-green-900 mb-4">{{ __('Sent Offers (Purchases)') }}</h3>
                        @if($sentOffers->isEmpty())
                            <p class="text-gray-500">{{ __('No offers sent.') }}</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-green-100">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Car') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Seller') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($sentOffers as $offer)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $offer->car->title }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $offer->seller->nombre_contacto }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($offer->cantidad, 2) }} €</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        @if($offer->estado == 'pending') bg-yellow-100 text-yellow-800
                                                        @elseif($offer->estado == 'accepted_by_seller') bg-blue-100 text-blue-800
                                                        @elseif($offer->estado == 'rejected') bg-red-100 text-red-800
                                                        @endif">
                                                        {{ ucfirst(str_replace('_', ' ', $offer->estado)) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    @if($offer->estado == 'accepted_by_seller')
                                                        <form action="{{ route('offers.pay', $offer) }}" method="POST" class="inline-block">
                                                            @csrf
                                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
                                                                {{ __('Pay & Confirm') }}
                                                            </button>
                                                        </form>
                                                    @elseif($offer->estado == 'pending')
                                                        <span class="text-gray-500 text-xs">{{ __('Waiting for seller') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tab: Rentals -->
            <div x-show="activeTab === 'rentals'" class="space-y-8" style="display: none;">
                <!-- 4. Mis Alquileres (Cliente) -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('My Rentals') }} ({{ __('As Customer') }})</h3>
                        @if($rentals->isEmpty())
                            <p class="text-gray-500">{{ __('No rentals yet.') }}</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Car') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Period') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total Price') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Return Date') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($rentals as $rental)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $rental->car->title }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $rental->fecha_inicio->format('d/m/Y') }} - {{ $rental->fecha_fin->format('d/m/Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($rental->precio_total, 2) }} €</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-indigo-600 font-bold">
                                                    {{ $rental->fecha_fin->format('d/m/Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        @if($rental->id_estado == 1) bg-yellow-100 text-yellow-800
                                                        @elseif($rental->id_estado == 7) bg-blue-100 text-blue-800
                                                        @elseif($rental->id_estado == 2) bg-green-100 text-green-800
                                                        @elseif($rental->id_estado == 3) bg-green-100 text-green-800
                                                        @elseif($rental->id_estado == 4) bg-red-100 text-red-800
                                                        @elseif($rental->id_estado == 5) bg-gray-100 text-gray-800
                                                        @elseif($rental->id_estado == 6) bg-red-100 text-red-800
                                                        @endif">
                                                        {{ $rental->status->nombre }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    @if($rental->id_estado == 7) {{-- Aceptado, esperando pago --}}
                                                        <form action="{{ route('rentals.pay', $rental) }}" method="POST" class="inline-block">
                                                            @csrf
                                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition">
                                                                {{ __('Pay & Confirm') }}
                                                            </button>
                                                        </form>
                                                    @elseif($rental->id_estado == 1)
                                                        <span class="text-gray-500 text-xs">{{ __('Waiting for owner') }}</span>
                                                    @elseif(in_array($rental->id_estado, [2, 3, 4, 5]))
                                                        <a href="{{ route('rentals.receipt', $rental) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Download Receipt') }}</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 5. Mis Arrendamientos (Dueño) -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('My Leases') }} ({{ __('As Owner') }})</h3>
                        @if($myRentalsAsOwner->isEmpty())
                            <p class="text-gray-500">{{ __('No leases yet.') }}</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Car') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Customer') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Period') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Total Price') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($myRentalsAsOwner as $rental)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $rental->car->title }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $rental->customer->nombre_contacto }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    {{ $rental->fecha_inicio->format('d/m/Y') }} - {{ $rental->fecha_fin->format('d/m/Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($rental->precio_total, 2) }} €</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        @if($rental->id_estado == 1) bg-yellow-100 text-yellow-800
                                                        @elseif($rental->id_estado == 7) bg-blue-100 text-blue-800
                                                        @elseif($rental->id_estado == 2) bg-green-100 text-green-800
                                                        @elseif($rental->id_estado == 3) bg-green-100 text-green-800
                                                        @elseif($rental->id_estado == 4) bg-red-100 text-red-800
                                                        @elseif($rental->id_estado == 5) bg-gray-100 text-gray-800
                                                        @elseif($rental->id_estado == 6) bg-red-100 text-red-800
                                                        @endif">
                                                        {{ $rental->status->nombre }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    @if($rental->id_estado == 1)
                                                        <form action="{{ route('rentals.accept', $rental) }}" method="POST" class="inline-block">
                                                            @csrf
                                                            <button type="submit" class="text-green-600 hover:text-green-900 mr-4" onclick="return confirm('{{ __('Accept rental?') }}')">{{ __('Accept') }}</button>
                                                        </form>
                                                        <form action="{{ route('rentals.reject', $rental) }}" method="POST" class="inline-block">
                                                            @csrf
                                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('{{ __('Reject rental?') }}')">{{ __('Reject') }}</button>
                                                        </form>
                                                    @elseif(in_array($rental->id_estado, [2, 3, 4, 5]))
                                                        <a href="{{ route('rentals.receipt', $rental) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Download Receipt') }}</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tab: History -->
            <div x-show="activeTab === 'history'" class="space-y-8" style="display: none;">
                <!-- 3. Mis Compras (Cerradas) -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('My Purchases') }}</h3>
                        @if($purchases->isEmpty())
                            <p class="text-gray-500">{{ __('No purchases yet.') }}</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Car') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Seller') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Price') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($purchases as $sale)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $sale->vehiculo->title }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $sale->vendedor->nombre_contacto }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($sale->precio, 2) }} €</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $sale->created_at->format('d/m/Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('sales.receipt', $sale) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Download Receipt') }}</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 4. Mis Ventas (Cerradas) -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('My Sales') }}</h3>
                        @if($sales->isEmpty())
                            <p class="text-gray-500">{{ __('No sales yet.') }}</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Car') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Buyer') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Price') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($sales as $sale)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $sale->vehiculo->title }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $sale->comprador->nombre_contacto }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ number_format($sale->precio, 2) }} €</td>
                                                <td class="px-6 py-4 whitespace-nowrap">{{ $sale->created_at->format('d/m/Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <a href="{{ route('sales.receipt', $sale) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('Download Receipt') }}</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
