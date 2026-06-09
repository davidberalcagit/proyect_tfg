<div class="p-4 sm:p-6 bg-white rounded-lg shadow-md">
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-xl font-bold text-[#284961]">Gestión de Modelos</h2>
    </div>

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
            <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" @click="show = false">
                <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Cerrar</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
            </span>
        </div>
    @endif

    <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-[#284961] text-white">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Marca</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Modelo</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider w-48">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                {{-- Formulario Crear --}}
                <tr>
                    <td class="px-6 py-4">
                        <select wire:model="newModelBrandId" class="w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="">Selecciona Marca</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->nombre }}</option>
                            @endforeach
                        </select>
                        @error('newModelBrandId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </td>
                    <td class="px-6 py-4">
                        <input type="text" wire:model="newModelName" wire:keydown.enter="store" class="w-full border-gray-300 rounded-lg shadow-sm" placeholder="Nuevo modelo...">
                        @error('newModelName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </td>
                    <td class="px-6 py-4 text-right">
                        <button wire:click="store" class="bg-[#B35F12] hover:bg-[#9A5210] text-white font-bold py-2 px-4 rounded">Crear</button>
                    </td>
                </tr>

                {{-- Listado --}}
                @foreach($models as $model)
                    <tr wire:key="model-{{ $model->id }}" class="hover:bg-gray-50 transition duration-150">
                        @if($editingModelId === $model->id)
                            <td class="px-6 py-4">
                                <select wire:model="editingModelBrandId" class="w-full border-gray-300 rounded-lg shadow-sm">
                                    <option value="">Selecciona Marca</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('editingModelBrandId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </td>
                            <td class="px-6 py-4">
                                <input type="text" wire:model="editingModelName" wire:keydown.enter="update" class="w-full border-gray-300 rounded-lg shadow-sm">
                                @error('editingModelName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </td>
                            <td class="px-6 py-4 text-right font-medium">
                                <button wire:click="update" class="text-green-600 hover:text-green-800 font-bold">Guardar</button>
                                <button wire:click="cancelEdit" class="ml-2 text-gray-600 hover:text-gray-800 font-bold">Cancelar</button>
                            </td>
                        @else
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $model->marca->nombre ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $model->nombre }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="edit({{ $model->id }})" class="text-[#284961] hover:text-[#1c3344] font-bold">Editar</button>
                                <button wire:click="delete({{ $model->id }})" wire:confirm="¿Seguro que quieres eliminar este modelo?" class="ml-2 text-red-600 hover:text-red-800 font-bold">Borrar</button>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        {{ $models->links() }}
    </div>
</div>
