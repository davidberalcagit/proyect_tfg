<div class="p-6 bg-white rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Gestión de Colores</h2>
        <button wire:click="create" class="bg-[#B35F12] hover:bg-[#9A5210] text-white font-bold py-2 px-4 rounded">Nuevo</button>
    </div>
    @if (session()->has('message')) <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">{{ session('message') }}</div> @endif
    <table class="min-w-full table-auto">
        <thead class="bg-gray-100"><tr><th class="px-4 py-2">ID</th><th class="px-4 py-2">Nombre</th><th class="px-4 py-2 text-center">Acciones</th></tr></thead>
        <tbody>
            @foreach($colors as $color)
                <tr class="border-b hover:bg-gray-50"><td class="px-4 py-2">{{ $color->id }}</td><td class="px-4 py-2">{{ $color->nombre }}</td>
                <td class="px-4 py-2 text-center"><button wire:click="edit({{ $color->id }})" class="text-blue-600 mr-2">Editar</button><button wire:click="delete({{ $color->id }})" onclick="confirm('¿Seguro?') || event.stopImmediatePropagation()" class="text-red-600">Borrar</button></td></tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">{{ $colors->links() }}</div>
    @if($isModalOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-500 bg-opacity-75">
            <div class="bg-white rounded-lg p-6 w-full max-w-md">
                <h3 class="text-lg font-medium mb-4">{{ $color_id ? 'Editar' : 'Crear' }} Color</h3>
                <input type="text" wire:model="nombre" class="border rounded w-full py-2 px-3 mb-4" placeholder="Nombre">
                @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                <div class="flex justify-end"><button wire:click="closeModal" class="bg-gray-300 px-4 py-2 rounded mr-2">Cancelar</button><button wire:click="store" class="bg-[#B35F12] text-white px-4 py-2 rounded">Guardar</button></div>
            </div>
        </div>
    @endif
</div>
