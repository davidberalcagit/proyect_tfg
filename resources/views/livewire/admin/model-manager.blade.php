<div class="p-6 bg-white rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Gestión de Modelos</h2>
        <button wire:click="create" class="bg-[#B35F12] hover:bg-[#9A5210] text-white font-bold py-2 px-4 rounded">Nuevo Modelo</button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('message') }}</div>
    @endif

    <table class="min-w-full table-auto">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">ID</th>
                <th class="px-4 py-2 text-left">Marca</th>
                <th class="px-4 py-2 text-left">Modelo</th>
                <th class="px-4 py-2 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($models as $model)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-2">{{ $model->id }}</td>
                    <td class="px-4 py-2">{{ $model->marca->nombre ?? 'N/A' }}</td>
                    <td class="px-4 py-2">{{ $model->nombre }}</td>
                    <td class="px-4 py-2 text-center">
                        <button wire:click="edit({{ $model->id }})" class="text-blue-600 mr-2">Editar</button>
                        <button wire:click="delete({{ $model->id }})" onclick="confirm('¿Seguro?') || event.stopImmediatePropagation()" class="text-red-600">Borrar</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">{{ $models->links() }}</div>

    @if($isModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full p-6 relative">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $model_id ? 'Editar' : 'Crear' }} Modelo</h3>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Marca</label>
                    <select wire:model="id_marca" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                        <option value="">Seleccione una marca</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}">{{ $brand->nombre }}</option>
                        @endforeach
                    </select>
                    @error('id_marca') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nombre del Modelo</label>
                    <input type="text" wire:model="nombre" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                    @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end">
                    <button wire:click="closeModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">Cancelar</button>
                    <button wire:click="store" class="bg-[#B35F12] hover:bg-[#9A5210] text-white font-bold py-2 px-4 rounded">Guardar</button>
                </div>
            </div>
        </div>
    @endif
</div>
