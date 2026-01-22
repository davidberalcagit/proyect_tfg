<?php

namespace App\Listeners;

use App\Events\RentalPaid;
use App\Jobs\SendRentalProcessedJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyRentalParticipants
{
    public function handle(RentalPaid $event): void
    {
        SendRentalProcessedJob::dispatch($event->rental);
    }
}
