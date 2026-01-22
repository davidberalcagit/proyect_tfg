<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('User Details') }}: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Info Básica -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Basic Information') }}</h3>
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Name') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->name }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Email') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->email }}</dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Role') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    @foreach($user->roles as $role)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endforeach
                                </dd>
                            </div>
                            <div class="sm:col-span-1">
                                <dt class="text-sm font-medium text-gray-500">{{ __('Registered') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $user->created_at->format('d/m/Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Info Cliente -->
                    @if($user->customer)
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Customer Profile') }}</h3>
                            <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Contact Name') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->customer->nombre_contacto }}</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Phone') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->customer->telefono }}</dd>
                                </div>
                                <div class="sm:col-span-1">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Type') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $user->customer->entityType->nombre ?? 'N/A' }}</dd>
                                </div>
                            </dl>
                        </div>
                    @endif
                </div>

                <div class="border-t border-gray-200 mt-6 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Activity Summary') }}</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <span class="block text-2xl font-bold text-indigo-600">{{ $user->customer ? $user->customer->cars->count() : 0 }}</span>
                            <span class="text-sm text-gray-500">{{ __('Cars Listed') }}</span>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg text-center">
                            <span class="block text-2xl font-bold text-green-600">{{ $user->customer ? $user->customer->rentals->count() : 0 }}</span>
                            <span class="text-sm text-gray-500">{{ __('Rentals Made') }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-4">
                    <a href="{{ route('support.users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                        {{ __('Back to list') }}
                    </a>

                    @can('update', $user)
                        <a href="{{ route('support.users.edit', $user) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition">
                            {{ __('Edit User') }}
                        </a>
                    @endcan

                    @can('delete', $user)
                        <form action="{{ route('support.users.destroy', $user) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this user?') }}');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:ring focus:ring-red-300 disabled:opacity-25 transition">
                                {{ __('Delete User') }}
                            </button>
                        </form>
                    @endcan
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
