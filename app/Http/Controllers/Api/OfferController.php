<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cars;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    public function index()
    {
        $userCustomer = Auth::user()->customer;

        if (!$userCustomer) {
            return response()->json(['message' => 'Usuario no tiene perfil de cliente'], 403);
        }

        // Mostrar ofertas hechas por mí O recibidas por mis coches
        return Offer::with(['car', 'buyer', 'seller'])
            ->where('id_comprador', $userCustomer->id)
            ->orWhere('id_vendedor', $userCustomer->id)
            ->paginate(20);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_vehiculo' => 'required|exists:cars,id',
            'precio_oferta' => 'required|numeric|min:0',
            'mensaje' => 'nullable|string|max:500',
        ]);

        $buyer = Auth::user()->customer;
        if (!$buyer) {
            return response()->json(['message' => 'Debes crear un perfil de cliente para hacer ofertas.'], 403);
        }

        $car = Cars::findOrFail($request->id_vehiculo);

        // Validar que no te hagas una oferta a ti mismo
        if ($car->id_vendedor === $buyer->id) {
            return response()->json(['message' => 'No puedes hacer una oferta por tu propio coche.'], 400);
        }

        // Validar si ya existe una oferta pendiente para este coche de este usuario (opcional)
        $existingOffer = Offer::where('id_vehiculo', $car->id)
            ->where('id_comprador', $buyer->id)
            ->where('estado', 'pendiente') // Asumiendo que tienes un campo estado
            ->first();

        if ($existingOffer) {
            return response()->json(['message' => 'Ya tienes una oferta pendiente para este coche.'], 409);
        }

        $offer = Offer::create([
            'id_vehiculo' => $car->id,
            'id_vendedor' => $car->id_vendedor, // El vendedor es el dueño del coche
            'id_comprador' => $buyer->id,
            'precio_oferta' => $request->precio_oferta,
            'mensaje' => $request->mensaje,
            'estado' => 'pendiente', // Estado inicial
            'fecha_oferta' => now(),
        ]);

        return response()->json($offer, 201);
    }

    public function show($id)
    {
        $offer = Offer::with(['car', 'buyer', 'seller'])->findOrFail($id);

        $myCustomerId = Auth::user()->customer->id;

        // Solo ver la oferta si soy el comprador o el vendedor
        if ($offer->id_comprador !== $myCustomerId && $offer->id_vendedor !== $myCustomerId) {
            return response()->json(['message' => 'No tienes permiso para ver esta oferta.'], 403);
        }

        return $offer;
    }

    public function update(Request $request, $id)
    {
        $offer = Offer::findOrFail($id);
        $userCustomer = Auth::user()->customer;

        // Lógica de actualización:
        // - El comprador puede cambiar el precio si la oferta está pendiente.
        // - El vendedor puede ACEPTAR o RECHAZAR la oferta (cambiar estado).

        if ($offer->id_comprador === $userCustomer->id) {
            // Es el comprador editando su oferta
            $request->validate([
                'precio_oferta' => 'numeric|min:0',
                'mensaje' => 'string|max:500',
            ]);
            $offer->update($request->only(['precio_oferta', 'mensaje']));

        } elseif ($offer->id_vendedor === $userCustomer->id) {
            // Es el vendedor respondiendo a la oferta
            $request->validate([
                'estado' => 'required|in:aceptada,rechazada,pendiente',
            ]);
            $offer->update(['estado' => $request->estado]);
        } else {
            return response()->json(['message' => 'No tienes permiso para modificar esta oferta.'], 403);
        }

        return response()->json($offer, 200);
    }

    public function destroy($id)
    {
        $offer = Offer::findOrFail($id);
        $userCustomer = Auth::user()->customer;

        // Solo el comprador puede cancelar (eliminar) su oferta
        if ($offer->id_comprador !== $userCustomer->id) {
            return response()->json(['message' => 'Solo el creador de la oferta puede eliminarla.'], 403);
        }

        $offer->delete();
        return response()->json(null, 204);
    }
}
