<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreGearRequest;
use App\Http\Requests\UpdateGearRequest;
use App\Models\Gears;

/**
 * @group Tablas Auxiliares
 * @subgroup Marchas
 * @subgroupDescription Gestión de los tipos de marchas (transmisiones).
 */
class GearsController extends Controller
{
    /**
     * Listar Marchas
     *
     * @response [
     *  { "id": 1, "tipo": "Manual" }
     * ]
     */
    public function index()
    {
        return Gears::all();
    }

    /**
     * Crear Marcha
     *
     * @authenticated
     * @bodyParam tipo string required Tipo de marcha. Example: Secuencial
     * @response 201 { ... }
     */
    public function store(StoreGearRequest $request)
    {
        $gear = Gears::create($request->validated());
        return response()->json($gear, 201);
    }

    /**
     * Ver Marcha
     *
     * @urlParam id int required ID.
     * @response { "id": 1, "tipo": "Manual" }
     */
    public function show($id)
    {
        return Gears::findOrFail($id);
    }

    /**
     * Actualizar Marcha
     *
     * @authenticated
     * @urlParam id int required ID.
     * @bodyParam tipo string Nuevo tipo.
     * @response 200 { ... }
     */
    public function update(UpdateGearRequest $request, $id)
    {
        $gear = Gears::findOrFail($id);
        $gear->update($request->validated());
        return response()->json($gear, 200);
    }

    /**
     * Eliminar Marcha
     *
     * @authenticated
     * @urlParam id int required ID.
     * @response 204 {}
     */
    public function destroy($id)
    {
        Gears::destroy($id);
        return response()->json(null, 204);
    }
}
