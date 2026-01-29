<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarModels;
use Illuminate\Http\Request;

/**
 * @group Tablas Auxiliares
 */
class CarModelsController extends Controller
{
    /**
     * Listar Modelos
     *
     * Obtiene todos los modelos de coches registrados.
     *
     * @response [
     *  {
     *      "id": 1,
     *      "nombre": "Corolla",
     *      "marca": { "id": 1, "nombre": "Toyota" }
     *  }
     * ]
     */
    public function index()
    {
        return CarModels::with('marca')->get();
    }

    /**
     * Crear Modelo
     *
     * Registra un nuevo modelo. (Solo Admin)
     *
     * @authenticated
     * @bodyParam nombre string required El nombre del modelo. Example: Cybertruck
     * @bodyParam id_marca int required El ID de la marca asociada. Example: 10
     *
     * @response 201 { ... }
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'id_marca' => 'required|exists:brands,id'
        ]);
        $model = CarModels::create($request->all());
        return response()->json($model, 201);
    }

    /**
     * Ver Modelo
     *
     * Obtiene los detalles de un modelo.
     *
     * @urlParam id int required El ID del modelo. Example: 1
     *
     * @response { ... }
     */
    public function show($id)
    {
        return CarModels::with('marca')->findOrFail($id);
    }

    /**
     * Actualizar Modelo
     *
     * Modifica un modelo existente. (Solo Admin)
     *
     * @authenticated
     * @urlParam id int required El ID del modelo. Example: 1
     * @bodyParam nombre string Nuevo nombre.
     *
     * @response 200 { ... }
     */
    public function update(Request $request, $id)
    {
        $model = CarModels::findOrFail($id);
        $model->update($request->all());
        return response()->json($model, 200);
    }

    /**
     * Eliminar Modelo
     *
     * Elimina un modelo del sistema. (Solo Admin)
     *
     * @authenticated
     * @urlParam id int required El ID del modelo. Example: 1
     *
     * @response 204 {}
     */
    public function destroy($id)
    {
        CarModels::destroy($id);
        return response()->json(null, 204);
    }
}
