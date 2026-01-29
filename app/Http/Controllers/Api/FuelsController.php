<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fuels;
use Illuminate\Http\Request;

/**
 * @group Tablas Auxiliares
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
    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|unique:fuels,nombre']);
        $fuel = Fuels::create($request->all());
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
    public function update(Request $request, $id)
    {
        $fuel = Fuels::findOrFail($id);
        $fuel->update($request->all());
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
