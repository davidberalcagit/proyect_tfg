@props(['car', 'brands', 'fuels', 'gears', 'colors', 'listingType' => null])

<div class="grid grid-cols-1 gap-6">

    @if(!$car->exists && isset($listingType))
        <input type="hidden" name="id_listing_type" value="{{ $listingType->id }}">
    @endif


    <div>
        <x-label for="brand" value="{{ __('Brand') }}" />
        <x-select name="id_marca" id="brand" class="mt-1 block w-full">
            <option value="">--{{ __('Select a brand') }}--</option>
            @foreach($brands as $brand)
                <option value="{{ $brand->id }}" {{ old('id_marca', $car->id_marca) == $brand->id ? 'selected' : '' }}>
                    {{ $brand->nombre }}
                </option>
            @endforeach
            <option value="other" {{ old('id_marca', $car->id_marca) == 'other' ? 'selected' : '' }}>{{ __('Other (New Brand)') }}</option>
        </x-select>

        <div id="temp_brand_container" class="mt-2 {{ old('id_marca', $car->id_marca) === 'other' || $car->temp_brand ? '' : 'hidden' }}">
            <x-label for="temp_brand" value="{{ __('Nombre de la Nueva Marca') }}" />
            <x-input type="text" name="temp_brand" id="temp_brand" class="mt-1 block w-full" value="{{ old('temp_brand', $car->temp_brand) }}" placeholder="Ej: Tesla" />
            <p class="text-xs text-orange-600 mt-1">{{ __('Al crear una nueva marca, el coche quedará pendiente de revisión.') }}</p>
        </div>
    </div>

    <div>
        <x-label for="model" value="{{ __('Model') }}" />
        <x-select name="id_modelo" id="model" class="mt-1 block w-full" :disabled="!old('id_marca', $car->id_marca) && !$car->id_marca">
            <option value="">--{{ __('Select a model') }}--</option>
        </x-select>

        <div id="temp_model_container" class="mt-2 {{ old('id_modelo', $car->id_modelo) === 'other' || $car->temp_model ? '' : 'hidden' }}">
            <x-label for="temp_model" value="{{ __('Nombre del Nuevo Modelo') }}" />
            <x-input type="text" name="temp_model" id="temp_model" class="mt-1 block w-full" value="{{ old('temp_model', $car->temp_model) }}" placeholder="Ej: Model S" />
            <p class="text-xs text-orange-600 mt-1">{{ __('Al crear un nuevo modelo, el coche quedará pendiente de revisión.') }}</p>
        </div>
    </div>

    <div>
        <x-label for="color" value="{{ __('Color') }}" />
        <x-select name="id_color" id="color" class="mt-1 block w-full">
            <option value="">--{{ __('Select a color') }}--</option>
            @foreach($colors as $color)
                <option value="{{ $color->id }}" {{ old('id_color', $car->id_color) == $color->id ? 'selected' : '' }}>
                    {{ $color->nombre }}
                </option>
            @endforeach
            <option value="other" {{ old('id_color', $car->id_color) == 'other' ? 'selected' : '' }}>{{ __('Other (New Color)') }}</option>
        </x-select>

        <div id="temp_color_container" class="mt-2 {{ old('id_color', $car->id_color) === 'other' || $car->temp_color ? '' : 'hidden' }}">
            <x-label for="temp_color" value="{{ __('Nombre del Nuevo Color') }}" />
            <x-input type="text" name="temp_color" id="temp_color" class="mt-1 block w-full" value="{{ old('temp_color', $car->temp_color) }}" placeholder="Ej: Azul Eléctrico" />
        </div>
    </div>

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
        <x-label for="year" value="{{ __('Year') }}" />
        <x-input type="number" name="anyo_matri" id="year" class="mt-1 block w-full" value="{{ old('anyo_matri', $car->anyo_matri) }}" />
    </div>

    <div>
        <x-label for="km" value="{{ __('KM') }}" />
        <x-input type="number" name="km" id="km" class="mt-1 block w-full" value="{{ old('km', $car->km) }}" />
    </div>

    <div>
        @php
            $priceLabel = __('Price');
            if (isset($listingType) && $listingType->nombre === 'Alquiler') {
                $priceLabel = __('Daily Price');
            } elseif ($car->exists && $car->listingType && $car->listingType->nombre === 'Alquiler') {
                $priceLabel = __('Daily Price');
            }
        @endphp
        <x-label for="price" value="{{ $priceLabel }}" />
        <x-input type="number" step="0.01" name="precio" id="price" class="mt-1 block w-full" value="{{ old('precio', $car->precio) }}" />
        <p class="text-xs text-gray-500 mt-1">{{ __('Note:The total price may increase due to VAT.') }}</p>
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
            <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-sm text-gray-600 mb-2">{{ __('Current Image') }}:</p>
                <div class="flex items-center gap-4">
                    <img src="{{ Storage::url($car->image) }}" alt="{{ $car->title }}" class="h-24 w-24 object-cover rounded-md shadow-sm">

                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="delete_image" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-red-600 font-medium">{{ __('Delete Image') }}</span>
                    </label>
                </div>
            </div>
        @endif
    </div>

    <div class="flex items-center justify-end mt-4">
        <x-button class="ml-4">
            {{ $car->exists ? __('Update') : __('Create') }}
        </x-button>
        <a href="{{ route('cars.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 rounded-md text-sm text-red-600 hover:bg-gray-400 ml-2 font-semibold uppercase tracking-widest">
            {{ __('Cancel') }}
        </a>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const brandSelect = document.getElementById('brand');
        const modelSelect = document.getElementById('model');
        const colorSelect = document.getElementById('color');

        const tempBrandContainer = document.getElementById('temp_brand_container');
        const tempModelContainer = document.getElementById('temp_model_container');
        const tempColorContainer = document.getElementById('temp_color_container');

        const oldBrand = '{{ old('id_marca', $car->id_marca) }}';
        const oldModel = '{{ old('id_modelo', $car->id_modelo) }}';
        const oldColor = '{{ old('id_color', $car->id_color) }}';

        const isTempBrand = '{{ $car->temp_brand ? "true" : "false" }}' === 'true';
        const isTempModel = '{{ $car->temp_model ? "true" : "false" }}' === 'true';
        const isTempColor = '{{ $car->temp_color ? "true" : "false" }}' === 'true';

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

                        if (selectedModelId === 'other' || (isTempModel && !selectedModelId)) {
                             otherOption.selected = true;
                        }

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
            if (oldBrand !== 'other') {
                loadModels(oldBrand, oldModel);
            }
        } else if (isTempBrand) {
            brandSelect.value = 'other';
            toggleTempBrand();
        }

        if (oldColor) {
            toggleTempColor();
        } else if (isTempColor) {
            colorSelect.value = 'other';
            toggleTempColor();
        }
    });
</script>
@endpush
