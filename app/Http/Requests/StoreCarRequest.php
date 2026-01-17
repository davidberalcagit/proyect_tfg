<?php

namespace App\Http\Requests;

use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Color;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class StoreCarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // El usuario debe estar autenticado y tener perfil de cliente
        $user = Auth::user();
        return $user && $user->customer;
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

            'temp_brand' => 'required_without:id_marca|nullable|string|max:50',
            'temp_model' => 'required_without:id_modelo|nullable|string|max:50',

            'id_combustible' => 'required|integer|exists:fuels,id',
            'id_marcha' => 'required|integer|exists:gears,id',
            'precio' => 'required|numeric|min:0',
            'anyo_matri' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'km' => 'required|integer',
            'matricula' => ['required', 'string', 'max:255'],

            'id_color' => 'nullable|exists:colors,id',
            'temp_color' => 'required_without:id_color|nullable|string|max:50',

            'descripcion' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Si viene "other", lo convertimos a null
        $this->merge([
            'id_marca' => $this->id_marca === 'other' ? null : $this->id_marca,
            'id_modelo' => $this->id_modelo === 'other' ? null : $this->id_modelo,
            'id_color' => $this->id_color === 'other' ? null : $this->id_color,
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validaciones personalizadas de duplicados
            if ($this->temp_brand) {
                $tempBrand = trim($this->temp_brand);
                if (Brands::where('nombre', 'LIKE', $tempBrand)->exists()) {
                    $validator->errors()->add('temp_brand', 'La marca "' . $tempBrand . '" ya existe. Por favor selecciónela de la lista.');
                }
            }

            if ($this->temp_model) {
                $tempModel = trim($this->temp_model);
                if ($this->id_marca) {
                    if (CarModels::where('nombre', 'LIKE', $tempModel)->where('id_marca', $this->id_marca)->exists()) {
                        $validator->errors()->add('temp_model', 'El modelo "' . $tempModel . '" ya existe para esta marca.');
                    }
                }
            }

            if ($this->temp_color) {
                $tempColor = trim($this->temp_color);
                if (Color::where('nombre', 'LIKE', $tempColor)->exists()) {
                    $validator->errors()->add('temp_color', 'El color "' . $tempColor . '" ya existe. Por favor selecciónelo de la lista.');
                }
            }
        });
    }
}
