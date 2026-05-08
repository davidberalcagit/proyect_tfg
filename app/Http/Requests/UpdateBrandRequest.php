<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $brandId = $this->route('brand') ?? $this->route('id');
        return [
            'nombre' => 'sometimes|required|unique:brands,nombre,' . $brandId
        ];
    }
}
