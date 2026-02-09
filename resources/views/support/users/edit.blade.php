<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit User') }}: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <form method="POST" action="{{ route('support.users.update', $user) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6">
                        <!-- Nombre Usuario -->
                        <div>
                            <x-label for="name" value="{{ __('Username') }}" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                            <x-input-error for="name" class="mt-2" />
                        </div>

                        <!-- Email -->
                        <div>
                            <x-label for="email" value="{{ __('Email') }}" />
                            <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                            <x-input-error for="email" class="mt-2" />
                        </div>

                        <!-- Rol -->
                        <div>
                            <x-label for="role" value="{{ __('Role') }}" />
                            <x-select id="role" name="role" class="block mt-1 w-full">
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </x-select>
                            <x-input-error for="role" class="mt-2" />
                        </div>

                        <!-- Datos del Cliente -->
                        @if($user->customer)
                            <div class="border-t border-gray-200 pt-4 mt-2">
                                <h3 class="text-sm font-medium text-gray-900 mb-2">{{ __('Customer Details') }}</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Nombre de Contacto -->
                                    <div class="md:col-span-2">
                                        <x-label for="nombre_contacto" value="{{ __('Contact Name') }}" />
                                        <x-input id="nombre_contacto" class="block mt-1 w-full" type="text" name="nombre_contacto" :value="old('nombre_contacto', $user->customer->nombre_contacto)" />
                                        <x-input-error for="nombre_contacto" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-label for="telefono" value="{{ __('Phone') }}" />
                                        <x-input id="telefono" class="block mt-1 w-full" type="text" name="telefono" :value="old('telefono', $user->customer->telefono)" />
                                        <x-input-error for="telefono" class="mt-2" />
                                    </div>

                                    @if($user->customer->individual)
                                        <div>
                                            <x-label for="dni" value="{{ __('DNI') }}" />
                                            <x-input id="dni" class="block mt-1 w-full" type="text" name="dni" :value="old('dni', $user->customer->individual->dni)" />
                                            <x-input-error for="dni" class="mt-2" />
                                        </div>
                                    @endif

                                    @if($user->customer->dealership)
                                        <div>
                                            <x-label for="nif" value="{{ __('NIF') }}" />
                                            <x-input id="nif" class="block mt-1 w-full" type="text" name="nif" :value="old('nif', $user->customer->dealership->nif)" />
                                            <x-input-error for="nif" class="mt-2" />
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Password (Opcional) -->
                        <div class="border-t border-gray-200 pt-4 mt-2">
                            <h3 class="text-sm font-medium text-gray-900 mb-2">{{ __('Change Password (Optional)') }}</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-label for="password" value="{{ __('New Password') }}" />
                                    <x-input id="password" class="block mt-1 w-full" type="password" name="password" autocomplete="new-password" />
                                    <x-input-error for="password" class="mt-2" />
                                </div>

                                <div>
                                    <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                                    <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" autocomplete="new-password" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('support.users.show', $user) }}" class="text-gray-600 hover:text-gray-900 mr-4">{{ __('Cancel') }}</a>
                        <x-button>
                            {{ __('Update User') }}
                        </x-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
