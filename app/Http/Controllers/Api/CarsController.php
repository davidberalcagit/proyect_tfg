<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use Illuminate\Support\Facades\Auth;

/**
 * @group Coches
 *
 * Gestión del inventario de vehículos.
 */
class CarsController extends Controller
{
    /**
     * Listar Coches Disponibles
     *
     * Obtiene una lista paginada de coches que están en estado "En Venta" (1).
     *
     * @queryParam page int El número de página. Example: 1
     * @queryParam search string Término de búsqueda opcional. Example: Toyota
     *
     * @response {
     *  "current_page": 1,
     *  "data": [
     *    {
     *      "id": 1,
     *      "title": "Toyota Corolla 2020",
     *      "precio": 15000,
     *      "km": 50000,
     *      "image": "cars/imagen.jpg",
     *      "marca": {
     *        "id": 1,
     *        "nombre": "Toyota"
     *      },
     *      "modelo": {
     *        "id": 1,
     *        "nombre": "Corolla"
     *      },
     *      "status": {
     *        "id": 1,
     *        "nombre": "En Venta"
     *      }
     *    }
     *  ],
     *  "first_page_url": "http://localhost/api/cars?page=1",
     *  "from": 1,
     *  "last_page": 5,
     *  "last_page_url": "http://localhost/api/cars?page=5",
     *  "next_page_url": "http://localhost/api/cars?page=2",
     *  "path": "http://localhost/api/cars",
     *  "per_page": 20,
     *  "prev_page_url": null,
     *  "to": 20,
     *  "total": 100
     * }
     */
    public function index()
    {
        return Cars::with(['marca', 'modelo', 'status'])
            ->where('id_estado', 1)
            ->paginate(20);
    }

    /**
     * Crear Coche
     *
     * Publica un nuevo vehículo. El estado inicial será "Pendiente" (4).
     * Requiere que el usuario tenga un perfil de vendedor (Customer).
     *
     * @authenticated
     * @bodyParam id_marca int ID de la marca (opcional si usa temp_brand). Example: 1
     * @bodyParam id_modelo int ID del modelo (opcional si usa temp_model). Example: 1
     * @bodyParam temp_brand string Nombre de marca nueva (si no existe). Example: Tesla
     * @bodyParam temp_model string Nombre de modelo nuevo (si no existe). Example: Cybertruck
     * @bodyParam precio number required Precio en euros. Example: 15000
     * @bodyParam anyo_matri int required Año de matriculación. Example: 2020
     * @bodyParam km int required Kilometraje. Example: 50000
     * @bodyParam matricula string required Matrícula del vehículo. Example: 1234ABC
     * @bodyParam id_combustible int required ID del tipo de combustible. Example: 1
     * @bodyParam id_marcha int required ID del tipo de marcha. Example: 1
     * @bodyParam id_color int ID del color. Example: 1
     * @bodyParam id_listing_type int required ID del tipo de listado (Venta/Alquiler). Example: 1
     * @bodyParam descripcion string required Descripción detallada. Example: Coche en perfecto estado...
     * @bodyParam image file Imagen del vehículo.
     *
     * @response 201 {
     *  "message": "Coche creado correctamente. Está pendiente de revisión por un supervisor.",
     *  "data": {
     *      "id": 10,
     *      "title": "Toyota Corolla 2020",
     *      "id_estado": 4,
     *      "created_at": "2023-10-27T10:00:00.000000Z"
     *  }
     * }
     * @response 403 {
     *  "message": "El usuario no tiene un perfil de vendedor asociado."
     * }
     */
    public function store(StoreCarRequest $request)
    {
        // Validation is handled by StoreCarRequest

        $user = Auth::user();

        if (!$user->customer) {
            return response()->json(['message' => 'El usuario no tiene un perfil de vendedor asociado.'], 403);
        }

        $estadoInicial = 4; // Siempre pendiente

        // Generar Título Automático
        $brandName = '';
        $modelName = '';

        if ($request->temp_brand) {
            $brandName = $request->temp_brand;
        } elseif ($request->id_marca) {
            $brandName = Brands::find($request->id_marca)->nombre;
        }

        if ($request->temp_model) {
            $modelName = $request->temp_model;
        } elseif ($request->id_modelo) {
            $modelName = CarModels::find($request->id_modelo)->nombre;
        }

        $generatedTitle = trim("$brandName $modelName " . $request->anyo_matri);

        $car = new Cars($request->all());
        $car->title = $generatedTitle;
        $car->id_vendedor = $user->customer->id;
        $car->id_estado = $estadoInicial;

        if ($request->temp_brand) $car->id_marca = null;
        if ($request->temp_model) $car->id_modelo = null;
        if ($request->temp_color) $car->id_color = null;

        $car->save();

        return response()->json([
            'message' => 'Coche creado correctamente. Está pendiente de revisión por un supervisor.',
            'data' => $car
        ], 201);
    }

    /**
     * Ver Detalle de Coche
     *
     * Obtiene los detalles completos de un coche específico.
     *
     * @urlParam id int required El ID del coche. Example: 1
     *
     * @response {
     *  "id": 1,
     *  "title": "Toyota Corolla 2020",
     *  "precio": 15000,
     *  "descripcion": "Coche en perfecto estado...",
     *  "marca": { "id": 1, "nombre": "Toyota" },
     *  "modelo": { "id": 1, "nombre": "Corolla" },
     *  "status": { "id": 1, "nombre": "En Venta" }
     * }
     * @response 404 {
     *  "message": "No query results for model [App\\Models\\Cars] 999"
     * }
     */
    public function show($id)
    {
        return Cars::with(['marca', 'modelo', 'status'])->findOrFail($id);
    }

    /**
     * Actualizar Coche
     *
     * Modifica los datos de un coche existente. Solo el dueño puede hacerlo y solo si el coche está en estado "Pendiente" (4).
     *
     * @authenticated
     * @urlParam id int required El ID del coche. Example: 1
     * @bodyParam precio number Nuevo precio. Example: 14500
     * @bodyParam descripcion string Nueva descripción.
     *
     * @response {
     *  "id": 1,
     *  "title": "Toyota Corolla 2020",
     *  "precio": 14500,
     *  "updated_at": "2023-10-28T12:00:00.000000Z"
     * }
     * @response 403 {
     *  "message": "No tienes permiso para editar este coche."
     * }
     */
    public function update(UpdateCarRequest $request, Cars $car)
    {
        // Model binding injects $car. Policy check is done in FormRequest.

        // Additional check if policy didn't cover it (redundant if policy is correct)
        if ($car->id_vendedor !== Auth::user()->customer->id) {
             return response()->json(['message' => 'No tienes permiso para editar este coche.'], 403);
        }

        if ($car->id_estado == 1) {
            return response()->json(['message' => 'No puedes editar un coche que ya ha sido aprobado.'], 403);
        }

        $car->update($request->all());
        return response()->json($car, 200);
    }

    /**
     * Eliminar Coche
     *
     * Elimina un coche del sistema. Solo el dueño puede hacerlo.
     *
     * @authenticated
     * @urlParam id int required El ID del coche. Example: 1
     *
     * @response 204 {}
     * @response 403 {
     *  "message": "No tienes permiso para eliminar este coche."
     * }
     */
    public function destroy(Cars $car)
    {
        // Using model binding for consistency
        if ($car->id_vendedor !== Auth::user()->customer->id) {
             return response()->json(['message' => 'No tienes permiso para eliminar este coche.'], 403);
        }

        $car->delete();
        return response()->json(null, 204);
    }

    /**
     * Mis Coches
     *
     * Lista los coches publicados por el usuario autenticado.
     *
     * @authenticated
     * @response [
     *  {
     *      "id": 1,
     *      "title": "Toyota Corolla 2020",
     *      "status": { "nombre": "En Venta" }
     *  }
     * ]
     * @response 401 {
     *  "message": "Unauthenticated."
     * }
     */
    public function myCars()
    {
        $user = Auth::user();

        // Verificar si el usuario está autenticado
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Verificar si tiene perfil de cliente
        if (!$user->customer) {
            return response()->json([], 200);
        }

        return Cars::with(['marca', 'modelo', 'status'])
            ->where('id_vendedor', $user->customer->id)
            ->get();
    }
}
