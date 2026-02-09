<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customers;
use App\Models\Dealerships;
use Illuminate\Support\Facades\Auth;

/**
 * @group Clientes
 *
 * Gestión de perfiles de usuario (Vendedores/Compradores).
 */
class CustomersController extends Controller
{
    /**
     * Listar Clientes
     *
     * Obtiene una lista paginada de todos los perfiles de clientes.
     *
     * @authenticated
     * @response {
     *  "data": [
     *    {
     *      "id": 1,
     *      "nombre_contacto": "Juan Perez",
     *      "telefono": "666777888",
     *      "entity_type": { "nombre": "Particular" }
     *    }
     *  ]
     * }
     */
    public function index()
    {
        return Customers::with(['user', 'entityType', 'dealership'])->paginate(20);
    }

    /**
     * Crear Perfil de Cliente
     *
     * Crea un perfil de vendedor/comprador para el usuario autenticado.
     *
     * @authenticated
     * @bodyParam id_entidad int required Tipo de entidad (1: Particular, 2: Concesionario). Example: 1
     * @bodyParam nombre_contacto string required Nombre completo. Example: Juan Perez
     * @bodyParam telefono string required Teléfono de contacto. Example: 666777888
     * @bodyParam nombre_empresa string Nombre de la empresa (solo si es concesionario). Example: Coches SL
     * @bodyParam nif string NIF de la empresa (solo si es concesionario). Example: B12345678
     * @bodyParam direccion string Dirección de la empresa (solo si es concesionario).
     *
     * @response 201 { ... }
     * @response 409 { "message": "Este usuario ya tiene un perfil de cliente creado." }
     */
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

    /**
     * Ver Perfil Público
     *
     * Muestra la información pública de un cliente (vendedor).
     *
     * @authenticated
     * @urlParam id int required El ID del cliente. Example: 1
     *
     * @response {
     *  "id": 1,
     *  "nombre_contacto": "Juan Perez",
     *  "cars": [ ... ]
     * }
     */
    public function show($id)
    {
        return Customers::with(['user', 'entityType', 'cars', 'dealership'])->findOrFail($id);
    }

    /**
     * Mi Perfil
     *
     * Obtiene el perfil de cliente del usuario autenticado.
     *
     * @authenticated
     * @response { ... }
     * @response 404 { "message": "No tienes perfil de cliente." }
     */
    public function me()
    {
        $customer = Auth::user()->customer;

        if (!$customer) {
            return response()->json(['message' => 'No tienes perfil de cliente.'], 404);
        }

        return response()->json($customer->load(['entityType', 'cars', 'dealership']));
    }

    /**
     * Actualizar Perfil
     *
     * Modifica los datos de contacto. Solo el propio usuario puede hacerlo.
     *
     * @authenticated
     * @urlParam id int required El ID del cliente. Example: 1
     * @bodyParam telefono string Nuevo teléfono.
     *
     * @response 200 { ... }
     */
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

    /**
     * Eliminar Perfil
     *
     * Elimina el perfil de cliente.
     *
     * @authenticated
     * @urlParam id int required El ID del cliente. Example: 1
     *
     * @response 204 {}
     */
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
