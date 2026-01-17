<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarModels;
use Illuminate\Http\Request;

class CarModelsController extends Controller
{
    public function index()
    {
        return CarModels::with('marca')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required',
            'id_marca' => 'required|exists:brands,id'
        ]);
        $model = CarModels::create($request->all());
        return response()->json($model, 201);
    }

    public function show($id)
    {
        return CarModels::with('marca')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $model = CarModels::findOrFail($id);
        $model->update($request->all());
        return response()->json($model, 200);
    }

    public function destroy($id)
    {
        CarModels::destroy($id);
        return response()->json(null, 204);
    }
}
