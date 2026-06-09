<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Cars;
use App\Models\Offer;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreApiOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->customer;
    }

    public function failedAuthorization()
    {
        throw new HttpResponseException(response()->json(['message' => 'Debes crear un perfil de cliente para hacer ofertas.'], 403));
    }

    public function rules(): array
    {
        return [
            'id_vehiculo' => 'required|exists:cars,id',
            'precio_oferta' => 'required|numeric|min:0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (empty($this->id_vehiculo) || $validator->errors()->has('id_vehiculo')) {
                return;
            }

            $buyer = Auth::user()->customer;
            $car = Cars::find($this->id_vehiculo);

            if ($car && $car->id_vendedor === $buyer->id) {
                throw new HttpResponseException(response()->json(['message' => 'No puedes hacer una oferta por tu propio coche.'], 400));
            }

            $existingOffer = Offer::where('id_vehiculo', $car->id)
                ->where('id_comprador', $buyer->id)
                ->pending()
                ->exists();

            if ($existingOffer) {

                throw new HttpResponseException(response()->json(['message' => 'Ya tienes una oferta pendiente para este coche.'], 409));
            }
        });
    }
}
