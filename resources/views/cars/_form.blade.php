@props(['car', 'brands', 'fuels', 'gears', 'colors', 'listingType' => null, 'priceLabel' => __('Price')])

<style>
    .ck-editor__editable_inline {
        min-height: 200px;
    }
</style>

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
        <x-label for="price" value="{{ $priceLabel }}" />
        <x-input type="number" step="0.01" name="precio" id="price" class="mt-1 block w-full" value="{{ old('precio', $car->precio) }}" />
        <p class="text-xs text-gray-500 mt-1">{{ __('Note:The total price may increase due to VAT.') }}</p>
    </div>

    <div>
        <x-label for="matricula" value="{{ __('Matricula') }}" />
        <x-input type="text" name="matricula" id="matricula" class="mt-1 block w-full" value="{{ old('matricula', $car->matricula) }}" />
    </div>

    <div wire:ignore>
        <x-label for="descripcion" value="{{ __('Descripcion') }}" />
        <x-textarea name="descripcion" id="descripcion" class="mt-1 block w-full">{{ old('descripcion', $car->descripcion) }}</x-textarea>
    </div>

    <div>
        <x-label for="image" value="{{ __('Image') }}" class="mb-2" />

        <div x-data="{
         dragover: false,
         previewUrl: null,
         handleFileChange(event) {
             const file = event.target.files[0];
             if (file) {
                 this.previewUrl = URL.createObjectURL(file);
                 const current = document.getElementById('current-image-container');
                 if(current) current.style.display = 'none';
             } else {
                 this.previewUrl = null;
             }
         },
         handleDrop(event) {
             this.dragover = false;
             const file = event.dataTransfer.files[0];
             if (file && file.type.startsWith('image/')) {
                 const dt = new DataTransfer();
                 dt.items.add(file);
                 document.getElementById('image').files = dt.files;
                 this.previewUrl = URL.createObjectURL(file);
                 const current = document.getElementById('current-image-container');
                 if(current) current.style.display = 'none';
             }
         }
     }"
             class="relative w-full rounded-xl flex items-center justify-center transition-colors duration-200 cursor-pointer overflow-hidden group"
             :style="previewUrl
         ? 'height: 224px; border: 2px dashed #d1d5db; background: #f9fafb;'
         : dragover
             ? 'height: 224px; border: 2px dashed #B35F12; background: #fff7ed;'
             : 'height: 224px; border: 2px dashed #d1d5db; background: #f9fafb;'"
             @dragover.prevent="dragover = true"
             @dragleave.prevent="dragover = false"
             @drop.prevent="handleDrop($event)">

            <input type="file" name="image" id="image" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" @change="handleFileChange($event)" accept="image/*">

            <div class="text-center p-6 relative z-10" x-show="!previewUrl">
                <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-[#B35F12] transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="mt-4 flex text-sm text-gray-600 justify-center">
            <span class="relative bg-transparent rounded-md font-medium text-[#284961] hover:text-[#1c3344]">
                {{ __('Upload a file') }}
            </span>
                    <p class="pl-1">{{ __('or drag and drop') }}</p>
                </div>
                <p class="text-xs text-gray-500 mt-2">PNG, JPG, JPEG hasta 2MB</p>
            </div>

            <div x-show="previewUrl" class="absolute inset-0 w-full h-full z-10 flex items-center justify-center p-2" x-cloak>
                <img :src="previewUrl" style="max-width: 100%; max-height: 200px; object-fit: contain; border-radius: 8px;">
                <div class="absolute inset-0 bg-black bg-opacity-40 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity rounded-xl">
                    <svg class="h-10 w-10 text-white mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                    <p class="text-white font-bold text-sm">{{ __('Click or drop to change image') }}</p>
                </div>
            </div>
        </div>

        @if (!empty($car->image))
        <div class="mt-4 p-4 bg-gray-50 rounded-lg border border-gray-200" id="current-image-container">
                <p class="text-sm text-gray-600 mb-4 font-semibold">{{ __('Current Image') }}:</p>
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="relative w-32 h-24 flex-shrink-0">
                        <img src="{{ Str::startsWith($car->image, 'http') ? $car->image : Storage::url($car->image) }}" alt="{{ $car->title }}" class="w-full h-full object-cover rounded-md shadow-md border border-gray-300">
                    </div>

                    <input type="hidden" name="delete_image" id="delete_image_input" value="0">

                    <button type="button"
                            onclick="document.getElementById('delete_image_input').value = '1'; document.getElementById('current-image-container').style.display = 'none';"
                            class="bg-white px-4 py-2 border border-red-200 rounded-md shadow-sm hover:bg-red-50 text-sm text-red-600 font-bold transition focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-1 flex-shrink-0">
                        {{ __('Delete Current Image') }}
                    </button>
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
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        ClassicEditor
            .create(document.querySelector('#descripcion'), {
                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo' ]
            })
            .catch(error => {
                console.error(error);
            });

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
