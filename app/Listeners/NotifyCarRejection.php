<?php

namespace App\Listeners;

use App\Events\CarRejected;
use App\Jobs\SendCarStatusNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyCarRejection
{
    public function handle(CarRejected $event): void
    {
        SendCarStatusNotificationJob::dispatch($event->car, 'rejected', $event->reason);
    }
}
