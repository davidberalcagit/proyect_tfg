<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Supervisor Dashboard') }}
            </h2>
            <a href="{{ route('supervisor.report') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                {{ __('Download Report') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Mensajes -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">¡Éxito!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Estadísticas Generales -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm uppercase font-bold">Pendientes Revisión</div>
                    <div class="text-3xl font-bold text-orange-600">{{ $stats['pending_cars_count'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm uppercase font-bold">Total Coches</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['total_cars'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm uppercase font-bold">En Venta</div>
                    <div class="text-3xl font-bold text-green-600">{{ $stats['cars_for_sale'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div class="text-gray-500 text-sm uppercase font-bold">Ventas Totales</div>
                    <div class="text-3xl font-bold text-purple-600">{{ $stats['total_sales'] }}</div>
                </div>
            </div>

            <!-- Tabla de Coches Pendientes -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-8">
                <div class="p-6 border-b border-gray-200 bg-orange-50">
                    <h3 class="text-lg font-medium text-orange-800">Coches Pendientes de Aprobación</h3>
                </div>
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendedor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Marca/Modelo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($pendingCars as $car)
                                <tr x-data="{ showRejectForm: false }">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('cars.show', $car) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                            {{ $car->title }}
                                        </a>
                                        <div class="text-xs text-gray-500">{{ $car->matricula }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $car->vendedor->nombre_contacto ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($car->temp_brand)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Nueva Marca: {{ $car->temp_brand }}
                                            </span>
                                        @else
                                            {{ $car->marca->nombre ?? '-' }}
                                        @endif
                                        <br>
                                        @if($car->temp_model)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Nuevo Modelo: {{ $car->temp_model }}
                                            </span>
                                        @else
                                            {{ $car->modelo->nombre ?? '-' }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($car->precio, 2) }} €</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2" x-show="!showRejectForm">
                                            <form action="{{ route('supervisor.approve', $car->id) }}" method="POST">
                                                @csrf
                                                <x-button class="bg-green-600 hover:bg-green-700 active:bg-green-900 border-green-600 focus:border-green-900 ring-green-300">
                                                    Aprobar
                                                </x-button>
                                            </form>
                                            <x-danger-button @click="showRejectForm = true">
                                                Rechazar
                                            </x-danger-button>
                                        </div>

                                        <!-- Formulario de Rechazo -->
                                        <div x-show="showRejectForm" class="mt-2 text-left bg-red-50 p-3 rounded-md border border-red-200" style="display: none;">
                                            <form action="{{ route('supervisor.reject', $car->id) }}" method="POST">
                                                @csrf
                                                <div class="mb-2">
                                                    <x-label for="reason" value="Razón del rechazo:" class="text-red-700" />
                                                    <x-textarea name="reason" id="reason" class="block w-full text-xs mt-1" rows="2" required placeholder="Escribe la razón aquí..."></x-textarea>
                                                </div>
                                                <div class="flex justify-end space-x-2">
                                                    <x-secondary-button @click="showRejectForm = false" type="button" class="text-xs px-2 py-1">
                                                        Cancelar
                                                    </x-secondary-button>
                                                    <x-danger-button type="submit" class="text-xs px-2 py-1">
                                                        Confirmar
                                                    </x-danger-button>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay coches pendientes de revisión.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabla de Ventas Recientes -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Ventas Recientes</h3>
                </div>
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehículo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendedor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Precio</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($stats['recent_sales'] as $sale)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $sale->vehiculo->title ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $sale->vendedor->nombre_contacto ?? 'N/A' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($sale->precio, 2) }} €</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $sale->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No hay ventas recientes.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
