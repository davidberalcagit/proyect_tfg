<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use App\Models\Fuels;
use App\Models\Gears;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cars = Cars::all();
        return view('cars.index', compact('cars'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = Brands::all();
        $fuels  = Fuels::all();
        $gears = Gears::all();


        return view('cars.create',compact('brands','fuels', 'gears'));
    }

    public function getModels(Brands $brand)
    {
        $models = CarModels::where('id_marca', $brand->id)->get();
        return response()->json($models);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'id_marca' => 'required|integer|exists:brands,id',
            'id_combustible' => 'required|integer|exists:fuels,id',
            'id_marcha' => 'required|integer|exists:gears,id',
            'id_modelo' => 'required|integer|exists:car_models,id',
            'precio' => 'required|numeric',
            'anyo_matri' => 'required|integer',
            'km' => 'required|integer',
            'matricula' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $car = new Cars($request->all());
        $car->id_vendedor = Auth::id();


        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/cars');
            $car->image = $path;
        }

        $car->save();

        return redirect()->route('cars.index')->with('success', 'Car created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cars $car)
    {
        return view('cars.show', compact('car'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cars $car)
    {
        $brands = Brands::all();
        $fuels = Fuels::all();
        $gears = Gears::all();
        return view('cars.edit', compact('car', 'brands', 'fuels', 'gears'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cars $car)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'id_marca' => 'required|integer|exists:brands,id',
            'id_combustible' => 'required|integer|exists:fuels,id',
            'id_marcha' => 'required|integer|exists:gears,id',
            'id_modelo' => 'required|integer|exists:car_models,id',
            'precio' => 'required|numeric',
            'anyo_matri' => 'required|integer',
            'km' => 'required|integer',
            'matricula' => 'required|string|max:255',
            'color' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $car->fill($request->except('image'));
        $car->id_marca = $request->id_marca;
        $car->id_combustible = $request->id_combustible;
        $car->id_marcha = $request->id_marcha;
        $car->id_modelo = $request->id_modelo;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('public/cars');
            $car->image = $path;
        }

        $car->save();

        return redirect()->route('cars.index')->with('success', 'Car updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cars $car)
    {
        $car->delete();
        return redirect()->route('cars.index')->with('success', 'Car deleted successfully.');
    }
}
