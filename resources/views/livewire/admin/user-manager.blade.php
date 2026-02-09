<div class="p-6 bg-white rounded-lg shadow-md">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <h2 class="text-xl font-bold text-[#284961]">Gestión de Usuarios</h2>

        <div class="flex w-full md:w-auto gap-4">
            <div class="w-full md:w-64">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar usuario..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-[#B35F12] focus:ring focus:ring-[#B35F12] focus:ring-opacity-50 text-sm">
            </div>

            <a href="{{ route('support.users.create') }}" class="inline-flex items-center px-4 py-2 bg-[#B35F12] hover:bg-[#9A5210] text-white font-bold rounded shadow transition duration-150 ease-in-out text-sm whitespace-nowrap">
                + Nuevo Usuario
            </a>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded shadow-sm">
            <p class="font-bold">Éxito</p>
            <p>{{ session('message') }}</p>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded shadow-sm">
            <p class="font-bold">Error</p>
            <p>{{ session('error') }}</p>
        </div>
    @endif

    <div class="overflow-x-auto bg-white rounded-lg shadow border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-[#284961] text-white">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-[#1c3344] transition" wire:click="sortBy('id')">
                        ID
                        @if($sortField === 'id')
                            <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                        @endif
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-[#1c3344] transition" wire:click="sortBy('name')">
                        Nombre
                        @if($sortField === 'name')
                            <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                        @endif
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-[#1c3344] transition" wire:click="sortBy('email')">
                        Email
                        @if($sortField === 'email')
                            <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                        @endif
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider cursor-pointer hover:bg-[#1c3344] transition" wire:click="sortBy('seller_name')">
                        Nombre Vendedor
                        @if($sortField === 'seller_name')
                            <span>{!! $sortDirection === 'asc' ? '&#8593;' : '&#8595;' !!}</span>
                        @endif
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider">
                        Rol
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($users as $user)
                    <tr wire:key="user-{{ $user->id }}" class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->customer->nombre_contacto ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $user->hasRole('admin') ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ $user->getRoleNames()->first() ?? 'Usuario' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end space-x-2">
                                <a href="{{ route('support.users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900 font-bold transition mr-2">
                                    Ver
                                </a>
                                <button wire:click="delete({{ $user->id }})"
                                        wire:confirm="¿Estás seguro de que quieres eliminar a este usuario?"
                                        class="text-red-600 hover:text-red-900 font-bold transition">
                                    Eliminar
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>
