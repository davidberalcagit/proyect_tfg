<div class="p-6 bg-white rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-[#284961]">Gestión de Combustibles</h2>
        <button wire:click="create" class="bg-[#B35F12] hover:bg-[#9A5210] text-white font-bold py-2 px-4 rounded shadow transition duration-150 ease-in-out">
            + Nuevo Combustible
        </button>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm" role="alert">
            <p class="font-bold">Éxito</p>
            <p>{{ session('message') }}</p>
        </div>
    @endif

    <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-[#284961] text-white">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider w-20">ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">Nombre</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider w-48">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($fuels as $fuel)
                    <tr wire:key="fuel-{{ $fuel->id }}" class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $fuel->id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $fuel->nombre }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-3">
                                <button wire:click="edit({{ $fuel->id }})" class="text-[#284961] hover:text-[#1c3344] font-bold transition">
                                    Editar
                                </button>
                                <button wire:click="delete({{ $fuel->id }})"
                                        onclick="confirm('¿Seguro que quieres borrar este combustible?') || event.stopImmediatePropagation()"
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

    <div class="mt-6">
        {{ $fuels->links() }}
    </div>

    @if($isModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center backdrop-blur-sm">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"></div>
            <div class="bg-white rounded-xl overflow-hidden shadow-2xl transform transition-all sm:max-w-lg sm:w-full p-6 relative z-10 border border-gray-200">
                <h3 class="text-xl font-bold text-[#284961] mb-4">{{ $fuel_id ? 'Editar' : 'Crear' }} Combustible</h3>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nombre</label>
                    <input type="text" wire:model="nombre" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#B35F12] focus:ring focus:ring-[#B35F12] focus:ring-opacity-50 py-2 px-3">
                    @error('nombre') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <button wire:click="closeModal" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition">Cancelar</button>
                    <button wire:click="store" class="bg-[#B35F12] hover:bg-[#9A5210] text-white font-bold py-2 px-4 rounded-lg shadow transition">Guardar</button>
                </div>
            </div>
        </div>
    @endif
</div>
