<?php

namespace App\Http\Controllers;

use App\Events\CarCreated;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CarsController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $query = Cars::available();

        if ($request->has('search') && $request->search != '') {
            $query->search($request->search);
        }

        $cars = $query->inRandomOrder()->paginate(51);

        return view('cars.index', compact('cars'));
    }

    public function myCars()
    {
        if (!Auth::check() || !Auth::user()->customer) {
            return redirect()->route('login');
        }

        $sellerId = Auth::user()->customer->id;
        $cars = Cars::bySeller($sellerId)->paginate(50);

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

        $brandName = $request->temp_brand ? trim($request->temp_brand) : Brands::find($request->id_marca)->nombre;
        $modelName = $request->temp_model ? trim($request->temp_model) : CarModels::find($request->id_modelo)->nombre;
        $title = trim("$brandName $modelName " . $request->anyo_matri);

        // Aplicar IVA (21%) y Comisión (5%) al precio base
        $data = $request->all();
        $basePrice = $data['precio'];
        $iva = $basePrice * 0.21;
        $commission = $basePrice * 0.05;
        $data['precio'] = $basePrice + $iva + $commission;

        $car = new Cars($data);
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

        CarCreated::dispatch($car);

        return redirect()->route('cars.show', $car)->with('success', 'Coche creado correctamente. Se ha aplicado el 21% de IVA y 5% de comisión al precio.');
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

        $car->fill($request->except(['image', 'delete_image']));

        if ($request->has('delete_image') && $car->image) {
            Storage::disk('public')->delete($car->image);
            $car->image = null;
        }

        if ($request->hasFile('image')) {
            if ($car->image && !$request->has('delete_image')) {
                 Storage::disk('public')->delete($car->image);
            }

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

        if ($car->image) {
            Storage::disk('public')->delete($car->image);
        }

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
