<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Car') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                            <div>
                                <label for="title" class="block font-medium text-sm text-gray-700">Title</label>
                                <input type="text" name="title" id="title" class="form-input rounded-md shadow-sm mt-1 block w-full" value="{{ old('title') }}" />
                            </div>
                            <div>
                                <label for="brand" class="block font-medium text-sm text-gray-700">Brand</label>
                                <select name="id_marca" id="brand" class="form-input rounded-md shadow-sm mt-1 block w-full">
                                    <option value="">--Selecciona una marca--</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}" {{ old('id_marca') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="model" class="block font-medium text-sm text-gray-700">Model</label>
                                <select name="id_modelo" id="model" class="form-input rounded-md shadow-sm mt-1 block w-full" disabled>
                                    <option value="">--Selecciona un modelo--</option>
                                </select>
                            </div>
                            <div>
                                <label for="fuels" class="block font-medium text-sm text-gray-700">Fuels</label>
                                <select name="id_combustible" id="fuels" class="form-input rounded-md shadow-sm mt-1 block w-full">
                                    <option value="">--Selecciona un combustible--</option>
                                    @foreach($fuels as $fuel)
                                        <option value="{{ $fuel->id }}" {{ old('id_combustible') == $fuel->id ? 'selected' : '' }}>
                                            {{ $fuel->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block font-medium text-sm text-gray-700">Gear</label>
                                <div class="mt-2">
                                    @foreach($gears as $gear)
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="id_marcha" value="{{ $gear->id }}" {{ old('id_marcha') == $gear->id ? 'checked' : '' }} class="form-radio">
                                            <span class="ml-2">{{ $gear->tipo }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <label for="añyo_matri" class="block font-medium text-sm text-gray-700">Year</label>
                                <x-input type="number" name="anyo_matri" id="year" class="form-input rounded-md shadow-sm mt-1 block w-full" value="{{ old('anyo_matri') }}" />
                            </div>

                            <div>
                                <label for="km" class="block font-medium text-sm text-gray-700">KM</label>
                                <input type="text" name="km" id="km" class="form-input rounded-md shadow-sm mt-1 block w-full" value="{{ old('km') }}" />
                            </div>
                            <div>
                                <label for="precio" class="block font-medium text-sm text-gray-700">Price</label>
                                <input type="text" name="precio" id="price" class="form-input rounded-md shadow-sm mt-1 block w-full" value="{{ old('precio') }}" />
                            </div>
                            <div>
                                <label for="matricula" class="block font-medium text-sm text-gray-700">Matricula</label>
                                <input type="text" name="matricula" id="matricula" class="form-input rounded-md shadow-sm mt-1 block w-full" value="{{ old('matricula') }}" />
                            </div>
                            <div>
                                <label for="color" class="block font-medium text-sm text-gray-700">Color</label>
                                <input type="text" name="color" id="color" class="form-input rounded-md shadow-sm mt-1 block w-full" value="{{ old('color') }}" />
                            </div>
                            <div>
                                <label for="descripcion" class="block font-medium text-sm text-gray-700">Descripcion</label>
                                <textarea name="descripcion" id="descripcion" class="form-input rounded-md shadow-sm mt-1 block w-full">{{ old('descripcion') }}</textarea>
                            </div>
                            <div>
                                <label for="image" class="block font-medium text-sm text-gray-700">Image</label>
                                <input type="file" name="image" id="image" class="form-input rounded-md shadow-sm mt-1 block w-full" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition">
                                    {{ __('Create') }}
                                </button>
                                <a href="{{ route('cars.index') }}"
                                   class="inline-flex items-center px-4 py-2 bg-gray-300 rounded-md text-sm text-red-600 hover:bg-gray-400">
                                    Cancelar
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
            const oldBrand = '{{ old('id_marca') }}';
            const oldModel = '{{ old('id_modelo') }}';

            function loadModels(brandId, selectedModelId) {
                modelSelect.disabled = true;
                modelSelect.innerHTML = '<option value=""   >--Selecciona un modelo--</option>';

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
