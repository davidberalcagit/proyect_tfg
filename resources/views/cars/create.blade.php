<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Car') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 shadow-md rounded-r" role="alert">
                <p class="font-bold">{{ __('Important Information') }}</p>
                <p>{{ __('The acceptance process for your vehicle may take some time. During this period, your car will appear as') }} <strong>"{{ __('Pending Review') }}"</strong>.</p>
                <p class="mt-2 text-sm">{{ __('You can edit your vehicle data while it is pending.') }} <span class="font-bold text-red-600">{{ __('Once confirmed and published, you will not be able to make changes.') }}</span></p>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <x-validation-errors class="mb-4" />

                    <form action="{{ route('cars.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" name="id_listing_type" value="{{ $listingType->id }}">

                        <div class="grid grid-cols-1 gap-6">

                            <!-- Marca -->
                            <div>
                                <x-label for="brand" value="{{ __('Brand') }}" />
                                <x-select name="id_marca" id="brand" class="mt-1 block w-full">
                                    <option value="">--{{ __('Select a brand') }}--</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('id_marca') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->nombre }}
                                        </option>
                                    @endforeach
                                    <option value="other" {{ old('id_marca') == 'other' ? 'selected' : '' }}>{{ __('Other (New Brand)') }}</option>
                                </x-select>
                                <div id="temp_brand_container" class="mt-2 hidden">
                                    <x-label for="temp_brand" value="{{ __('Nombre de la Nueva Marca') }}" />
                                    <x-input type="text" name="temp_brand" id="temp_brand" class="mt-1 block w-full" value="{{ old('temp_brand') }}" placeholder="Ej: Tesla" />
                                    <p class="text-xs text-orange-600 mt-1">{{ __('Al crear una nueva marca, el coche quedará pendiente de revisión.') }}</p>
                                </div>
                            </div>

                            <!-- Modelo -->
                            <div>
                                <x-label for="model" value="{{ __('Model') }}" />
                                <x-select name="id_modelo" id="model" class="mt-1 block w-full" disabled>
                                    <option value="">--{{ __('Select a model') }}--</option>
                                </x-select>
                                <div id="temp_model_container" class="mt-2 hidden">
                                    <x-label for="temp_model" value="{{ __('Nombre del Nuevo Modelo') }}" />
                                    <x-input type="text" name="temp_model" id="temp_model" class="mt-1 block w-full" value="{{ old('temp_model') }}" placeholder="Ej: Model S" />
                                    <p class="text-xs text-orange-600 mt-1">{{ __('Al crear un nuevo modelo, el coche quedará pendiente de revisión.') }}</p>
                                </div>
                            </div>

                            <!-- Color -->
                            <div>
                                <x-label for="color" value="{{ __('Color') }}" />
                                <x-select name="id_color" id="color" class="mt-1 block w-full">
                                    <option value="">--{{ __('Select a color') }}--</option>
                                    @foreach($colors as $color)
                                        <option value="{{ $color->id }}" {{ old('id_color') == $color->id ? 'selected' : '' }}>
                                            {{ $color->nombre }}
                                        </option>
                                    @endforeach
                                    <option value="other" {{ old('id_color') == 'other' ? 'selected' : '' }}>{{ __('Other (New Color)') }}</option>
                                </x-select>
                                <div id="temp_color_container" class="mt-2 hidden">
                                    <x-label for="temp_color" value="{{ __('Nombre del Nuevo Color') }}" />
                                    <x-input type="text" name="temp_color" id="temp_color" class="mt-1 block w-full" value="{{ old('temp_color') }}" placeholder="Ej: Azul Eléctrico" />
                                </div>
                            </div>

                            <!-- Combustible -->
                            <div>
                                <x-label value="{{ __('Fuels') }}" />
                                <div class="mt-2 flex flex-wrap gap-4">
                                    @foreach($fuels as $fuel)
                                        <label class="inline-flex items-center">
                                            <x-radio name="id_combustible" value="{{ $fuel->id }}" :checked="old('id_combustible') == $fuel->id" />
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
                                            <x-radio name="id_marcha" value="{{ $gear->id }}" :checked="old('id_marcha') == $gear->id" />
                                            <span class="ml-2 text-gray-700">{{ $gear->tipo }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <x-label for="year" value="{{ __('Year') }}" />
                                <x-input type="number" name="anyo_matri" id="year" class="mt-1 block w-full" value="{{ old('anyo_matri') }}" />
                            </div>

                            <div>
                                <x-label for="km" value="{{ __('KM') }}" />
                                <x-input type="number" name="km" id="km" class="mt-1 block w-full" value="{{ old('km') }}" />
                            </div>

                            <div>
                                <x-label for="price" value="{{ __('Price') }}" />
                                <x-input type="number" step="0.01" name="precio" id="price" class="mt-1 block w-full" value="{{ old('precio') }}" />
                            </div>

                            <div>
                                <x-label for="matricula" value="{{ __('Matricula') }}" />
                                <x-input type="text" name="matricula" id="matricula" class="mt-1 block w-full" value="{{ old('matricula') }}" />
                            </div>

                            <div>
                                <x-label for="descripcion" value="{{ __('Descripcion') }}" />
                                <x-textarea name="descripcion" id="descripcion" class="mt-1 block w-full">{{ old('descripcion') }}</x-textarea>
                            </div>

                            <div>
                                <x-label for="image" value="{{ __('Image') }}" />
                                <x-input type="file" name="image" id="image" class="mt-1 block w-full" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <x-button class="ml-4">
                                    {{ __('Create') }}
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
            const colorSelect = document.getElementById('color');

            const tempBrandContainer = document.getElementById('temp_brand_container');
            const tempModelContainer = document.getElementById('temp_model_container');
            const tempColorContainer = document.getElementById('temp_color_container');

            const oldBrand = '{{ old('id_marca') }}';
            const oldModel = '{{ old('id_modelo') }}';
            const oldColor = '{{ old('id_color') }}';

            function toggleTempBrand() {
                if (brandSelect.value === 'other') {
                    tempBrandContainer.classList.remove('hidden');
                    modelSelect.innerHTML = '<option value="other" selected>{{ __("Other (New Model)") }}</option>';
                    modelSelect.disabled = true;
                    toggleTempModel();
                } else {
                    tempBrandContainer.classList.add('hidden');
                    modelSelect.disabled = false;
                }
            }

            function toggleTempModel() {
                if (modelSelect.value === 'other') {
                    tempModelContainer.classList.remove('hidden');
                } else {
                    tempModelContainer.classList.add('hidden');
                }
            }

            function toggleTempColor() {
                if (colorSelect.value === 'other') {
                    tempColorContainer.classList.remove('hidden');
                } else {
                    tempColorContainer.classList.add('hidden');
                }
            }

            function loadModels(brandId, selectedModelId) {
                if (brandId === 'other') return;

                modelSelect.innerHTML = '<option value="">--{{ __("Select a model") }}--</option>';
                modelSelect.disabled = true;

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

                            const otherOption = document.createElement('option');
                            otherOption.value = 'other';
                            otherOption.textContent = '{{ __("Other (New Model)") }}';
                            if (selectedModelId === 'other') otherOption.selected = true;
                            modelSelect.appendChild(otherOption);

                            toggleTempModel();
                        });
                }
            }

            brandSelect.addEventListener('change', function () {
                toggleTempBrand();
                if (this.value !== 'other') {
                    loadModels(this.value, null);
                }
            });

            modelSelect.addEventListener('change', function () {
                toggleTempModel();
            });

            colorSelect.addEventListener('change', function () {
                toggleTempColor();
            });

            if (oldBrand) {
                toggleTempBrand();
                if (oldBrand !== 'other') loadModels(oldBrand, oldModel);
            }
            if (oldColor) toggleTempColor();
        });
    </script>
    @endpush
</x-app-layout>
