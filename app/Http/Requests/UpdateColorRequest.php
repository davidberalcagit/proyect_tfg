<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateColorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $colorId = $this->route('color') ?? $this->route('id');
        return [
            'nombre' => 'sometimes|required|string|max:255|unique:colors,nombre,' . $colorId,
            'hex_code' => 'sometimes|nullable|string|max:7|starts_with:#',
        ];
    }
}
