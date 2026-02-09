<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'telefono' => 'required|string|max:20',
            'nombre_contacto' => 'required|string|max:255',
            'id_entidad' => 'required|exists:entity_types,id',
            'nombre_empresa' => 'required_if:id_entidad,2|string|max:255|nullable',
            'nif' => 'required_if:id_entidad,2|string|max:20|nullable',
            'direccion' => 'required_if:id_entidad,2|string|max:255|nullable',
        ];
    }
}
