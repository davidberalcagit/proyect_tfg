<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

/**
 * @group Tablas Auxiliares
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
    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|unique:colors,nombre']);
        $color = Color::create($request->all());
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
    public function update(Request $request, $id)
    {
        $color = Color::findOrFail($id);
        $color->update($request->all());
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
