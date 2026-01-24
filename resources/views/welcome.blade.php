<x-app-layout>
    <div class="mx-auto mt-10 max-w-6xl px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h1 class="mt-4 mb-8 font-serif text-4xl font-extrabold text-[#284961] md:text-5xl">
                {{ __('Welcome to Vehicle Platform') }}
            </h1>
            <p class="mb-8 text-lg text-gray-600">
                {{ __('Find the best cars for sale and rent.') }}
            </p>
            <div>
                <a href="{{ route('cars.index') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-[#B35F12] hover:bg-[#9A5210] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#B35F12] transition">
                    {{ __('View Cars') }}
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
