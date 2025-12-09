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

            <div>
                <x-label for="id_entidad" value="{{ __('Type') }}" />
                <select id="id_entidad" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm" name="id_entidad" :value="old('id_entidad')" required>

                    <option value="" disabled selected>Elegir</option>
                    <option value="1" {{ old('id_entidad') == 1 ? 'selected' : '' }}>Particular</option>
                    <option value="2" {{ old('id_entidad') == 2 ? 'selected' : '' }}>Empresa</option>
                </select>
            </div>
            <div id="campos_particular" class="hidden">
                <div class="mt-4">
                    <x-label for="dni" value="DNI" />
                    <x-input id="dni" class="block mt-1 w-full" type="text" name="dni" :value="old('dni')" />
                </div>

                <div class="mt-4">
                    <x-label for="fecha_nacimiento" value="Fecha de nacimiento" />
                    <x-input id="fecha_nacimiento" class="block mt-1 w-full" type="date" name="fecha_nacimiento" :value="old('fecha_nacimiento')" />
                </div>
            </div>
            <div id="campos_empresa" class="hidden">
                <div class="mt-4">
                    <x-label for="nombre_empresa" value="Nombre empresa" />
                    <x-input id="nombre_empresa" class="block mt-1 w-full" type="text" name="nombre_empresa" :value="old('nombre_empresa')" />
                </div>

                <div class="mt-4">
                    <x-label for="nif" value="NIF" />
                    <x-input id="nif" class="block mt-1 w-full" type="text" name="nif" :value="old('nif')"/>
                </div>

                <div class="mt-4">
                    <x-label for="direccion" value="Dirección" />
                    <x-input id="direccion" class="block mt-1 w-full" type="text" name="direccion" :value="old('direccion')" />
                </div>
            </div>
            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
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
                const selectEntidad = document.getElementById('id_entidad');
                const camposParticular = document.getElementById('campos_particular');
                const camposEmpresa = document.getElementById('campos_empresa');

                function actualizarCampos() {
                    const valor = selectEntidad.value;

                    if (valor == "1") {
                        // Mostrar particular
                        camposParticular.classList.remove('hidden');
                        camposEmpresa.classList.add('hidden');
                    }
                    else if (valor == "2") {
                        // Mostrar empresa
                        camposEmpresa.classList.remove('hidden');
                        camposParticular.classList.add('hidden');
                    }
                    else {
                        camposParticular.classList.add('hidden');
                        camposEmpresa.classList.add('hidden');
                    }
                }

                selectEntidad.addEventListener('change', actualizarCampos);
                actualizarCampos()
            });
        </script>
    </x-authentication-card>
</x-guest-layout>
