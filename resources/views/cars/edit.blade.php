<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Car') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <x-validation-errors class="mb-4" />

                    <form action="{{ route('cars.update', $car) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <x-label for="brand" value="{{ __('Brand') }}" />
                                <x-select name="id_marca" id="brand" class="mt-1 block w-full">
                                    <option value="">--{{ __('Select a brand') }}--</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('id_marca', $car->id_marca) == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->nombre }}
                                        </option>
                                    @endforeach
                                </x-select>
                            </div>
                            <div>
                                <x-label for="model" value="{{ __('Model') }}" />
                                <x-select name="id_modelo" id="model" class="mt-1 block w-full" disabled>
                                    <option value="">--{{ __('Select a model') }}--</option>
                                </x-select>
                            </div>

                            <!-- Color -->
                            <div>
                                <x-label for="color" value="{{ __('Color') }}" />
                                <x-select name="id_color" id="color" class="mt-1 block w-full">
                                    <option value="">--{{ __('Select a color') }}--</option>
                                    @foreach($colors as $color)
                                        <option value="{{ $color->id }}" {{ old('id_color', $car->id_color) == $color->id ? 'selected' : '' }}>
                                            {{ $color->nombre }}
                                        </option>
                                    @endforeach
                                </x-select>
                            </div>

                            <!-- Combustible -->
                            <div>
                                <x-label value="{{ __('Fuels') }}" />
                                <div class="mt-2 flex flex-wrap gap-4">
                                    @foreach($fuels as $fuel)
                                        <label class="inline-flex items-center">
                                            <x-radio name="id_combustible" value="{{ $fuel->id }}" :checked="old('id_combustible', $car->id_combustible) == $fuel->id" />
                                            <span class="ml-2 text-gray-700">{{ $fuel->nombre }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Marchas -->
                            <div>
                                <x-label value="{{ __('Gear') }}" />
                                <div class="mt-2 flex flex-wrap gap-4">
                                    @foreach($gears as $gear)
                                        <label class="inline-flex items-center">
                                            <x-radio name="id_marcha" value="{{ $gear->id }}" :checked="old('id_marcha', $car->id_marcha) == $gear->id" />
                                            <span class="ml-2 text-gray-700">{{ $gear->tipo }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <x-label for="price" value="{{ __('Price') }}" />
                                <x-input type="number" step="0.01" name="precio" id="price" class="mt-1 block w-full" value="{{ old('precio', $car->precio) }}" />
                            </div>

                            <div>
                                <x-label for="year" value="{{ __('Year') }}" />
                                <x-input type="number" name="anyo_matri" id="year" class="mt-1 block w-full" value="{{ old('anyo_matri', $car->anyo_matri) }}" />
                            </div>

                            <div>
                                <x-label for="km" value="{{ __('KM') }}" />
                                <x-input type="number" name="km" id="km" class="mt-1 block w-full" value="{{ old('km', $car->km) }}" />
                            </div>

                            <div>
                                <x-label for="matricula" value="{{ __('Matricula') }}" />
                                <x-input type="text" name="matricula" id="matricula" class="mt-1 block w-full" value="{{ old('matricula', $car->matricula) }}" />
                            </div>

                            <div>
                                <x-label for="descripcion" value="{{ __('Descripcion') }}" />
                                <x-textarea name="descripcion" id="descripcion" class="mt-1 block w-full">{{ old('descripcion', $car->descripcion) }}</x-textarea>
                            </div>

                            <div>
                                <x-label for="image" value="{{ __('Image') }}" />
                                <x-input type="file" name="image" id="image" class="mt-1 block w-full" />
                                @if ($car->image)
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($car->image) }}" alt="{{ $car->title }}" class="h-20 w-20 object-cover rounded-md">
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <x-button class="ml-4">
                                    {{ __('Update') }}
                                </x-button>
                                <a href="{{ route('cars.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 rounded-md text-sm text-red-600 hover:bg-gray-400 ml-2 font-semibold uppercase tracking-widest">
                                    {{ __('Cancel') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        // ... (El script se mantiene igual)
        document.addEventListener('DOMContentLoaded', function () {
            const brandSelect = document.getElementById('brand');
            const modelSelect = document.getElementById('model');
            const oldBrand = '{{ old('id_marca', $car->id_marca) }}';
            const oldModel = '{{ old('id_modelo', $car->id_modelo) }}';

            function loadModels(brandId, selectedModelId) {
                modelSelect.disabled = true;
                modelSelect.innerHTML = '<option value="">--{{ __("Select a model") }}--</option>';

                if (brandId) {
                    fetch(`/api/brands/${brandId}/models`)
                        .then(response => response.json())
                        .then(data => {
                            modelSelect.disabled = false;
                            data.forEach(model => {
                                const option = document.createElement('option');
                                option.value = model.id;
                                option.textContent = model.nombre;
                                if (model.id == selectedModelId) {
                                    option.selected = true;
                                }
                                modelSelect.appendChild(option);
                            });
                        });
                }
            }

            brandSelect.addEventListener('change', function () {
                loadModels(this.value, null);
            });

            if (oldBrand) {
                loadModels(oldBrand, oldModel);
            }
        });
    </script>
    @endpush
</x-app-layout>
