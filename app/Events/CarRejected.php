<?php

namespace App\Events;

use App\Models\Cars;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CarRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $car;
    public $reason;

    public function __construct(Cars $car, string $reason)
    {
        $this->car = $car;
        $this->reason = $reason;
    }
}
