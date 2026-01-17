<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\CarModels;
use Illuminate\Http\Request;

class BrandsController extends Controller
{
    public function index()
    {
        return Brands::all();
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|unique:brands,nombre']);
        $brand = Brands::create($request->all());
        return response()->json($brand, 201);
    }

    public function show($id)
    {
        return Brands::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $brand = Brands::findOrFail($id);
        $brand->update($request->all());
        return response()->json($brand, 200);
    }

    public function destroy($id)
    {
        Brands::destroy($id);
        return response()->json(null, 204);
    }

    // Método para obtener modelos de una marca
    public function models($id)
    {
        return CarModels::where('id_marca', $id)->get();
    }
}
