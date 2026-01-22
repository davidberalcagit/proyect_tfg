<?php

namespace App\Listeners;

use App\Events\CarRejected;
use App\Jobs\SendCarRejectedNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyCarRejection
{
    public function handle(CarRejected $event): void
    {
        SendCarRejectedNotificationJob::dispatch($event->car, $event->reason);
    }
}
