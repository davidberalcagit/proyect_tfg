<div>
    <button wire:click="openModal" class="w-full justify-center inline-flex items-center px-4 py-2 bg-[#284961] border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-[#1c3344] focus:bg-[#1c3344] active:bg-[#1c3344] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition h-10 shadow-md">
        {{ __('Make Offer') }}
    </button>

    @if($isModalOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

            <div class="bg-white rounded-xl overflow-hidden shadow-2xl transform transition-all sm:max-w-md w-full p-6 relative z-10 border border-gray-200">
                <h3 class="text-xl font-bold text-[#284961] mb-4" id="modal-title">{{ __('Offer for') }} {{ $car->title }}</h3>

                @if (session()->has('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 mb-4 text-sm rounded">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">{{ __('Your Offer') }} (€)</label>
                    <input type="number" wire:model="cantidad" wire:keydown.enter="submitOffer" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-[#B35F12] focus:ring focus:ring-[#B35F12] focus:ring-opacity-50 py-2 px-3 text-lg font-mono text-[#284961]" placeholder="0.00" autofocus>
                    @error('cantidad') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <button wire:click="closeModal" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition text-sm uppercase tracking-wide">
                        {{ __('Cancel') }}
                    </button>
                    <button wire:click="submitOffer" class="bg-[#B35F12] hover:bg-[#9A5210] text-white font-bold py-2 px-4 rounded-lg shadow transition text-sm uppercase tracking-wide">
                        {{ __('Send Offer') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if (session()->has('message'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-xl z-50 flex items-center" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" x-transition>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('message') }}
        </div>
    @endif
</div>
