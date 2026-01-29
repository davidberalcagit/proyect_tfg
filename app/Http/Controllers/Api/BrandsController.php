<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\CarModels;
use Illuminate\Http\Request;

/**
 * @group Tablas Auxiliares
 *
 * Gestión de datos maestros (Marcas, Modelos, etc.).
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
     * Registra una nueva marca. (Solo Admin)
     *
     * @authenticated
     * @bodyParam nombre string required El nombre de la marca. Example: Tesla
     *
     * @response 201 {
     *  "id": 10,
     *  "nombre": "Tesla",
     *  "created_at": "..."
     * }
     */
    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|unique:brands,nombre']);
        $brand = Brands::create($request->all());
        return response()->json($brand, 201);
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
    public function update(Request $request, $id)
    {
        $brand = Brands::findOrFail($id);
        $brand->update($request->all());
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
