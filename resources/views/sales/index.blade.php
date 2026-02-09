<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-[#284961] leading-tight">
                {{ __('My Transactions') }}
            </h2>
            <a href="{{ route('sales.export') }}" class="inline-flex items-center px-4 py-2 bg-[#284961] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#1c3344] active:bg-[#1c3344] focus:outline-none focus:border-[#1c3344] focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                {{ __('Export Sales (CSV)') }}
            </a>
        </div>
    </x-slot>

    <div id="loading-overlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 hidden flex items-center justify-center cursor-wait">
        <div class="bg-white p-5 rounded-lg shadow-xl flex flex-col items-center">
            <svg class="animate-spin h-10 w-10 text-[#B35F12] mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700 font-semibold">{{ __('Processing...') }}</span>
        </div>
    </div>

    <div class="py-12" x-data="{ activeTab: 'offers' }">
        <div class="max-w-[95%] 2xl:max-w-[90%] mx-auto sm:px-6 lg:px-8 transition-all duration-300">

            <div class="flex space-x-4 mb-6 border-b border-gray-200 pb-4 overflow-x-auto">
                <button @click="activeTab = 'offers'"
                    :class="{ 'bg-[#B35F12] text-white shadow-md': activeTab === 'offers', 'bg-white text-gray-600 hover:bg-gray-50': activeTab !== 'offers' }"
                    class="px-6 py-2 rounded-full font-bold text-sm transition duration-200 ease-in-out border border-gray-200 whitespace-nowrap">
                    {{ __('Offers') }}
                </button>

                <button @click="activeTab = 'rentals'"
                    :class="{ 'bg-[#B35F12] text-white shadow-md': activeTab === 'rentals', 'bg-white text-gray-600 hover:bg-gray-50': activeTab !== 'rentals' }"
                    class="px-6 py-2 rounded-full font-bold text-sm transition duration-200 ease-in-out border border-gray-200 whitespace-nowrap">
                    {{ __('Rentals') }}
                </button>

                <button @click="activeTab = 'history'"
                    :class="{ 'bg-[#B35F12] text-white shadow-md': activeTab === 'history', 'bg-white text-gray-600 hover:bg-gray-50': activeTab !== 'history' }"
                    class="px-6 py-2 rounded-full font-bold text-sm transition duration-200 ease-in-out border border-gray-200 whitespace-nowrap">
                    {{ __('History') }}
                </button>
            </div>

            <div x-show="activeTab === 'offers'" class="space-y-8" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">


                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-t-4 border-[#284961]">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-[#284961] mb-4 flex items-center">
                            {{ __('Received Offers (Sales)') }}
                        </h3>
                        @if($receivedOffers->isEmpty())
                            <p class="text-gray-500 italic">{{ __('No offers received.') }}</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Car') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Buyer') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($receivedOffers as $offer)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <a href="{{ route('cars.show', $offer->car) }}" class="text-[#B35F12] font-bold hover:underline">{{ Str::limit($offer->car->title, 30) }}</a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $offer->buyer->nombre_contacto }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap font-mono text-[#284961] font-bold">{{ number_format($offer->cantidad, 2) }} €</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        @if($offer->estado == 'pending') bg-yellow-100 text-yellow-800
                                                        @elseif($offer->estado == 'accepted_by_seller') bg-blue-100 text-blue-800
                                                        @elseif($offer->estado == 'rejected') bg-red-100 text-red-800
                                                        @endif">
                                                        {{ ucfirst(str_replace('_', ' ', $offer->estado)) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    @if($offer->estado == 'pending')
                                                        <form action="{{ route('offers.accept', $offer) }}" method="POST" class="inline-block action-form">
                                                            @csrf
                                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-bold mr-2 transition shadow">{{ __('Accept') }}</button>
                                                        </form>
                                                        <form action="{{ route('offers.reject', $offer) }}" method="POST" class="inline-block action-form">
                                                            @csrf
                                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-bold transition shadow">{{ __('Reject') }}</button>
                                                        </form>
                                                    @elseif($offer->estado == 'accepted_by_seller')
                                                        <span class="text-gray-500 text-xs italic">{{ __('Waiting for payment') }}</span>
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

                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-t-4 border-[#B35F12]">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-[#B35F12] mb-4 flex items-center">
                            {{ __('Sent Offers (Purchases)') }}
                        </h3>
                        @if($sentOffers->isEmpty())
                            <p class="text-gray-500 italic">{{ __('No offers sent.') }}</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Car') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Seller') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Amount') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($sentOffers as $offer)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <a href="{{ route('cars.show', $offer->car) }}" class="text-[#B35F12] font-bold hover:underline">{{ Str::limit($offer->car->title, 30) }}</a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $offer->seller->nombre_contacto }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap font-mono text-[#284961] font-bold">{{ number_format($offer->cantidad, 2) }} €</td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        @if($offer->estado == 'pending') bg-yellow-100 text-yellow-800
                                                        @elseif($offer->estado == 'accepted_by_seller') bg-blue-100 text-blue-800
                                                        @elseif($offer->estado == 'rejected') bg-red-100 text-red-800
                                                        @endif">
                                                        {{ ucfirst(str_replace('_', ' ', $offer->estado)) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    @if($offer->estado == 'accepted_by_seller')
                                                        <form action="{{ route('offers.pay', $offer) }}" method="POST" class="inline-block action-form">
                                                            @csrf
                                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow font-bold transition">
                                                                {{ __('Pay & Confirm') }}
                                                            </button>
                                                        </form>
                                                    @elseif($offer->estado == 'pending')
                                                        <span class="text-gray-500 text-xs italic">{{ __('Waiting for seller') }}</span>
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

            <div x-show="activeTab === 'rentals'" class="space-y-8" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-t-4 border-[#284961]">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-[#284961] mb-4">{{ __('My Rentals') }} ({{ __('As Customer') }})</h3>
                        @if($rentals->isEmpty())
                            <p class="text-gray-500 italic">{{ __('No rentals yet.') }}</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Car') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Period') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Total Price') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Return Date') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($rentals as $rental)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <a href="{{ route('cars.show', $rental->car) }}" class="text-[#B35F12] font-bold hover:underline">{{ Str::limit($rental->car->title, 30) }}</a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                                                    {{ $rental->fecha_inicio->format('d/m/Y') }} - {{ $rental->fecha_fin->format('d/m/Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap font-mono text-[#284961] font-bold">{{ number_format($rental->precio_total, 2) }} €</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-[#284961] font-bold">
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
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    @if($rental->id_estado == 7)
                                                        <form action="{{ route('rentals.pay', $rental) }}" method="POST" class="inline-block action-form">
                                                            @csrf
                                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-bold transition shadow">
                                                                {{ __('Pay & Confirm') }}
                                                            </button>
                                                        </form>
                                                    @elseif($rental->id_estado == 1)
                                                        <span class="text-gray-500 text-xs italic">{{ __('Waiting for owner') }}</span>
                                                    @elseif(in_array($rental->id_estado, [2, 3, 4, 5]))
                                                        <a href="{{ route('rentals.receipt', $rental) }}" class="text-[#284961] hover:text-[#4C86B3] font-bold">{{ __('Download Receipt') }}</a>
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

                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-t-4 border-[#B35F12]">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-[#B35F12] mb-4">{{ __('My Leases') }} ({{ __('As Owner') }})</h3>
                        @if($myRentalsAsOwner->isEmpty())
                            <p class="text-gray-500 italic">{{ __('No leases yet.') }}</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Car') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Customer') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Period') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Total Price') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($myRentalsAsOwner as $rental)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <a href="{{ route('cars.show', $rental->car) }}" class="text-[#B35F12] font-bold hover:underline">{{ Str::limit($rental->car->title, 30) }}</a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $rental->customer->nombre_contacto }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                                    {{ $rental->fecha_inicio->format('d/m/Y') }} - {{ $rental->fecha_fin->format('d/m/Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap font-mono text-[#284961] font-bold">{{ number_format($rental->precio_total, 2) }} €</td>
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
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    @if($rental->id_estado == 1)
                                                        <form action="{{ route('rentals.accept', $rental) }}" method="POST" class="inline-block action-form">
                                                            @csrf
                                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-bold mr-2 transition shadow" onclick="return confirm('{{ __('Accept rental?') }}')">{{ __('Accept') }}</button>
                                                        </form>
                                                        <form action="{{ route('rentals.reject', $rental) }}" method="POST" class="inline-block action-form">
                                                            @csrf
                                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs font-bold transition shadow" onclick="return confirm('{{ __('Reject rental?') }}')">{{ __('Reject') }}</button>
                                                        </form>
                                                    @elseif(in_array($rental->id_estado, [2, 3, 4, 5]))
                                                        <a href="{{ route('rentals.receipt', $rental) }}" class="text-[#284961] hover:text-[#4C86B3] font-bold">{{ __('Download Receipt') }}</a>
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

            <div x-show="activeTab === 'history'" class="space-y-8" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-t-4 border-[#284961]">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-[#284961] mb-4">{{ __('My Purchases') }}</h3>
                        @if($purchases->isEmpty())
                            <p class="text-gray-500 italic">{{ __('No purchases yet.') }}</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Car') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Seller') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Price') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($purchases as $sale)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <a href="{{ route('cars.show', $sale->vehiculo) }}" class="text-[#B35F12] font-bold hover:underline">{{ Str::limit($sale->vehiculo->title, 30) }}</a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $sale->vendedor->nombre_contacto }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap font-mono text-[#284961] font-bold">{{ number_format($sale->precio, 2) }} €</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $sale->created_at->format('d/m/Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="{{ route('sales.receipt', $sale) }}" class="text-[#284961] hover:text-[#4C86B3] font-bold">{{ __('Download Receipt') }}</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg border-t-4 border-[#B35F12]">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-[#B35F12] mb-4">{{ __('My Sales') }}</h3>
                        @if($sales->isEmpty())
                            <p class="text-gray-500 italic">{{ __('No sales yet.') }}</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Car') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Buyer') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Price') }}</th>
                                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($sales as $sale)
                                            <tr class="hover:bg-gray-50 transition">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <a href="{{ route('cars.show', $sale->vehiculo) }}" class="text-[#B35F12] font-bold hover:underline">{{ Str::limit($sale->vehiculo->title, 30) }}</a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $sale->comprador->nombre_contacto }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap font-mono text-[#284961] font-bold">{{ number_format($sale->precio, 2) }} €</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $sale->created_at->format('d/m/Y') }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <a href="{{ route('sales.receipt', $sale) }}" class="text-[#284961] hover:text-[#4C86B3] font-bold">{{ __('Download Receipt') }}</a>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.querySelectorAll('form.action-form');
            const overlay = document.getElementById('loading-overlay');

            forms.forEach(form => {
                form.addEventListener('submit', function () {
                    overlay.classList.remove('hidden');
                    document.body.classList.add('cursor-wait'); // Añadir cursor wait al body
                });
            });
        });
    </script>
</x-app-layout>
