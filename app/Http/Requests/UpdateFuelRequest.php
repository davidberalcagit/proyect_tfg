<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFuelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $fuelId = $this->route('fuel') ?? $this->route('id');
        return [
            'nombre' => 'sometimes|required|unique:fuels,nombre,' . $fuelId
        ];
    }
}
