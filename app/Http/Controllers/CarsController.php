<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use App\Models\Color;
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
        // Show cars in random order, 50 per page, only if they are 'En venta' (id_estado = 1)
        $cars = Cars::where('id_estado', 1)->inRandomOrder()->paginate(51);
        return view('cars.index', compact('cars'));
    }

    public function myCars()
    {
        if (!Auth::check() || !Auth::user()->customer) {
            return redirect()->route('login');
        }

        $sellerId = Auth::user()->customer->id;
        $cars = Cars::where('id_vendedor', $sellerId)->paginate(50);

        return view('cars.my_cars', compact('cars'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = Brands::all();
        $fuels  = Fuels::all();
        $gears = Gears::all();
        $colors = Color::all();


        return view('cars.create',compact('brands','fuels', 'gears', 'colors'));
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
            'id_marca' => 'required|integer|exists:brands,id',
            'id_modelo' => 'required|integer|exists:car_models,id',
            'id_combustible' => 'required|integer|exists:fuels,id',
            'id_marcha' => 'required|integer|exists:gears,id',
            'precio' => 'required|numeric',
            'anyo_matri' => 'required|integer|min:1900|max:2026',
            'km' => 'required|integer',
            'matricula' => ['required', 'string', 'max:255', 'regex:/^[0-9]{4}[A-Z]{3}$/i'],
            'id_color' => 'required|integer|exists:colors,id',
            'descripcion' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $brand = Brands::find($request->id_marca);
        $model = CarModels::find($request->id_modelo);
        $title = $brand->nombre . ' ' . $model->nombre;

        $car = new Cars($request->all());
        $car->title = $title;
        // Assuming Auth::user() has a customer relationship.
        $car->id_vendedor = Auth::user()->customer->id;
        $car->id_estado = 1; // Default to 'En venta'


        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('cars', 'public');
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
        $car->load('vendedor', 'marcha', 'combustible', 'color', 'marca', 'modelo', 'status');
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
        $colors = Color::all();
        return view('cars.edit', compact('car', 'brands', 'fuels', 'gears', 'colors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cars $car)
    {
        $request->validate([
            'id_marca' => 'required|integer|exists:brands,id',
            'id_modelo' => 'required|integer|exists:car_models,id',
            'id_combustible' => 'required|integer|exists:fuels,id',
            'id_marcha' => 'required|integer|exists:gears,id',
            'precio' => 'required|numeric',
            'anyo_matri' => 'required|integer|min:1900|max:2026',
            'km' => 'required|integer',
            'matricula' => ['required', 'string', 'max:255', 'regex:/^[0-9]{4}[A-Z]{3}$/i'],
            'id_color' => 'required|integer|exists:colors,id',
            'descripcion' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $brand = Brands::find($request->id_marca);
        $model = CarModels::find($request->id_modelo);
        $title = $brand->nombre . ' ' . $model->nombre;

        $car->fill($request->except('image'));
        $car->title = $title;
        $car->id_marca = $request->id_marca;
        $car->id_modelo = $request->id_modelo;
        $car->id_combustible = $request->id_combustible;
        $car->id_marcha = $request->id_marcha;
        $car->id_color = $request->id_color;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('cars', 'public');
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
