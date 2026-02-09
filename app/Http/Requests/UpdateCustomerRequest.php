<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'telefono' => 'sometimes|string|max:20',
            'nombre_contacto' => 'sometimes|string|max:255',
            'nombre_empresa' => 'sometimes|string|max:255|nullable',
            'direccion' => 'sometimes|string|max:255|nullable',
        ];
    }
}
