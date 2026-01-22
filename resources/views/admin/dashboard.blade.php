<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Mensajes -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">¡Éxito!</strong>
                    <span class="block sm:inline">{{ session('success') }}</span>
                    @if(session('output'))
                        <pre class="mt-2 bg-gray-100 p-2 rounded text-xs">{{ session('output') }}</pre>
                    @endif
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error:</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Sección 1: Mantenimiento del Sistema -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Mantenimiento del Sistema</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <h4 class="font-bold text-gray-700">Limpiar Caché</h4>
                        <p class="text-xs text-gray-500 mb-3">Ejecuta cache:clear</p>
                        <form action="{{ route('admin.run-job') }}" method="POST">
                            @csrf
                            <input type="hidden" name="job" value="clear-cache">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded w-full text-sm">Ejecutar</button>
                        </form>
                    </div>
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <h4 class="font-bold text-gray-700">Optimizar</h4>
                        <p class="text-xs text-gray-500 mb-3">Ejecuta optimize:clear</p>
                        <form action="{{ route('admin.run-job') }}" method="POST">
                            @csrf
                            <input type="hidden" name="job" value="optimize">
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded w-full text-sm">Ejecutar</button>
                        </form>
                    </div>
                    <div class="border rounded-lg p-4 bg-gray-50">
                        <h4 class="font-bold text-gray-700">Procesar Cola</h4>
                        <p class="text-xs text-gray-500 mb-3">Ejecuta queue:work (manual)</p>
                        <form action="{{ route('admin.run-job') }}" method="POST">
                            @csrf
                            <input type="hidden" name="job" value="queue-work">
                            <button type="submit" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded w-full text-sm">Procesar</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sección 2: Jobs de Negocio (Asíncronos) -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Procesos en Segundo Plano (Colas)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border rounded-lg p-4 bg-blue-50">
                        <h4 class="font-bold text-blue-800">Procesar Imagen (Simulado)</h4>
                        <p class="text-xs text-gray-600 mb-3">Simula redimensionado y marca de agua en el primer coche.</p>
                        <form action="{{ route('admin.run-job') }}" method="POST">
                            @csrf
                            <input type="hidden" name="job" value="process-image">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-800 text-white font-bold py-2 px-4 rounded w-full text-sm">Encolar Job</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sección 3: Tareas de Mantenimiento (Síncronas) -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 border-b pb-2">Tareas Inmediatas (Síncronas)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border rounded-lg p-4 bg-yellow-50">
                        <h4 class="font-bold text-yellow-800">Limpiar Ofertas Rechazadas</h4>
                        <p class="text-xs text-gray-600 mb-3">Elimina ofertas rechazadas > 30 días.</p>
                        <form action="{{ route('admin.run-job') }}" method="POST">
                            @csrf
                            <input type="hidden" name="job" value="cleanup-offers">
                            <button type="submit" class="bg-yellow-600 hover:bg-yellow-800 text-white font-bold py-2 px-4 rounded w-full text-sm">Ejecutar Ahora</button>
                        </form>
                    </div>
                    <div class="border rounded-lg p-4 bg-yellow-50">
                        <h4 class="font-bold text-yellow-800">Auditar Precios</h4>
                        <p class="text-xs text-gray-600 mb-3">Busca coches con precio <= 0.</p>
                        <form action="{{ route('admin.run-job') }}" method="POST">
                            @csrf
                            <input type="hidden" name="job" value="audit-prices">
                            <button type="submit" class="bg-yellow-600 hover:bg-yellow-800 text-white font-bold py-2 px-4 rounded w-full text-sm">Ejecutar Ahora</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
