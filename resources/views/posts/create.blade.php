<x-app-layout meta-title="Create new post" meta-description="Form to create a new Post">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create new post') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg ">
                <div class="p-6 text-gray-900 ">
                    <form method="POST"
                          action="{{ route('posts.store') }}"
                          class="space-y-4 max-w-xl"
                    >
                        @include('posts.form_fields')
                        <x-primary-button type="submit">{{ __('Save') }}</x-primary-button>
                        @csrf
                        <a class="'inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-600 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 transition ease-in-out duration-150'" href="{{ route('posts.index') }}">{{ __('Back') }}</a>

                    </form>

                </div>
            </div>
        </div>
    </div>

</x-app-layout>
