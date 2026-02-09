<?php

namespace App\Http\Controllers;

use App\Models\Cars;
use App\Models\Customers;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function show(Customers $customer)
    {
        $cars = Cars::where('id_vendedor', $customer->id)
                    ->available()
                    ->latest()
                    ->paginate(12);

        return view('seller.show', compact('customer', 'cars'));
    }
}
