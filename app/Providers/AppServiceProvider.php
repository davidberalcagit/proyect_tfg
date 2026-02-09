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

    }


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

        Event::listen(
            \App\Events\SaleCompleted::class,
            \App\Listeners\NotifySaleParticipants::class
        );

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
