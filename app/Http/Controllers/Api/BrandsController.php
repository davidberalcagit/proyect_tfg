<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brands;
use App\Models\CarModels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @group Tablas Auxiliares
 * @subgroup Marcas
 * @subgroupDescription Gestión de las marcas de los vehículos.
 */
class BrandsController extends Controller
{
    /**
     * Listar Marcas
     *
     * Obtiene todas las marcas de coches registradas.
     *
     * @response [
     *  {
     *      "id": 1,
     *      "nombre": "Toyota"
     *  },
     *  {
     *      "id": 2,
     *      "nombre": "Ford"
     *  }
     * ]
     */
    public function index()
    {
        return Brands::all();
    }

    /**
     * Crear Marca
     *
     * Registra una nueva marca y, opcionalmente, modelos asociados.
     *
     * @authenticated
     * @bodyParam nombre string required El nombre de la marca. Example: Tesla
     * @bodyParam models array Opcional. Una lista de nombres de modelos. Example: ["Model S", "Model 3"]
     *
     * @response 201 {
     *  "id": 10,
     *  "nombre": "Tesla",
     *  "models_created": 2
     * }
     */
    public function store(StoreBrandRequest $request)
    {
        $validated = $request->validated();
        $modelsCreatedCount = 0;

        $brand = DB::transaction(function () use ($validated, &$modelsCreatedCount) {
            $brand = Brands::create(['nombre' => $validated['nombre']]);

            if (!empty($validated['models'])) {
                foreach ($validated['models'] as $modelName) {
                    if (!empty(trim($modelName))) {
                        $brand->models()->create(['nombre' => $modelName]);
                        $modelsCreatedCount++;
                    }
                }
            }
            return $brand;
        });

        return response()->json([
            'id' => $brand->id,
            'nombre' => $brand->nombre,
            'models_created' => $modelsCreatedCount
        ], 201);
    }

    /**
     * Ver Marca
     *
     * Obtiene los detalles de una marca.
     *
     * @urlParam id int required El ID de la marca. Example: 1
     *
     * @response {
     *  "id": 1,
     *  "nombre": "Toyota"
     * }
     */
    public function show($id)
    {
        return Brands::findOrFail($id);
    }

    /**
     * Actualizar Marca
     *
     * Modifica el nombre de una marca. (Solo Admin)
     *
     * @authenticated
     * @urlParam id int required El ID de la marca. Example: 1
     * @bodyParam nombre string required Nuevo nombre. Example: Toyota Updated
     *
     * @response 200 { ... }
     */
    public function update(UpdateBrandRequest $request, $id)
    {
        $brand = Brands::findOrFail($id);
        $brand->update($request->validated());
        return response()->json($brand, 200);
    }

    /**
     * Eliminar Marca
     *
     * Elimina una marca del sistema. (Solo Admin)
     *
     * @authenticated
     * @urlParam id int required El ID de la marca. Example: 1
     *
     * @response 204 {}
     */
    public function destroy($id)
    {
        Brands::destroy($id);
        return response()->json(null, 204);
    }

    /**
     * Listar Modelos de una Marca
     *
     * Obtiene todos los modelos asociados a una marca específica.
     *
     * @urlParam id int required El ID de la marca. Example: 1
     *
     * @response [
     *  {
     *      "id": 1,
     *      "id_marca": 1,
     *      "nombre": "Corolla"
     *  },
     *  {
     *      "id": 2,
     *      "id_marca": 1,
     *      "nombre": "Yaris"
     *  }
     * ]
     */
    public function models($id)
    {
        return CarModels::where('id_marca', $id)->get();
    }
}
