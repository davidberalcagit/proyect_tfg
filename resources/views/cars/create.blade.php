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
                    @if ($errors->any())
                        <div class="mb-4">
                            <div class="font-medium text-red-600">{{ __('Whoops! Something went wrong.') }}</div>
                            <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('cars.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-1 gap-6">

                            <!-- Marca -->
                            <div>
                                <label for="brand" class="block font-medium text-sm text-gray-700">{{ __('Brand') }}</label>
                                <select name="id_marca" id="brand" class="form-input rounded-md shadow-sm mt-1 block w-full">
                                    <option value="">--{{ __('Select a brand') }}--</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('id_marca') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->nombre }}
                                        </option>
                                    @endforeach
                                    <option value="other" {{ old('id_marca') == 'other' ? 'selected' : '' }}>{{ __('Other (New Brand)') }}</option>
                                </select>
                                <div id="temp_brand_container" class="mt-2 hidden">
                                    <label for="temp_brand" class="block font-medium text-sm text-gray-700">{{ __('Nombre de la Nueva Marca') }}</label>
                                    <input type="text" name="temp_brand" id="temp_brand" class="form-input rounded-md shadow-sm mt-1 block w-full" value="{{ old('temp_brand') }}" placeholder="Ej: Tesla" />
                                    <p class="text-xs text-orange-600 mt-1">{{ __('Al crear una nueva marca, el coche quedará pendiente de revisión.') }}</p>
                                </div>
                            </div>

                            <!-- Modelo -->
                            <div>
                                <label for="model" class="block font-medium text-sm text-gray-700">{{ __('Model') }}</label>
                                <select name="id_modelo" id="model" class="form-input rounded-md shadow-sm mt-1 block w-full" disabled>
                                    <option value="">--{{ __('Select a model') }}--</option>
                                </select>
                                <div id="temp_model_container" class="mt-2 hidden">
                                    <label for="temp_model" class="block font-medium text-sm text-gray-700">{{ __('Nombre del Nuevo Modelo') }}</label>
                                    <input type="text" name="temp_model" id="temp_model" class="form-input rounded-md shadow-sm mt-1 block w-full" value="{{ old('temp_model') }}" placeholder="Ej: Model S" />
                                    <p class="text-xs text-orange-600 mt-1">{{ __('Al crear un nuevo modelo, el coche quedará pendiente de revisión.') }}</p>
                                </div>
                            </div>

                            <!-- Color -->
                            <div>
                                <label for="color" class="block font-medium text-sm text-gray-700">{{ __('Color') }}</label>
                                <select name="id_color" id="color" class="form-input rounded-md shadow-sm mt-1 block w-full">
                                    <option value="">--{{ __('Select a color') }}--</option>
                                    @foreach($colors as $color)
                                        <option value="{{ $color->id }}" {{ old('id_color') == $color->id ? 'selected' : '' }}>
                                            {{ $color->nombre }}
                                        </option>
                                    @endforeach
                                    <option value="other" {{ old('id_color') == 'other' ? 'selected' : '' }}>{{ __('Other (New Color)') }}</option>
                                </select>
                                <div id="temp_color_container" class="mt-2 hidden">
                                    <label for="temp_color" class="block font-medium text-sm text-gray-700">{{ __('Nombre del Nuevo Color') }}</label>
                                    <input type="text" name="temp_color" id="temp_color" class="form-input rounded-md shadow-sm mt-1 block w-full" value="{{ old('temp_color') }}" placeholder="Ej: Azul Eléctrico" />
                                </div>
                            </div>

                            <div>
                                <label for="fuels" class="block font-medium text-sm text-gray-700">{{ __('Fuels') }}</label>
                                <select name="id_combustible" id="fuels" class="form-input rounded-md shadow-sm mt-1 block w-full">
                                    <option value="">--{{ __('Select a fuel') }}--</option>
                                    @foreach($fuels as $fuel)
                                        <option value="{{ $fuel->id }}" {{ old('id_combustible') == $fuel->id ? 'selected' : '' }}>
                                            {{ $fuel->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700">{{ __('Gear') }}</label>
                                <div class="mt-2">
                                    @foreach($gears as $gear)
                                        <label class="inline-flex items-center mr-4">
                                            <input type="radio" name="id_marcha" value="{{ $gear->id }}" {{ old('id_marcha') == $gear->id ? 'checked' : '' }} class="form-radio">
                                            <span class="ml-2">{{ $gear->tipo }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <label for="year" class="block font-medium text-sm text-gray-700">{{ __('Year') }}</label>
                                <x-input type="number" name="anyo_matri" id="year" class="form-input rounded-md shadow-sm mt-1 block w-full" value="{{ old('anyo_matri') }}" />
                            </div>

                            <div>
                                <label for="km" class="block font-medium text-sm text-gray-700">{{ __('KM') }}</label>
                                <input type="number" name="km" id="km" class="form-input rounded-md shadow-sm mt-1 block w-full" value="{{ old('km') }}" />
                            </div>
                            <div>
                                <label for="price" class="block font-medium text-sm text-gray-700">{{ __('Price') }}</label>
                                <input type="number" step="0.01" name="precio" id="price" class="form-input rounded-md shadow-sm mt-1 block w-full" value="{{ old('precio') }}" />
                            </div>
                            <div>
                                <label for="matricula" class="block font-medium text-sm text-gray-700">{{ __('Matricula') }}</label>
                                <input type="text" name="matricula" id="matricula" class="form-input rounded-md shadow-sm mt-1 block w-full" value="{{ old('matricula') }}" />
                            </div>

                            <div>
                                <label for="descripcion" class="block font-medium text-sm text-gray-700">{{ __('Descripcion') }}</label>
                                <textarea name="descripcion" id="descripcion" class="form-input rounded-md shadow-sm mt-1 block w-full">{{ old('descripcion') }}</textarea>
                            </div>
                            <div>
                                <label for="image" class="block font-medium text-sm text-gray-700">{{ __('Image') }}</label>
                                <input type="file" name="image" id="image" class="form-input rounded-md shadow-sm mt-1 block w-full" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                                    {{ __('Create') }}
                                </button>
                                <a href="{{ route('cars.index') }}"
                                   class="inline-flex items-center px-4 py-2 bg-gray-300 rounded-md text-sm text-red-600 hover:bg-gray-400 ml-2">
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
