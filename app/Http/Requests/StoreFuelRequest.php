<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFuelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255|unique:fuels,nombre',
            'emission_type' => ['required', 'string', Rule::in(['E5', 'E10', 'B7', 'B10', 'H2', 'LNG', 'ZERO', 'ECO'])],
        ];
    }
}
