<?php

namespace App\Events;

use App\Models\Sales;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SaleCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sale;

    public function __construct(Sales $sale)
    {
        $this->sale = $sale;
    }
}
