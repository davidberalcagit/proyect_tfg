<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Cars;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreApiSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->customer;
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(response()->json(['message' => 'No tienes perfil de vendedor.'], 403));
    }

    public function rules(): array
    {
        return [
            'id_vehiculo' => 'required|exists:cars,id',
            'id_comprador' => 'required|exists:customers,id',
            'precio' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'metodo_pago' => 'required|string|max:50',
            'estado' => 'required|exists:sale_statuses,id',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (empty($this->id_vehiculo) || $validator->errors()->has('id_vehiculo')) {
                return;
            }

            $car = Cars::find($this->id_vehiculo);
            $seller = Auth::user()->customer;

            if ($car && $car->id_vendedor !== $seller->id) {
                throw new HttpResponseException(response()->json(['message' => 'No puedes vender un coche que no es tuyo.'], 403));
            }

            if ($car && $car->id_estado == 3) {
                 throw new HttpResponseException(response()->json(['message' => 'Este coche ya ha sido vendido.'], 400));
            }
        });
    }
}
