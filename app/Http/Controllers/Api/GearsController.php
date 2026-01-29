<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gears;
use Illuminate\Http\Request;

/**
 * @group Tablas Auxiliares
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
    public function store(Request $request)
    {
        $request->validate(['tipo' => 'required|unique:gears,tipo']);
        $gear = Gears::create($request->all());
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
    public function update(Request $request, $id)
    {
        $gear = Gears::findOrFail($id);
        $gear->update($request->all());
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
