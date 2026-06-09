<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\Sales;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateApiSaleRequest extends FormRequest
{
    private $saleInstance;

    public function authorize(): bool
    {
        if (!Auth::check() || !Auth::user()->customer) {
            return false;
        }

        $id = $this->route('sale') ?? $this->route('id');
        $this->saleInstance = Sales::find($id);

        if (!$this->saleInstance) {
            return true;
        }

        return $this->saleInstance->id_vendedor === Auth::user()->customer->id;
    }

    protected function failedAuthorization()
    {
        throw new HttpResponseException(response()->json(['message' => 'No tienes permiso para editar esta venta.'], 403));
    }

    public function rules(): array
    {
        return [
            'precio' => 'numeric|min:0',
            'fecha' => 'date',
            'metodo_pago' => 'string|max:50',
            'estado' => 'exists:sale_statuses,id',
        ];
    }
}
