<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Mensajes de éxito/error -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Success!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                    @if(session('output'))
                        <pre class="mt-2 text-xs bg-gray-100 p-2 rounded">{{ session('output') }}</pre>
                    @endif
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Sección de Estadísticas -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Resumen del Sistema</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-100 p-4 rounded-lg">
                        <span class="block text-2xl font-bold">{{ \App\Models\User::count() }}</span>
                        <span class="text-gray-600">Usuarios</span>
                    </div>
                    <div class="bg-green-100 p-4 rounded-lg">
                        <span class="block text-2xl font-bold">{{ \App\Models\Cars::count() }}</span>
                        <span class="text-gray-600">Coches</span>
                    </div>
                    <div class="bg-yellow-100 p-4 rounded-lg">
                        <span class="block text-2xl font-bold">{{ \App\Models\Sales::count() }}</span>
                        <span class="text-gray-600">Ventas</span>
                    </div>
                </div>
            </div>

            <!-- Acciones del Sistema (Restaurado) -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Acciones del Sistema</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <form action="{{ route('admin.run-job') }}" method="POST">
                        @csrf
                        <input type="hidden" name="job" value="clear-cache">
                        <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Limpiar Caché
                        </button>
                    </form>
                    <form action="{{ route('admin.run-job') }}" method="POST">
                        @csrf
                        <input type="hidden" name="job" value="optimize">
                        <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                            Optimizar
                        </button>
                    </form>
                    <form action="{{ route('admin.run-job') }}" method="POST">
                        @csrf
                        <input type="hidden" name="job" value="queue-work">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Ejecutar Cola
                        </button>
                    </form>
                    <form action="{{ route('admin.run-job') }}" method="POST">
                        @csrf
                        <input type="hidden" name="job" value="check-rentals">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Revisar Alquileres
                        </button>
                    </form>
                    <form action="{{ route('admin.run-job') }}" method="POST">
                        @csrf
                        <input type="hidden" name="job" value="cleanup-images">
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Limpiar Imágenes
                        </button>
                    </form>
                    <form action="{{ route('admin.run-job') }}" method="POST">
                        @csrf
                        <input type="hidden" name="job" value="process-image">
                        <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                            Procesar Imagen (Test)
                        </button>
                    </form>
                    <form action="{{ route('admin.run-job') }}" method="POST">
                        @csrf
                        <input type="hidden" name="job" value="cleanup-offers">
                        <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded">
                            Limpiar Ofertas
                        </button>
                    </form>
                    <form action="{{ route('admin.run-job') }}" method="POST">
                        @csrf
                        <input type="hidden" name="job" value="audit-prices">
                        <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold py-2 px-4 rounded">
                            Auditar Precios
                        </button>
                    </form>
                </div>
            </div>

            <!-- Gestión de Tablas Auxiliares (Livewire CRUDs) -->
            <div x-data="{ activeTab: 'brands' }" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex" aria-label="Tabs">
                        <button @click="activeTab = 'brands'" :class="{'border-[#B35F12] text-[#B35F12]': activeTab === 'brands', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'brands'}" class="w-1/5 py-4 px-1 text-center border-b-2 font-medium text-sm">
                            Marcas
                        </button>
                        <button @click="activeTab = 'models'" :class="{'border-[#B35F12] text-[#B35F12]': activeTab === 'models', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'models'}" class="w-1/5 py-4 px-1 text-center border-b-2 font-medium text-sm">
                            Modelos
                        </button>
                        <button @click="activeTab = 'fuels'" :class="{'border-[#B35F12] text-[#B35F12]': activeTab === 'fuels', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'fuels'}" class="w-1/5 py-4 px-1 text-center border-b-2 font-medium text-sm">
                            Combustibles
                        </button>
                        <button @click="activeTab = 'colors'" :class="{'border-[#B35F12] text-[#B35F12]': activeTab === 'colors', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'colors'}" class="w-1/5 py-4 px-1 text-center border-b-2 font-medium text-sm">
                            Colores
                        </button>
                        <button @click="activeTab = 'gears'" :class="{'border-[#B35F12] text-[#B35F12]': activeTab === 'gears', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'gears'}" class="w-1/5 py-4 px-1 text-center border-b-2 font-medium text-sm">
                            Marchas
                        </button>
                    </nav>
                </div>

                <div class="p-6">
                    <div x-show="activeTab === 'brands'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100">
                        @livewire('admin.brand-manager')
                    </div>
                    <div x-show="activeTab === 'models'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100" style="display: none;">
                        @livewire('admin.model-manager')
                    </div>
                    <div x-show="activeTab === 'fuels'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100" style="display: none;">
                        @livewire('admin.fuel-manager')
                    </div>
                    <div x-show="activeTab === 'colors'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100" style="display: none;">
                        @livewire('admin.color-manager')
                    </div>
                    <div x-show="activeTab === 'gears'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-90" x-transition:enter-end="opacity-100 transform scale-100" style="display: none;">
                        @livewire('admin.gear-manager')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
