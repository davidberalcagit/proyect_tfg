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
            'nombre' => 'sometimes|required|unique:colors,nombre,' . $colorId
        ];
    }
}
