<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'nombre' => 'sometimes|required|string|max:255|unique:fuels,nombre,' . $fuelId,
            'emission_type' => ['sometimes', 'required', 'string', Rule::in(['E5', 'E10', 'B7', 'B10', 'H2', 'LNG', 'ZERO', 'ECO'])],
        ];
    }
}
