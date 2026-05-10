<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Offer;

class StoreOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->can('create', [Offer::class, $this->route('car')]);
    }

    public function rules(): array
    {
        return [
            'cantidad' => 'required|numeric|min:1'
        ];
    }
}
