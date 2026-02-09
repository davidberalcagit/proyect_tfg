<nav x-data="{ open: false }" class="bg-[#284961] border-b border-custom-border sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <!-- Cambiado max-w-7xl a un ancho más fluido para pantallas grandes -->
    <div class="max-w-[95%] 2xl:max-w-[90%] mx-auto px-4 sm:px-6 lg:px-8 transition-all duration-300">
        <div class="flex justify-between h-20">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="hover:scale-110 transition duration-300 ease-in-out">
                        <x-application-mark class="block h-10 w-auto animate-pulse" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link href="{{ route('cars.index') }}" :active="request()->routeIs('cars.index')" class="text-white hover:text-[#D1D5DB] active:text-white border-transparent hover:border-white focus:border-white">
                        {{ __('Cars') }}
                    </x-nav-link>

                    @auth
                        <!-- My Garage Dropdown -->
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-transparent hover:text-[#D1D5DB] focus:outline-none transition ease-in-out duration-150">
                                        <span>{{ __('My Garage') }}</span>

                                        <!-- Badge Logic -->
                                        @php
                                            $notificationCount = 0;
                                            if(Auth::user()->customer) {
                                                $customerId = Auth::user()->customer->id;
                                                $pendingOffers = \App\Models\Offer::where('id_vendedor', $customerId)->where('estado', 'pending')->count();
                                                $acceptedOffers = \App\Models\Offer::where('id_comprador', $customerId)->where('estado', 'accepted_by_seller')->count();
                                                $pendingRentals = \App\Models\Rental::whereHas('car', function($q) use ($customerId) { $q->where('id_vendedor', $customerId); })->where('id_estado', 1)->count();
                                                $acceptedRentals = \App\Models\Rental::where('id_cliente', $customerId)->where('id_estado', 7)->count();
                                                $notificationCount = $pendingOffers + $acceptedOffers + $pendingRentals + $acceptedRentals;
                                            }
                                        @endphp

                                        @if($notificationCount > 0)
                                            <span class="ml-2 flex h-2 w-2 relative">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                            </span>
                                        @endif

                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link href="{{ route('cars.my_cars') }}">
                                        {{ __('My Cars') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('favorites.index') }}">
                                        {{ __('My Favorites') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('sales.index') }}" class="flex justify-between items-center">
                                        {{ __('My Transactions') }}
                                        @if($notificationCount > 0)
                                            <span class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full ml-2">{{ $notificationCount }}</span>
                                        @endif
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        @role('admin')
                            <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')" class="text-white hover:text-[#D1D5DB] active:text-white border-transparent hover:border-white focus:border-white">
                                {{ __('Admin Panel') }}
                            </x-nav-link>
                        @endrole

                        @hasanyrole('supervisor|admin')
                            <x-nav-link href="{{ route('supervisor.dashboard') }}" :active="request()->routeIs('supervisor.dashboard')" class="text-white hover:text-[#D1D5DB] active:text-white border-transparent hover:border-white focus:border-white">
                                {{ __('Supervisor Panel') }}
                            </x-nav-link>
                        @endhasanyrole

                        @hasanyrole('soporte|admin')
                            <x-nav-link href="{{ route('support.users.index') }}" :active="request()->routeIs('support.users.*')" class="text-white hover:text-[#D1D5DB] active:text-white border-transparent hover:border-white focus:border-white">
                                {{ __('User Management') }}
                            </x-nav-link>
                        @endhasanyrole
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Contact Link -->
                <a href="{{ route('contact') }}"
                   x-data="{ hover: false }"
                   @mouseenter="hover = true"
                   @mouseleave="hover = false"
                   class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-[#D1D5DB] focus:outline-none transition duration-150 ease-in-out mr-4"
                   title="{{ __('Contact') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                         class="h-6 w-6"
                         x-show="!hover && !{{ request()->routeIs('contact') ? 'true' : 'false' }}"
                         style="{{ request()->routeIs('contact') ? 'display: none;' : '' }}">
                        <path d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z" />
                        <path d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                         class="h-6 w-6"
                         x-show="hover || {{ request()->routeIs('contact') ? 'true' : 'false' }}"
                         style="{{ request()->routeIs('contact') ? '' : 'display: none;' }}"
                         x-cloak>
                        <path d="M19.5 22.5a3 3 0 0 0 3-3v-8.174l-6.879 4.022 3.485 1.876a.75.75 0 1 1-.712 1.321l-5.683-3.06a1.5 1.5 0 0 0-1.422 0l-5.683 3.06a.75.75 0 0 1-.712-1.32l3.485-1.877L1.5 11.326V19.5a3 3 0 0 0 3 3h15Z" />
                        <path d="M1.5 9.589v-.745a3 3 0 0 1 1.578-2.642l7.5-4.038a3 3 0 0 1 2.844 0l7.5 4.038A3 3 0 0 1 22.5 8.844v.745l-8.426 4.926-.652-.351a3 3 0 0 0-2.844 0l-.652.351L1.5 9.589Z" />
                    </svg>
                </a>

                <!-- Language Switcher -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-transparent hover:text-[#D1D5DB] focus:outline-none transition ease-in-out duration-150">
                            <span class="mr-1">{{ strtoupper(App::getLocale()) }}</span>
                            <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link href="{{ route('lang.switch', 'en') }}">English</x-dropdown-link>
                        <x-dropdown-link href="{{ route('lang.switch', 'es') }}">Español</x-dropdown-link>
                    </x-slot>
                </x-dropdown>

                @auth
                    <!-- Settings Dropdown -->
                    <div class="ms-3 relative">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-transparent hover:text-[#D1D5DB] focus:outline-none transition ease-in-out duration-150">
                                    {{ Auth::user()->name }}
                                    <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Manage Account') }}</div>
                                <x-dropdown-link href="{{ route('profile.show') }}">{{ __('Profile') }}</x-dropdown-link>
                                <div class="border-t border-gray-200"></div>
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">{{ __('Log Out') }}</x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                @else
                    <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                        <x-nav-link href="{{ route('login') }}" :active="request()->routeIs('login')" class="text-white hover:text-[#D1D5DB] active:text-white">{{ __('Log in') }}</x-nav-link>
                        <x-nav-link href="{{ route('register') }}" :active="request()->routeIs('register')" class="text-white hover:text-[#D1D5DB] active:text-white">{{ __('Register') }}</x-nav-link>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-200 hover:bg-[#1c3344] focus:outline-none focus:bg-[#1c3344] focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#284961]">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('cars.index') }}" :active="request()->routeIs('cars.index')" class="text-white hover:bg-[#1c3344] hover:text-white border-l-4 border-transparent hover:border-white">
                {{ __('Cars') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link href="{{ route('contact') }}" :active="request()->routeIs('contact')" class="text-white hover:bg-[#1c3344] hover:text-white border-l-4 border-transparent hover:border-white flex items-center" x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                <!-- Icono Sobre Cerrado -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                     class="h-5 w-5 mr-2"
                     x-show="!hover && !{{ request()->routeIs('contact') ? 'true' : 'false' }}"
                     style="{{ request()->routeIs('contact') ? 'display: none;' : '' }}">
                    <path d="M1.5 8.67v8.58a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3V8.67l-8.928 5.493a3 3 0 0 1-3.144 0L1.5 8.67Z" />
                    <path d="M22.5 6.908V6.75a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3v.158l9.714 5.978a1.5 1.5 0 0 0 1.572 0L22.5 6.908Z" />
                </svg>

                <!-- Icono Sobre Abierto -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                     class="h-5 w-5 mr-2"
                     x-show="hover || {{ request()->routeIs('contact') ? 'true' : 'false' }}"
                     style="{{ request()->routeIs('contact') ? '' : 'display: none;' }}"
                     x-cloak>
                    <path d="M19.5 22.5a3 3 0 0 0 3-3v-8.174l-6.879 4.022 3.485 1.876a.75.75 0 1 1-.712 1.321l-5.683-3.06a1.5 1.5 0 0 0-1.422 0l-5.683 3.06a.75.75 0 0 1-.712-1.32l3.485-1.877L1.5 11.326V19.5a3 3 0 0 0 3 3h15Z" />
                    <path d="M1.5 9.589v-.745a3 3 0 0 1 1.578-2.642l7.5-4.038a3 3 0 0 1 2.844 0l7.5 4.038A3 3 0 0 1 22.5 8.844v.745l-8.426 4.926-.652-.351a3 3 0 0 0-2.844 0l-.652.351L1.5 9.589Z" />
                </svg>
                {{ __('Contact') }}
            </x-responsive-nav-link>

            @auth
                <div class="border-t border-[#1c3344] my-1"></div>
                <div class="px-4 py-2 text-xs text-gray-400 uppercase font-bold flex items-center">
                    {{ __('My Garage') }}
                    @if($notificationCount > 0)
                        <span class="ml-2 flex h-2 w-2 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                    @endif
                </div>

                <x-responsive-nav-link href="{{ route('cars.my_cars') }}" :active="request()->routeIs('cars.my_cars')" class="text-white hover:bg-[#1c3344] hover:text-white border-l-4 border-transparent hover:border-white pl-8">
                    {{ __('My Cars') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('favorites.index') }}" :active="request()->routeIs('favorites.index')" class="text-white hover:bg-[#1c3344] hover:text-white border-l-4 border-transparent hover:border-white pl-8">
                    {{ __('My Favorites') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('sales.index') }}" :active="request()->routeIs('sales.index')" class="text-white hover:bg-[#1c3344] hover:text-white border-l-4 border-transparent hover:border-white pl-8 flex justify-between items-center">
                    {{ __('My Transactions') }}
                    @if($notificationCount > 0)
                        <span class="bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full ml-2">{{ $notificationCount }}</span>
                    @endif
                </x-responsive-nav-link>

                <div class="border-t border-[#1c3344] my-1"></div>

                @role('admin')
                    <x-responsive-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')" class="text-white hover:bg-[#1c3344] hover:text-white border-l-4 border-transparent hover:border-white">
                        {{ __('Admin Panel') }}
                    </x-responsive-nav-link>
                @endrole

                @hasanyrole('supervisor|admin')
                    <x-responsive-nav-link href="{{ route('supervisor.dashboard') }}" :active="request()->routeIs('supervisor.dashboard')" class="text-white hover:bg-[#1c3344] hover:text-white border-l-4 border-transparent hover:border-white">
                        {{ __('Supervisor Panel') }}
                    </x-responsive-nav-link>
                @endhasanyrole

                @hasanyrole('soporte|admin')
                    <x-responsive-nav-link href="{{ route('support.users.index') }}" :active="request()->routeIs('support.users.*')" class="text-white hover:bg-[#1c3344] hover:text-white border-l-4 border-transparent hover:border-white">
                        {{ __('User Management') }}
                    </x-responsive-nav-link>
                @endhasanyrole

                <!-- Opciones de Perfil -->
                <div class="border-t border-[#1c3344] my-2"></div>

                <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')" class="text-white hover:bg-[#1c3344] hover:text-white border-l-4 border-transparent hover:border-white">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}" x-data>
                    @csrf
                    <x-responsive-nav-link href="{{ route('logout') }}" @click.prevent="$root.submit();" class="text-white hover:bg-[#1c3344] hover:text-white border-l-4 border-transparent hover:border-white">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            @else
                <x-responsive-nav-link href="{{ route('login') }}" :active="request()->routeIs('login')" class="text-white hover:bg-[#1c3344] hover:text-white border-l-4 border-transparent hover:border-white">
                    {{ __('Log in') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link href="{{ route('register') }}" :active="request()->routeIs('register')" class="text-white hover:bg-[#1c3344] hover:text-white border-l-4 border-transparent hover:border-white">
                    {{ __('Register') }}
                </x-responsive-nav-link>
            @endauth
        </div>
    </div>
</nav>
