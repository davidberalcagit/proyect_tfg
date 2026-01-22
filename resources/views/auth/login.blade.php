<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-between mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button class="ms-4">
                    {{ __('Log in') }}
                </x-button>
            </div>

            <div class="flex items-center justify-center mt-6 pt-4 border-t border-gray-200">
                <a href="{{ route('register') }}" class="text-sm text-indigo-600 hover:text-indigo-900 font-semibold">
                    {{ __('Not registered yet?') }} {{ __('Register') }}
                </a>
            </div>

            <!-- Language Switcher -->
            <div class="mt-6 flex justify-center space-x-4 text-sm text-gray-500">
                <a href="{{ route('lang.switch', 'en') }}" class="hover:text-gray-900 {{ App::getLocale() == 'en' ? 'font-bold text-indigo-600' : '' }}">English</a>
                <span>|</span>
                <a href="{{ route('lang.switch', 'es') }}" class="hover:text-gray-900 {{ App::getLocale() == 'es' ? 'font-bold text-indigo-600' : '' }}">Español</a>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
