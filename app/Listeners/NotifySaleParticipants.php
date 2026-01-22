<?php

namespace App\Listeners;

use App\Events\SaleCompleted;
use App\Jobs\SendSaleProcessedJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifySaleParticipants
{
    public function handle(SaleCompleted $event): void
    {
        SendSaleProcessedJob::dispatch($event->sale);
    }
}
