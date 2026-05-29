<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreColorRequest;
use App\Http\Requests\UpdateColorRequest;
use App\Models\Color;

/**
 * @group Tablas Auxiliares
 * @subgroup Colores
 * @subgroupDescription Gestión de los colores de vehículos.
 */
class ColorsController extends Controller
{
    /**
     * Listar Colores
     *
     * @response [
     *  { "id": 1, "nombre": "Rojo" }
     * ]
     */
    public function index()
    {
        return Color::all();
    }

    /**
     * Crear Color
     *
     * @authenticated
     * @bodyParam nombre string required Nombre del color. Example: Azul Mate
     * @response 201 { ... }
     */
    public function store(StoreColorRequest $request)
    {
        $color = Color::create($request->validated());
        return response()->json($color, 201);
    }

    /**
     * Ver Color
     *
     * @urlParam id int required ID del color.
     * @response { "id": 1, "nombre": "Rojo" }
     */
    public function show($id)
    {
        return Color::findOrFail($id);
    }

    /**
     * Actualizar Color
     *
     * @authenticated
     * @urlParam id int required ID del color.
     * @bodyParam nombre string Nuevo nombre.
     * @response 200 { ... }
     */
    public function update(UpdateColorRequest $request, $id)
    {
        $color = Color::findOrFail($id);
        $color->update($request->validated());
        return response()->json($color, 200);
    }

    /**
     * Eliminar Color
     *
     * @authenticated
     * @urlParam id int required ID del color.
     * @response 204 {}
     */
    public function destroy($id)
    {
        Color::destroy($id);
        return response()->json(null, 204);
    }
}
