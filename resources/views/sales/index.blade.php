<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Transactions') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- 1. Ofertas Recibidas -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 border-b border-gray-200 bg-indigo-50">
                    <h3 class="text-lg font-medium text-indigo-900 mb-4">{{ __('Received Offers (Pending)') }}</h3>
                    @if($receivedOffers->isEmpty())
                        <p class="text-gray-500">{{ __('No pending offers received.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-indigo-100">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Car') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Buyer') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Offer Amount') }}</th>
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
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <form action="{{ route('offers.accept', $offer) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-900 mr-4" onclick="return confirm('{{ __('Accept offer?') }}')">{{ __('Accept') }}</button>
                                                </form>
                                                <form action="{{ route('offers.reject', $offer) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('{{ __('Reject offer?') }}')">{{ __('Reject') }}</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 2. Mis Compras -->
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
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($purchases as $sale)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $sale->vehiculo->title }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $sale->vendedor->nombre_contacto }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($sale->precio, 2) }} €</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $sale->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 3. Mis Ventas -->
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
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($sales as $sale)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $sale->vehiculo->title }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $sale->comprador->nombre_contacto }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($sale->precio, 2) }} €</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $sale->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 4. Mis Alquileres (Yo alquilé) -->
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
                                                    @if($rental->id_estado == 1) bg-orange-100 text-orange-800
                                                    @elseif($rental->id_estado == 2) bg-yellow-100 text-yellow-800
                                                    @elseif($rental->id_estado == 3) bg-blue-100 text-blue-800
                                                    @elseif($rental->id_estado == 4) bg-red-100 text-red-800
                                                    @elseif($rental->id_estado == 5) bg-green-100 text-green-800
                                                    @elseif($rental->id_estado == 6) bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ $rental->status->nombre }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- 5. Mis Arrendamientos (Yo alquilé a otros) -->
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
                                                    @if($rental->id_estado == 1) bg-orange-100 text-orange-800
                                                    @elseif($rental->id_estado == 2) bg-yellow-100 text-yellow-800
                                                    @elseif($rental->id_estado == 3) bg-blue-100 text-blue-800
                                                    @elseif($rental->id_estado == 4) bg-red-100 text-red-800
                                                    @elseif($rental->id_estado == 5) bg-green-100 text-green-800
                                                    @elseif($rental->id_estado == 6) bg-gray-100 text-gray-800
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
    </div>
</x-app-layout>
