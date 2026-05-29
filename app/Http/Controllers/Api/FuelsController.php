<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFuelRequest;
use App\Http\Requests\UpdateFuelRequest;
use App\Models\Fuels;

/**
 * @group Tablas Auxiliares
 * @subgroup Combustibles
 * @subgroupDescription Gestión de los tipos de combustible disponibles.
 */
class FuelsController extends Controller
{
    /**
     * Listar Combustibles
     *
     * Obtiene la lista de tipos de combustible disponibles.
     *
     * @response [
     *  {
     *      "id": 1,
     *      "nombre": "Gasolina"
     *  },
     *  {
     *      "id": 2,
     *      "nombre": "Diesel"
     *  }
     * ]
     */
    public function index()
    {
        return Fuels::all();
    }

    /**
     * Crear Combustible
     *
     * Registra un nuevo tipo de combustible. (Solo Admin)
     *
     * @authenticated
     * @bodyParam nombre string required El nombre del combustible. Example: Hidrógeno
     *
     * @response 201 {
     *  "id": 5,
     *  "nombre": "Hidrógeno",
     *  "created_at": "..."
     * }
     */
    public function store(StoreFuelRequest $request)
    {
        $fuel = Fuels::create($request->validated());
        return response()->json($fuel, 201);
    }

    /**
     * Ver Combustible
     *
     * @urlParam id int required El ID del combustible. Example: 1
     *
     * @response {
     *  "id": 1,
     *  "nombre": "Gasolina"
     * }
     */
    public function show($id)
    {
        return Fuels::findOrFail($id);
    }

    /**
     * Actualizar Combustible
     *
     * Modifica un tipo de combustible. (Solo Admin)
     *
     * @authenticated
     * @urlParam id int required El ID del combustible. Example: 1
     * @bodyParam nombre string Nuevo nombre.
     *
     * @response 200 { ... }
     */
    public function update(UpdateFuelRequest $request, $id)
    {
        $fuel = Fuels::findOrFail($id);
        $fuel->update($request->validated());
        return response()->json($fuel, 200);
    }

    /**
     * Eliminar Combustible
     *
     * Elimina un tipo de combustible. (Solo Admin)
     *
     * @authenticated
     * @urlParam id int required El ID del combustible. Example: 1
     *
     * @response 204 {}
     */
    public function destroy($id)
    {
        Fuels::destroy($id);
        return response()->json(null, 204);
    }
}
