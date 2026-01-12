<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            </div>

              <div class="mt-4">
        <x-label for="type" value="Account type" />
        <select
            id="type"
            name="type"
            class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"
            required
        >
            <option value="" disabled selected>Select</option>
            <option value="individual" {{ old('type') === 'individual' ? 'selected' : '' }}>
                Individual
            </option>
            <option value="dealership" {{ old('type') === 'dealership' ? 'selected' : '' }}>
                Dealership
            </option>
        </select>
        {{-- Hidden input for id_entidad --}}
        <input type="hidden" name="id_entidad" id="id_entidad" value="{{ old('id_entidad') }}">
    </div>
        <div id="individual_fields" class="hidden">
        <div class="mt-4">
            <x-label for="dni" value="DNI" />
            <x-input
                id="dni"
                class="block mt-1 w-full"
                type="text"
                name="dni"
                :value="old('dni')"
            />
        </div>
          <div class="mt-4">
<x-label for="fecha_nacimiento" value="Date of birth" />
            <x-input
                id="fecha_nacimiento"
                class="block mt-1 w-full"
                type="date"
                name="fecha_nacimiento"
                :value="old('fecha_nacimiento')"
            />
        </div>
    </div>
           <div id="company_fields" class="hidden">
        <div class="mt-4">
            <x-label for="nombre_empresa" value="Company name" />
            <x-input
                id="nombre_empresa"
                class="block mt-1 w-full"
                type="text"
                name="nombre_empresa"
                :value="old('nombre_empresa')"
            />
        </div>

        <div class="mt-4">
            <x-label for="nif" value="VAT number" />
            <x-input
                id="nif"
                class="block mt-1 w-full"
                type="text"
                name="nif"
                :value="old('nif')"
            />
        </div>

        <div class="mt-4">
            <x-label for="direccion" value="Address" />
            <x-input
                id="direccion"
                class="block mt-1 w-full"
                type="text"
                name="direccion"
                :value="old('direccion')"
            />
        </div>
    </div>
        <div class="mt-4">
        <x-label for="telefono" value="Phone number" />
        <x-input
            id="telefono"
            class="block mt-1 w-full"
            type="text"
            name="telefono"
            :value="old('telefono')"
            required
        />
    </div>
     <div class="mt-4">
        <x-label for="email" value="Email" />
        <x-input
            id="email"
            class="block mt-1 w-full"
            type="email"
            name="email"
            :value="old('email')"
            required
            autocomplete="username"
        />
    </div>

       <div class="mt-4">
        <x-label for="password" value="Password" />
        <x-input
            id="password"
            class="block mt-1 w-full"
            type="password"
            name="password"
            required
            autocomplete="new-password"
        />
    </div>

          <div class="mt-4">
        <x-label for="password_confirmation" value="Confirm password" />
        <x-input
            id="password_confirmation"
            class="block mt-1 w-full"
            type="password"
            name="password_confirmation"
            required
            autocomplete="new-password"
        />
    </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ms-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const selectEntidad = document.getElementById('type');
                const inputIdEntidad = document.getElementById('id_entidad');
                const camposParticular = document.getElementById('individual_fields');
                const camposEmpresa = document.getElementById('company_fields');

                function actualizarCampos() {
                    const valor = selectEntidad.value;

                    if (valor == "individual") {
                        // Mostrar particular
                        camposParticular.classList.remove('hidden');
                        camposEmpresa.classList.add('hidden');
                        inputIdEntidad.value = "1";
                    }
                    else if (valor == "dealership") {
                        // Mostrar empresa
                        camposEmpresa.classList.remove('hidden');
                        camposParticular.classList.add('hidden');
                        inputIdEntidad.value = "2";
                    }
                    else {
                        camposParticular.classList.add('hidden');
                        camposEmpresa.classList.add('hidden');
                        inputIdEntidad.value = "";
                    }
                }

                selectEntidad.addEventListener('change', actualizarCampos);
                actualizarCampos()
            });
        </script>
    </x-authentication-card>
</x-guest-layout>
