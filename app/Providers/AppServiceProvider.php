<?php

namespace App\Providers;

use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);

        Event::listen(
            \App\Events\CarCreated::class,
            \App\Listeners\LogCarCreation::class,
        );

        Event::listen(
            \App\Events\OfferCreated::class,
            \App\Listeners\NotifySeller::class,
        );

        // Register listeners individually
        Event::listen(
            \App\Events\SaleCompleted::class,
            \App\Listeners\NotifySaleParticipants::class
        );

        // Check if LogSaleActivity exists before registering, or just register it if we know it exists.
        // Assuming it exists based on previous code, but if not, removing it is safer.
        // The error was about NotifySaleParticipants::__invoke, which happens when passing array to listen().
        if (class_exists(\App\Listeners\LogSaleActivity::class)) {
            Event::listen(
                \App\Events\SaleCompleted::class,
                \App\Listeners\LogSaleActivity::class
            );
        }

        Event::listen(
            \App\Events\RentalPaid::class,
            \App\Listeners\NotifyRentalParticipants::class,
        );

        Event::listen(
            \App\Events\CarRejected::class,
            \App\Listeners\NotifyCarRejection::class,
        );
    }
}
