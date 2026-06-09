<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCarModelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'sometimes|required|string|max:255',
            'id_marca' => 'sometimes|required|exists:brands,id',
            'carroceria' => 'sometimes|required|string|max:100',
        ];
    }
}
