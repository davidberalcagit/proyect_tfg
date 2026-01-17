<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarsController extends Controller
{
    public function index()
    {
        return Cars::with(['marca', 'modelo', 'status'])
            ->where('id_estado', 1)
            ->paginate(20);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_marca' => 'nullable|exists:brands,id',
            'id_modelo' => 'nullable|exists:car_models,id',
            'temp_brand' => 'required_without:id_marca|nullable|string|max:50',
            'temp_model' => 'required_without:id_modelo|nullable|string|max:50',

            'id_marcha' => 'required|exists:gears,id',
            'id_combustible' => 'required|exists:fuels,id',

            // Validación Color
            'id_color' => 'nullable|exists:colors,id',
            'temp_color' => 'required_without:id_color|nullable|string|max:50',

            'matricula' => 'required|string|max:20',
            'anyo_matri' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'km' => 'required|integer|min:0',
            'precio' => 'required|numeric|min:0',
            'descripcion' => 'nullable|string',
            'image' => 'nullable|string',
        ]);

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
        if ($request->temp_color) $car->id_color = null; // Nuevo

        $car->save();

        return response()->json([
            'message' => 'Coche creado correctamente. Está pendiente de revisión por un supervisor.',
            'data' => $car
        ], 201);
    }

    public function show($id)
    {
        return Cars::with(['marca', 'modelo', 'status'])->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $car = Cars::findOrFail($id);

        if ($car->id_vendedor !== Auth::user()->customer->id) {
             return response()->json(['message' => 'No tienes permiso para editar este coche.'], 403);
        }

        if ($car->id_estado == 1) {
            return response()->json(['message' => 'No puedes editar un coche que ya ha sido aprobado.'], 403);
        }

        $car->update($request->all());
        return response()->json($car, 200);
    }

    public function destroy($id)
    {
        $car = Cars::findOrFail($id);

        if ($car->id_vendedor !== Auth::user()->customer->id) {
             return response()->json(['message' => 'No tienes permiso para eliminar este coche.'], 403);
        }

        $car->delete();
        return response()->json(null, 204);
    }

    public function myCars()
    {
        $user = Auth::user();
        if (!$user->customer) return response()->json([], 200);

        return Cars::with(['marca', 'modelo', 'status'])
            ->where('id_vendedor', $user->customer->id)
            ->get();
    }
}
