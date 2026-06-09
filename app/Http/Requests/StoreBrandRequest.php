<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|unique:brands,nombre',
            'models' => 'nullable|array',
            'models.*' => 'nullable|string|max:255'
        ];
    }
}
