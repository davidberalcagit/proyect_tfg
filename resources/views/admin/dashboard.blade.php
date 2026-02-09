<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <!-- Overlay de Carga Global (Funciona para Forms y Livewire) -->
    <div id="loading-overlay" class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 hidden flex items-center justify-center cursor-wait" wire:loading.class.remove="hidden" wire:target>
        <div class="bg-white p-5 rounded-lg shadow-xl flex flex-col items-center">
            <svg class="animate-spin h-10 w-10 text-[#B35F12] mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-700 font-semibold">{{ __('Processing...') }}</span>
        </div>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Mensajes de éxito/error -->
            @if (session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <p class="font-bold">¡Éxito!</p>
                    <p>{{ session('success') }}</p>
                    @if(session('output'))
                        <div class="mt-2 bg-gray-800 text-green-400 p-3 rounded text-xs font-mono overflow-x-auto">
                            {{ session('output') }}
                        </div>
                    @endif
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
                    <p class="font-bold">Error</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <!-- 1. Sección de Estadísticas -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-8 border-t-4 border-[#284961]">
                <h3 class="text-lg font-bold text-[#284961] mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Resumen del Sistema
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex items-center justify-between">
                        <div>
                            <span class="text-gray-500 text-sm font-medium uppercase">Usuarios</span>
                            <span class="block text-3xl font-bold text-[#284961]">{{ \App\Models\User::count() }}</span>
                        </div>
                        <div class="p-3 bg-blue-100 rounded-full text-[#284961]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex items-center justify-between">
                        <div>
                            <span class="text-gray-500 text-sm font-medium uppercase">Coches</span>
                            <span class="block text-3xl font-bold text-[#284961]">{{ \App\Models\Cars::count() }}</span>
                        </div>
                        <div class="p-3 bg-green-100 rounded-full text-green-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex items-center justify-between">
                        <div>
                            <span class="text-gray-500 text-sm font-medium uppercase">Ventas</span>
                            <span class="block text-3xl font-bold text-[#284961]">{{ \App\Models\Sales::count() }}</span>
                        </div>
                        <div class="p-3 bg-yellow-100 rounded-full text-yellow-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Gestión de Tablas Auxiliares (Livewire CRUDs) -->
            @livewire('admin.dashboard')

            <!-- 3. Acciones del Sistema (Mantenimiento) -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-8 border-t-4 border-[#B35F12]">
                <h3 class="text-lg font-bold text-[#B35F12] mb-6 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Mantenimiento y Acciones
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <!-- Grupo: Mantenimiento -->
                    <div class="col-span-full mb-2 border-b pb-2 text-gray-500 font-semibold text-sm uppercase tracking-wider">Mantenimiento del Servidor</div>

                    <!-- Limpiar Caché -->
                    <form action="{{ route('admin.run-job') }}" method="POST" class="admin-action-form bg-gray-50 p-4 rounded-lg border border-gray-200 hover:shadow-md transition flex flex-col justify-between">
                        @csrf
                        <input type="hidden" name="job" value="clear-cache">
                        <div class="mb-4">
                            <h4 class="font-bold text-gray-800">Limpiar Caché</h4>
                            <p class="text-sm text-gray-600 mt-1">Elimina la caché de la aplicación para refrescar configuraciones.</p>
                        </div>
                        <button type="submit" class="w-full bg-[#284961] hover:bg-[#1c3344] text-white font-bold py-2 px-4 rounded transition text-sm">
                            Ejecutar
                        </button>
                    </form>

                    <!-- Optimizar -->
                    <form action="{{ route('admin.run-job') }}" method="POST" class="admin-action-form bg-gray-50 p-4 rounded-lg border border-gray-200 hover:shadow-md transition flex flex-col justify-between">
                        @csrf
                        <input type="hidden" name="job" value="optimize">
                        <div class="mb-4">
                            <h4 class="font-bold text-gray-800">Optimizar Sistema</h4>
                            <p class="text-sm text-gray-600 mt-1">Reconstruye la caché y optimiza la carga de clases y rutas.</p>
                        </div>
                        <button type="submit" class="w-full bg-[#B35F12] hover:bg-[#9A5210]] text-white font-bold py-2 px-4 rounded transition text-sm">
                            Ejecutar
                        </button>
                    </form>

                    <!-- Ejecutar Cola -->
                    <form action="{{ route('admin.run-job') }}" method="POST" class="admin-action-form bg-gray-50 p-4 rounded-lg border border-gray-200 hover:shadow-md transition flex flex-col justify-between">
                        @csrf
                        <input type="hidden" name="job" value="queue-work">
                        <div class="mb-4">
                            <h4 class="font-bold text-gray-800">Procesar Cola</h4>
                            <p class="text-sm text-gray-600 mt-1">Ejecuta trabajos pendientes en la cola (emails, procesos en segundo plano).</p>
                        </div>
                        <button type="submit" class="w-full bg-[#284961] hover:bg-[#1c3344] text-white font-bold py-2 px-4 rounded transition text-sm">
                            Ejecutar
                        </button>
                    </form>

                    <!-- Grupo: Datos -->
                    <div class="col-span-full mt-4 mb-2 border-b pb-2 text-gray-500 font-semibold text-sm uppercase tracking-wider">Gestión de Datos</div>

                    <!-- Revisar Alquileres -->
                    <form action="{{ route('admin.run-job') }}" method="POST" class="admin-action-form bg-gray-50 p-4 rounded-lg border border-gray-200 hover:shadow-md transition flex flex-col justify-between">
                        @csrf
                        <input type="hidden" name="job" value="check-rentals">
                        <div class="mb-4">
                            <h4 class="font-bold text-gray-800">Revisar Alquileres</h4>
                            <p class="text-sm text-gray-600 mt-1">Verifica estados de alquileres y actualiza disponibilidades.</p>
                        </div>
                        <button type="submit" class="w-full bg-[#284961] hover:bg-[#1c3344]] text-white  font-bold py-2 px-4 rounded transition text-sm">
                            Ejecutar
                        </button>
                    </form>

                    <!-- Limpiar Imágenes -->
                    <form action="{{ route('admin.run-job') }}" method="POST" class="admin-action-form bg-gray-50 p-4 rounded-lg border border-gray-200 hover:shadow-md transition flex flex-col justify-between">
                        @csrf
                        <input type="hidden" name="job" value="cleanup-images">
                        <div class="mb-4">
                            <h4 class="font-bold text-gray-800">Limpiar Imágenes</h4>
                            <p class="text-sm text-gray-600 mt-1">Elimina imágenes huérfanas que no están asociadas a ningún coche.</p>
                        </div>
                        <button type="submit" class="w-full bg-white border border-red-500 text-red-600 hover:bg-red-50 font-bold py-2 px-4 rounded transition text-sm">
                            Ejecutar
                        </button>
                    </form>

                    <!-- Limpiar Ofertas -->
                    <form action="{{ route('admin.run-job') }}" method="POST" class="admin-action-form bg-gray-50 p-4 rounded-lg border border-gray-200 hover:shadow-md transition flex flex-col justify-between">
                        @csrf
                        <input type="hidden" name="job" value="cleanup-offers">
                        <div class="mb-4">
                            <h4 class="font-bold text-gray-800">Limpiar Ofertas</h4>
                            <p class="text-sm text-gray-600 mt-1">Borra ofertas rechazadas antiguas para liberar espacio.</p>
                        </div>
                        <button type="submit" class="w-full bg-white border border-orange-500 text-orange-600 hover:bg-orange-50 font-bold py-2 px-4 rounded transition text-sm">
                            Ejecutar
                        </button>
                    </form>

                    <!-- Auditar Precios -->
                    <form action="{{ route('admin.run-job') }}" method="POST" class="admin-action-form bg-gray-50 p-4 rounded-lg border border-gray-200 hover:shadow-md transition flex flex-col justify-between">
                        @csrf
                        <input type="hidden" name="job" value="audit-prices">
                        <div class="mb-4">
                            <h4 class="font-bold text-gray-800">Auditar Precios</h4>
                            <p class="text-sm text-gray-600 mt-1">Genera un reporte de precios anómalos o fuera de rango.</p>
                        </div>
                        <button type="submit" class="w-full bg-white border border-[#284961] text-[#284961] hover:bg-gray-100 font-bold py-2 px-4 rounded transition text-sm">
                            Ejecutar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const forms = document.querySelectorAll('.admin-action-form');
            const overlay = document.getElementById('loading-overlay');

            // Mostrar overlay en submit de formularios normales
            forms.forEach(form => {
                form.addEventListener('submit', function () {
                    overlay.classList.remove('hidden');
                    document.body.classList.add('cursor-wait');
                });
            });

            // Mostrar overlay en acciones de Livewire
            if (typeof Livewire !== 'undefined') {
                Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
                    overlay.classList.remove('hidden');
                    document.body.classList.add('cursor-wait');

                    succeed(() => {
                        overlay.classList.add('hidden');
                        document.body.classList.remove('cursor-wait');
                    });

                    fail(() => {
                        overlay.classList.add('hidden');
                        document.body.classList.remove('cursor-wait');
                    });
                });
            }
        });
    </script>
</x-app-layout>
