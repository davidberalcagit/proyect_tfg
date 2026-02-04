<nav x-data="{ open: false }" class="bg-[#284961] border-b border-custom-border sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="hover:scale-110 transition duration-300 ease-in-out">
                        <x-application-mark class="block h-12 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link href="{{ route('cars.index') }}" :active="request()->routeIs('cars.index')" class="text-white hover:text-[#D1D5DB] active:text-white border-transparent hover:border-white focus:border-white">
                        {{ __('Cars') }}
                    </x-nav-link>

                    @auth
                        <!-- My Garage Dropdown -->
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <x-dropdown align="right" width="48">
                                <x-slot name="trigger">
                                    <button class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-[#D1D5DB] hover:border-gray-300 focus:outline-none focus:text-white focus:border-gray-300 transition duration-150 ease-in-out">
                                        <div>{{ __('My Garage') }}</div>

                                        <!-- Icono personalizado a la derecha -->
                                        <img src="{{ asset('icons/flecha-blanca.png') }}"
                                             class=" h-6 w-10 object-contain transition-transform duration-200"
                                             :class="{'rotate-180': open}"
                                             alt="Arrow"
                                             onerror="this.style.display='none'">
                                    </button>
                                </x-slot>

                                <x-slot name="content">
                                    <x-dropdown-link href="{{ route('cars.my_cars') }}">
                                        {{ __('My Cars') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('favorites.index') }}">
                                        {{ __('My Favorites') }}
                                    </x-dropdown-link>
                                    <x-dropdown-link href="{{ route('sales.index') }}">
                                        {{ __('My Transactions') }}
                                    </x-dropdown-link>
                                </x-slot>
                            </x-dropdown>
                        </div>

                        <!-- Admin Link -->
                        @role('admin')
                            <x-nav-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')" class="text-white hover:text-[#D1D5DB] active:text-white border-transparent hover:border-white focus:border-white">
                                {{ __('Admin Panel') }}
                            </x-nav-link>
                        @endrole

                        <!-- Supervisor Link -->
                        @hasanyrole('supervisor|admin')
                            <x-nav-link href="{{ route('supervisor.dashboard') }}" :active="request()->routeIs('supervisor.dashboard')" class="text-white hover:text-[#D1D5DB] active:text-white border-transparent hover:border-white focus:border-white">
                                {{ __('Supervisor Panel') }}
                            </x-nav-link>
                        @endhasanyrole

                        <!-- Support Link -->
                        @hasanyrole('soporte|admin')
                            <x-nav-link href="{{ route('support.users.index') }}" :active="request()->routeIs('support.users.*')" class="text-white hover:text-[#D1D5DB] active:text-white border-transparent hover:border-white focus:border-white">
                                {{ __('User Management') }}
                            </x-nav-link>
                        @endhasanyrole
                    @endauth
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Contact Link (Moved to Right) -->
                <x-nav-link href="{{ route('contact') }}"
                            :active="request()->routeIs('contact')" class="mr-4 flex items-center">
                    <img src="{{ asset('icons/sobre.png') }}" alt="Contact" class="w-6 h-6 hover:opacity-80 transition">
                </x-nav-link>


                <!-- Language Switcher -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-transparent hover:text-[#D1D5DB] focus:outline-none transition ease-in-out duration-150">
                            <span class="mr-1">{{ strtoupper(App::getLocale()) }}</span>
                            <svg class="h-4 w-4 fill-current transition-transform duration-200" :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
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
                                    <svg class="ms-2 -me-0.5 size-4 transition-transform duration-200" :class="{'rotate-180': open}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
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
                    <div class="hidden sm:flex sm:items-center sm:ms-6">
                        <x-nav-link href="{{ route('login') }}" :active="request()->routeIs('login')" class="text-white hover:text-[#D1D5DB] active:text-white">{{ __('Log in') }}</x-nav-link>
                        <x-nav-link href="{{ route('register') }}" :active="request()->routeIs('register')" class="text-white hover:text-[#D1D5DB] active:text-white">{{ __('Register') }}</x-nav-link>
                    </div>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-200 hover:bg-[#1c3344] focus:outline-none focus:bg-[#1c3344] focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6 transition-transform duration-300 ease-in-out" :class="{'rotate-90': open}" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div x-show="open"
         x-transition
         class="sm:hidden bg-[#284961] border-t border-gray-700"
         style="display: none;">

        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="{{ route('cars.index') }}" :active="request()->routeIs('cars.index')" class="text-white hover:bg-[#1c3344] hover:text-white border-l-4 border-transparent hover:border-white">
                {{ __('Cars') }}
            </x-responsive-nav-link>

            <!-- Contact Link (Mobile) -->
            <x-responsive-nav-link href="{{ route('contact') }}" :active="request()->routeIs('contact')" class="text-white hover:bg-[#1c3344] hover:text-white border-l-4 border-transparent hover:border-white">
                {{ __('Contact') }}
            </x-responsive-nav-link>

            @auth
                <!-- Mobile Group for My Garage -->
                <div class="border-t border-[#1c3344] my-1"></div>
                <div class="px-4 py-2 text-xs text-gray-400 uppercase font-bold flex items-center">
                    {{ __('My Garage') }}
                    <img src="{{ asset('icons/flecha-blanca.png') }}" class="ms-2 h-3 w-3 object-contain" alt="Arrow" onerror="this.style.display='none'">
                </div>

                <x-responsive-nav-link href="{{ route('cars.my_cars') }}" :active="request()->routeIs('cars.my_cars')" class="text-white hover:bg-[#1c3344] hover:text-white border-l-4 border-transparent hover:border-white pl-8">
                    {{ __('My Cars') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('favorites.index') }}" :active="request()->routeIs('favorites.index')" class="text-white hover:bg-[#1c3344] hover:text-white border-l-4 border-transparent hover:border-white pl-8">
                    {{ __('My Favorites') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link href="{{ route('sales.index') }}" :active="request()->routeIs('sales.index')" class="text-white hover:bg-[#1c3344] hover:text-white border-l-4 border-transparent hover:border-white pl-8">
                    {{ __('My Transactions') }}
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

                <!-- Opciones de Perfil Simplificadas en Móvil -->
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
