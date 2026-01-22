<?php

namespace App\Listeners;

use App\Events\OfferCreated;
use App\Jobs\SendOfferNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifySeller
{
    public function handle(OfferCreated $event): void
    {
        SendOfferNotificationJob::dispatch($event->offer);
    }
}
