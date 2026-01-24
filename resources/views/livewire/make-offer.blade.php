<div>
    <button wire:click="openModal" class="bg-[#284961] hover:bg-[#1c3344] text-white font-bold py-2 px-4 rounded w-full">
        Hacer Oferta
    </button>

    @if($isModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75"></div>
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-md sm:w-full p-6 relative">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Ofertar por {{ $car->title }}</h3>

                @if (session()->has('error'))
                    <div class="text-red-500 text-sm mb-2">{{ session('error') }}</div>
                @endif

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tu Oferta (€)</label>
                    <input type="number" wire:model="cantidad" class="shadow border rounded w-full py-2 px-3 text-gray-700">
                    @error('cantidad') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end">
                    <button wire:click="closeModal" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded mr-2">Cancelar</button>
                    <button wire:click="submitOffer" class="bg-[#B35F12] hover:bg-[#9A5210] text-white font-bold py-2 px-4 rounded">Enviar Oferta</button>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('message'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            {{ session('message') }}
        </div>
    @endif
</div>
