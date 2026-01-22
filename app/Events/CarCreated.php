<?php

namespace App\Events;

use App\Models\Cars;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CarCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $car;

    public function __construct(Cars $car)
    {
        $this->car = $car;
    }
}
