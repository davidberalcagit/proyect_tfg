<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();

        if (!$user) return false;

        // Admin tiene permiso total
        if ($user->hasRole('admin')) {
            return true;
        }

        $car = $this->route('car'); // Obtener el coche de la ruta

        // Verificar permisos: Dueño y estado pendiente
        if (!$user->customer) return false;

        if ($car->id_vendedor !== $user->customer->id) return false;

        if ($car->id_estado == 1) return false; // No editar aprobados si no eres admin

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_marca' => 'nullable|exists:brands,id',
            'id_modelo' => 'nullable|exists:car_models,id',
            'id_combustible' => 'required|integer|exists:fuels,id',
            'id_marcha' => 'required|integer|exists:gears,id',
            'precio' => 'required|numeric|min:0',
            'anyo_matri' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'km' => 'required|integer',
            'matricula' => 'required|string|max:255',
            'id_color' => 'nullable|exists:colors,id',
            'descripcion' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
