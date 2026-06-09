<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cars;
use App\Models\Offer;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Api\StoreApiOfferRequest;

/**
 * @group Ofertas
 *
 * Gestión de ofertas de compra y venta.
 */
class OfferController extends Controller
{
    /**
     * Listar Mis Ofertas
     * Muestra las ofertas realizadas por el usuario (como comprador) y las recibidas (como vendedor).
     * @authenticated
     * @queryParam page int El número de página. Example: 1
     *
     * @response {
     *  "data": [
     *    {
     *      "id": 1,
     *      "cantidad": 14000,
     *      "estado": "pending",
     *      "car": { "id": 1, "title": "Toyota Corolla" },
     *      "buyer": { "id": 2, "nombre_contacto": "Juan Perez" },
     *      "seller": { "id": 3, "nombre_contacto": "Concesionario X" }
     *    }
     *  ],
     *  "links": { ... },
     *  "meta": { ... }
     * }
     */
    public function index()
    {
        $userCustomer = Auth::user()->customer;

        if (!$userCustomer) {
            return response()->json(['message' => 'Usuario no tiene perfil de cliente'], 403);
        }

        return Offer::with(['car', 'buyer', 'seller'])
            ->where(function ($query) use ($userCustomer) {
                $query->where('id_comprador', $userCustomer->id)
                      ->orWhere('id_vendedor', $userCustomer->id);
            })
            ->paginate(20);
    }

    /**
     * Crear Oferta
     *
     * Envía una oferta por un coche.
     *
     * @authenticated
     * @bodyParam id_vehiculo int required ID del coche. Example: 5
     * @bodyParam precio_oferta number required Cantidad ofrecida. Example: 14000
     *
     * @response 201 {
     *  "id": 10,
     *  "id_vehiculo": 5,
     *  "cantidad": 14000,
     *  "estado": "pending",
     *  "created_at": "2023-10-27T10:00:00.000000Z"
     * }
     * @response 409 {
     *  "message": "Ya tienes una oferta pendiente para este coche."
     * }
     * @response 400 {
     *  "message": "No puedes hacer una oferta por tu propio coche."
     * }
     */
    public function store(StoreApiOfferRequest $request)
    {
        $buyer = Auth::user()->customer;
        $car = Cars::findOrFail($request->id_vehiculo);

        $offer = Offer::create([
            'id_vehiculo' => $car->id,
            'id_vendedor' => $car->id_vendedor,
            'id_comprador' => $buyer->id,
            'cantidad' => $request->precio_oferta,
            'estado' => 'pending',
        ]);

        return response()->json($offer, 201);
    }

    /**
     * Ver Oferta
     *
     * Muestra los detalles de una oferta. Solo visible para el comprador o el vendedor.
     *
     * @authenticated
     * @urlParam id int required El ID de la oferta. Example: 1
     *
     * @response {
     *  "id": 1,
     *  "cantidad": 14000,
     *  "estado": "pending",
     *  "car": { ... },
     *  "buyer": { ... },
     *  "seller": { ... }
     * }
     * @response 403 {
     *  "message": "No tienes permiso para ver esta oferta."
     * }
     */
    public function show($id)
    {
        $offer = Offer::with(['car', 'buyer', 'seller'])->findOrFail($id);

        $myCustomerId = Auth::user()->customer->id;

        if ($offer->id_comprador !== $myCustomerId && $offer->id_vendedor !== $myCustomerId) {
            return response()->json(['message' => 'No tienes permiso para ver esta oferta.'], 403);
        }

        return $offer;
    }

    /**
     * Eliminar Oferta
     *
     * Cancela (elimina) una oferta pendiente. Solo el creador puede hacerlo.
     *
     * @authenticated
     * @urlParam id int required El ID de la oferta. Example: 1
     *
     * @response 204 {}
     * @response 403 {
     *  "message": "Solo el creador de la oferta puede eliminarla."
     * }
     */
    public function destroy($id)
    {
        $offer = Offer::findOrFail($id);
        $userCustomer = Auth::user()->customer;

        if ($offer->id_comprador !== $userCustomer->id) {
            return response()->json(['message' => 'Solo el creador de la oferta puede eliminarla.'], 403);
        }

        $offer->delete();
        return response()->json(null, 204);
    }
}
