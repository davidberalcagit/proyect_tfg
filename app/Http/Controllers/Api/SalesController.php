<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cars;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    public function index()
    {
        // Mostrar ventas donde el usuario es vendedor o comprador
        $userCustomer = Auth::user()->customer;

        if (!$userCustomer) {
            return response()->json(['message' => 'Usuario no tiene perfil de cliente'], 403);
        }

        return Sales::with(['vehiculo', 'vendedor', 'comprador'])
            ->where('id_vendedor', $userCustomer->id)
            ->orWhere('id_comprador', $userCustomer->id)
            ->paginate(20);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_vehiculo' => 'required|exists:cars,id',
            'id_comprador' => 'required|exists:customers,id', // El ID del cliente que compra
            'precio' => 'required|numeric|min:0',
            'fecha' => 'required|date',
            'metodo_pago' => 'required|string|max:50',
            'estado' => 'required|exists:sale_statuses,id', // 1: Pendiente, 2: Completada, etc.
        ]);

        $car = Cars::findOrFail($request->id_vehiculo);
        $seller = Auth::user()->customer;

        if (!$seller) {
            return response()->json(['message' => 'No tienes perfil de vendedor.'], 403);
        }

        // Verificar que el coche pertenece al usuario autenticado
        if ($car->id_vendedor !== $seller->id) {
            return response()->json(['message' => 'No puedes vender un coche que no es tuyo.'], 403);
        }

        // Verificar que el coche no esté ya vendido (opcional, depende de tu lógica)
        if ($car->id_estado == 3) { // Asumiendo 3 es 'Vendido'
             return response()->json(['message' => 'Este coche ya ha sido vendido.'], 400);
        }

        $sale = Sales::create([
            'id_vehiculo' => $request->id_vehiculo,
            'id_vendedor' => $seller->id,
            'id_comprador' => $request->id_comprador,
            'precio' => $request->precio,
            'fecha' => $request->fecha,
            'metodo_pago' => $request->metodo_pago,
            'estado' => $request->estado,
        ]);

        // Opcional: Actualizar estado del coche a "Vendido" si la venta es completada
        // if ($request->estado == 2) { // Asumiendo 2 es Completada
        //     $car->update(['id_estado' => 3]); // 3 = Vendido
        // }

        return response()->json($sale, 201);
    }

    public function show($id)
    {
        $sale = Sales::with(['vehiculo', 'vendedor', 'comprador'])->findOrFail($id);

        // Seguridad: Solo ver la venta si eres parte de ella (vendedor o comprador)
        $myCustomerId = Auth::user()->customer->id;
        if ($sale->id_vendedor !== $myCustomerId && $sale->id_comprador !== $myCustomerId) {
            return response()->json(['message' => 'No tienes permiso para ver esta venta.'], 403);
        }

        return $sale;
    }

    public function update(Request $request, $id)
    {
        $sale = Sales::findOrFail($id);

        // Solo el vendedor puede actualizar la venta
        if ($sale->id_vendedor !== Auth::user()->customer->id) {
            return response()->json(['message' => 'No tienes permiso para editar esta venta.'], 403);
        }

        $request->validate([
            'precio' => 'numeric|min:0',
            'fecha' => 'date',
            'metodo_pago' => 'string|max:50',
            'estado' => 'exists:sale_statuses,id',
        ]);

        $sale->update($request->all());
        return response()->json($sale, 200);
    }

    public function destroy($id)
    {
        $sale = Sales::findOrFail($id);

        if ($sale->id_vendedor !== Auth::user()->customer->id) {
            return response()->json(['message' => 'No tienes permiso para eliminar esta venta.'], 403);
        }

        $sale->delete();
        return response()->json(null, 204);
    }
}
