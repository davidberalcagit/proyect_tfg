<div class="p-4 sm:p-6 bg-white rounded-lg shadow-md">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-xl font-bold text-[#284961]">Gestión de Marcas</h2>
        <button wire:click="create" class="w-full sm:w-auto bg-[#B35F12] hover:bg-[#9A5210] text-white font-bold py-2 px-4 rounded shadow transition duration-150 ease-in-out">
            + Nueva Marca
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3" wire:click="$set('message', null)">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Cerrar</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
            </span>
        </div>
    @endif

    <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-[#284961] text-white">
                <tr>
                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider w-20">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nombre</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider w-48">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($brands as $brand)
                    <tr wire:key="brand-{{ $brand->id }}" class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            {{ $brand->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $brand->nombre }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-3">
                                <button wire:click="edit({{ $brand->id }})" class="text-[#284961] hover:text-[#1c3344] font-bold transition">
                                    Editar
                                </button>
                                <button wire:click="delete({{ $brand->id }})"
                                        wire:confirm="¿Estás seguro de que quieres eliminar esta marca? Esta acción no se puede deshacer."
                                        class="text-red-600 hover:text-red-800 font-bold transition">
                                    Borrar
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <div class="mt-6">
        {{ $brands->links() }}
    </div>

    <!-- Modal para CREAR / EDITAR -->
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

            <!-- Modal Panel -->
            <div class="bg-white rounded-xl overflow-hidden shadow-2xl transform transition-all sm:max-w-lg w-full p-6 relative z-10 border border-gray-200">
                <h3 class="text-xl font-bold text-[#284961] mb-4" id="modal-title">
                    {{ $brand_id ? 'Editar Marca' : 'Crear Nueva Marca' }}
                </h3>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nombre de la Marca</label>
                    <input type="text" wire:model="nombre" wire:keydown.enter="store" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#B35F12] focus:ring focus:ring-[#B35F12] focus:ring-opacity-50 py-2 px-3" placeholder="Ej: Toyota" autofocus>
                    @error('nombre') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <button wire:click="closeModal" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition">
                        Cancelar
                    </button>
                    <button wire:click="store" class="bg-[#B35F12] hover:bg-[#9A5210] text-white font-bold py-2 px-4 rounded-lg shadow transition">
                        {{ $brand_id ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
