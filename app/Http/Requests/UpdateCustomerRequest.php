<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization logic is currently in the controller (checking ID).
        // We can move it here or keep it in controller.
        // For now, return true and let controller handle specific resource ownership
        // or implement logic here: $this->route('customer')->id_usuario === Auth::id()
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
