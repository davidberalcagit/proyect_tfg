<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customers;
use App\Models\Dealerships;
use Illuminate\Support\Facades\Auth;

class CustomersController extends Controller
{
    public function index()
    {
        return Customers::with(['user', 'entityType', 'dealership'])->paginate(20);
    }

    public function store(StoreCustomerRequest $request)
    {
        // Validation handled by StoreCustomerRequest

        $user = Auth::user();

        if ($user->customer) {
            return response()->json(['message' => 'Este usuario ya tiene un perfil de cliente creado.'], 409);
        }

        $dealershipId = null;

        // Lógica para Concesionarios (id_entidad = 2)
        if ($request->id_entidad == 2) {
            // Buscar si la empresa ya existe por NIF
            $existingDealership = Dealerships::where('nif', $request->nif)->first();

            if ($existingDealership) {
                // Si existe, nos unimos a ella
                $dealershipId = $existingDealership->id;
            } else {
                // Si no existe, la creamos
                $newDealership = Dealerships::create([
                    'nombre_empresa' => $request->nombre_empresa,
                    'nif' => $request->nif,
                    'direccion' => $request->direccion,
                ]);
                $dealershipId = $newDealership->id;
            }
        }

        $customer = Customers::create([
            'id_usuario' => $user->id,
            'id_entidad' => $request->id_entidad,
            'nombre_contacto' => $request->nombre_contacto,
            'telefono' => $request->telefono,
            'dealership_id' => $dealershipId, // Asignamos el ID del concesionario (o null si es particular)
        ]);

        return response()->json($customer->load('dealership'), 201);
    }

    public function show($id)
    {
        return Customers::with(['user', 'entityType', 'cars', 'dealership'])->findOrFail($id);
    }

    public function me()
    {
        $customer = Auth::user()->customer;

        if (!$customer) {
            return response()->json(['message' => 'No tienes perfil de cliente.'], 404);
        }

        return response()->json($customer->load(['entityType', 'cars', 'dealership']));
    }

    public function update(UpdateCustomerRequest $request, $id)
    {
        $customer = Customers::findOrFail($id);

        if ($customer->id_usuario !== Auth::id()) {
            return response()->json(['message' => 'No tienes permiso para editar este perfil.'], 403);
        }

        $customer->update($request->only(['telefono', 'nombre_contacto']));

        // Si es concesionario, permitir actualizar datos de la empresa (Opcional: ¿Todos pueden editar la empresa?)
        if ($customer->dealership_id && $request->has('nombre_empresa')) {
             $customer->dealership->update($request->only(['nombre_empresa', 'direccion']));
        }

        return response()->json($customer->load('dealership'), 200);
    }

    public function destroy($id)
    {
        $customer = Customers::findOrFail($id);

        if ($customer->id_usuario !== Auth::id()) {
            return response()->json(['message' => 'No tienes permiso para eliminar este perfil.'], 403);
        }

        $customer->delete();
        return response()->json(null, 204);
    }
}
