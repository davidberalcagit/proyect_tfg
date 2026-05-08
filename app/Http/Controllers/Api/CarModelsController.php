<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCarModelRequest;
use App\Http\Requests\UpdateCarModelRequest;
use App\Models\CarModels;

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
    public function store(StoreCarModelRequest $request)
    {
        $model = CarModels::create($request->validated());
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
    public function update(UpdateCarModelRequest $request, $id)
    {
        $model = CarModels::findOrFail($id);
        $model->update($request->validated());
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
