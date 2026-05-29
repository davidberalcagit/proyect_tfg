<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Offer;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateApiOfferRequest extends FormRequest
{
    private $offerInstance;

    public function authorize(): bool
    {
        if (!Auth::check() || !Auth::user()->customer) {
            return false;
        }

        $id = $this->route('offer') ?? $this->route('id');
        $this->offerInstance = Offer::find($id);

        if (!$this->offerInstance) {
            return true; // Let 404 happen naturally later if not found
        }

        $userCustomer = Auth::user()->customer;
        return ($this->offerInstance->id_comprador === $userCustomer->id || $this->offerInstance->id_vendedor === $userCustomer->id);
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(response()->json(['message' => 'No tienes permiso para modificar esta oferta.'], 403));
    }

    public function rules(): array
    {
        $id = $this->route('offer') ?? $this->route('id');
        $this->offerInstance = $this->offerInstance ?? Offer::find($id);

        if (!$this->offerInstance) return [];

        $userCustomer = Auth::user()->customer;

        if ($this->offerInstance->id_comprador === $userCustomer->id) {
            return [
                'precio_oferta' => 'numeric|min:0',
            ];
        } elseif ($this->offerInstance->id_vendedor === $userCustomer->id) {
            return [
                'estado' => 'required|in:aceptada,rechazada,pendiente',
            ];
        }

        return [];
    }
}
