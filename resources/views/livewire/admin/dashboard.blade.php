<div>
    <!-- Gestión de Tablas Auxiliares (Livewire CRUDs) -->
    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg border-t-4 border-gray-400 mb-8">
        <div class="border-b border-gray-200 bg-gray-100 overflow-x-auto">
            <nav class="-mb-px flex min-w-max" aria-label="Tabs">

                <!-- Botón Marcas -->
                <button wire:click="setTab('brands')"
                        class="w-32 sm:w-1/5 py-4 px-1 text-center text-sm transition-all duration-200 ease-in-out focus:outline-none {{ $activeTab === 'brands' ? 'border-[#B35F12] text-[#B35F12] bg-white border-b-4 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium' }}">
                    Marcas
                </button>

                <!-- Botón Modelos -->
                <button wire:click="setTab('models')"
                        class="w-32 sm:w-1/5 py-4 px-1 text-center text-sm transition-all duration-200 ease-in-out focus:outline-none {{ $activeTab === 'models' ? 'border-[#B35F12] text-[#B35F12] bg-white border-b-4 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium' }}">
                    Modelos
                </button>

                <!-- Botón Combustibles -->
                <button wire:click="setTab('fuels')"
                        class="w-32 sm:w-1/5 py-4 px-1 text-center text-sm transition-all duration-200 ease-in-out focus:outline-none {{ $activeTab === 'fuels' ? 'border-[#B35F12] text-[#B35F12] bg-white border-b-4 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium' }}">
                    Combustibles
                </button>

                <!-- Botón Colores -->
                <button wire:click="setTab('colors')"
                        class="w-32 sm:w-1/5 py-4 px-1 text-center text-sm transition-all duration-200 ease-in-out focus:outline-none {{ $activeTab === 'colors' ? 'border-[#B35F12] text-[#B35F12] bg-white border-b-4 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium' }}">
                    Colores
                </button>

                <!-- Botón Marchas -->
                <button wire:click="setTab('gears')"
                        class="w-32 sm:w-1/5 py-4 px-1 text-center text-sm transition-all duration-200 ease-in-out focus:outline-none {{ $activeTab === 'gears' ? 'border-[#B35F12] text-[#B35F12] bg-white border-b-4 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium' }}">
                    Marchas
                </button>
            </nav>
        </div>

        <div class="p-4 sm:p-6 min-h-[400px]">
            <!-- Skeleton Loader -->
            <div wire:loading class="w-full">
                <div class="animate-pulse p-4 sm:p-6 bg-white rounded-lg shadow-md border border-gray-100">
                    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                        <div class="h-8 bg-gray-200 rounded w-48"></div>
                        <div class="h-10 bg-gray-200 rounded w-32"></div>
                    </div>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-100 h-12 w-full border-b border-gray-200 flex items-center px-6">
                            <div class="h-4 bg-gray-300 rounded w-10 mr-10"></div>
                            <div class="h-4 bg-gray-300 rounded w-1/3"></div>
                        </div>
                        <div class="bg-white p-0">
                            @foreach(range(1, 5) as $i)
                                <div class="flex items-center px-6 py-4 border-b border-gray-100">
                                    <div class="h-4 bg-gray-100 rounded w-10 mr-10"></div>
                                    <div class="h-4 bg-gray-100 rounded w-1/2 flex-grow"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contenido Real -->
            <div wire:loading.remove>
                @if($activeTab === 'brands')
                    <livewire:admin.brand-manager />
                @elseif($activeTab === 'models')
                    <livewire:admin.model-manager />
                @elseif($activeTab === 'fuels')
                    <livewire:admin.fuel-manager />
                @elseif($activeTab === 'colors')
                    <livewire:admin.color-manager />
                @elseif($activeTab === 'gears')
                    <livewire:admin.gear-manager />
                @endif
            </div>
        </div>
    </div>
</div>
