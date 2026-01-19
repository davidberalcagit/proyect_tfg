<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Jobs\ProcessCarImageJob;
use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use App\Models\Color;
use App\Models\Fuels;
use App\Models\Gears;
use App\Models\ListingType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CarsController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index()
    {
        // Mostrar coches En Venta (1) o En Alquiler (3)
        $cars = Cars::whereIn('id_estado', [1, 3])->inRandomOrder()->paginate(51);
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

    public function create(Request $request)
    {
        $this->authorize('create', Cars::class);

        $brands = Brands::all();
        $fuels  = Fuels::all();
        $gears = Gears::all();
        $colors = Color::all();

        $listingType = ListingType::where('nombre', $request->query('type', 'sale') === 'rent' ? 'Alquiler' : 'Venta')->first() ?? ListingType::first();

        return view('cars.create', compact('brands','fuels', 'gears', 'colors', 'listingType'));
    }

    public function getModels(Brands $brand)
    {
        $models = CarModels::where('id_marca', $brand->id)->get();
        return response()->json($models);
    }

    public function store(StoreCarRequest $request)
    {
        $this->authorize('create', Cars::class);

        $user = Auth::user();

        // Generar Título
        $brandName = $request->temp_brand ? trim($request->temp_brand) : Brands::find($request->id_marca)->nombre;
        $modelName = $request->temp_model ? trim($request->temp_model) : CarModels::find($request->id_modelo)->nombre;
        $title = trim("$brandName $modelName " . $request->anyo_matri);

        $car = new Cars($request->all());
        $car->title = $title;
        $car->id_vendedor = $user->customer->id;
        $car->id_estado = 4;

        if ($request->temp_brand) $car->id_marca = null;
        if ($request->temp_model) $car->id_modelo = null;
        if ($request->temp_color) $car->id_color = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('cars', 'public');
            $car->image = $path;
        }

        $car->save();

        if ($car->image) {
            ProcessCarImageJob::dispatch($car->id);
        }

        Log::info('Coche creado con ID: ' . $car->id);

        return redirect()->route('cars.show', $car)->with('success', 'Coche creado correctamente. Está pendiente de revisión.');
    }

    public function show(Cars $car)
    {
        $car->load('vendedor', 'marcha', 'combustible', 'color', 'marca', 'modelo', 'status');
        return view('cars.show', compact('car'));
    }

    public function edit(Cars $car)
    {
        $this->authorize('update', $car);

        $brands = Brands::all();
        $fuels = Fuels::all();
        $gears = Gears::all();
        $colors = Color::all();
        return view('cars.edit', compact('car', 'brands', 'fuels', 'gears', 'colors'));
    }

    public function update(UpdateCarRequest $request, Cars $car)
    {
        $this->authorize('update', $car);

        $car->fill($request->except('image'));

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('cars', 'public');
            $car->image = $path;
            ProcessCarImageJob::dispatch($car->id);
        }

        $car->save();

        return redirect()->route('cars.index')->with('success', 'Coche actualizado correctamente.');
    }

    public function destroy(Cars $car)
    {
        $this->authorize('delete', $car);

        $car->delete();
        return redirect()->route('cars.index')->with('success', 'Coche eliminado correctamente.');
    }

    public function setStatusSale(Cars $car)
    {
        if ($car->id_vendedor !== Auth::user()->customer->id) abort(403);
        $saleType = ListingType::where('nombre', 'Venta')->first()->id;

        if (in_array($car->id_estado, [3, 6])) {
             $car->update(['id_estado' => 1, 'id_listing_type' => $saleType]);
             return redirect()->back()->with('success', 'Coche puesto en venta.');
        }
        return redirect()->back()->with('error', 'No se puede cambiar el estado.');
    }

    public function setStatusRent(Cars $car)
    {
        if ($car->id_vendedor !== Auth::user()->customer->id) abort(403);
        $rentType = ListingType::where('nombre', 'Alquiler')->first()->id;

        if ($car->id_estado == 1) {
             $car->update(['id_estado' => 3, 'id_listing_type' => $rentType]);
             return redirect()->back()->with('success', 'Coche puesto en alquiler.');
        }
        return redirect()->back()->with('error', 'No se puede cambiar el estado.');
    }
}
