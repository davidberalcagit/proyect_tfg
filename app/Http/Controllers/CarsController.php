<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCarImageJob;
use App\Models\Brands;
use App\Models\CarModels;
use App\Models\Cars;
use App\Models\Color;
use App\Models\Fuels;
use App\Models\Gears;
use App\Models\ListingType; // Importar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CarsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index()
    {
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

    public function create(Request $request)
    {
        $brands = Brands::all();
        $fuels  = Fuels::all();
        $gears = Gears::all();
        $colors = Color::all();

        // Obtener tipo de listado (sale/rent) y buscar su ID
        $typeSlug = $request->query('type', 'sale');
        $listingType = ListingType::where('nombre', $typeSlug === 'rent' ? 'Alquiler' : 'Venta')->first();

        // Si no existe, fallback al primero (Venta)
        if (!$listingType) $listingType = ListingType::first();

        return view('cars.create', compact('brands','fuels', 'gears', 'colors', 'listingType'));
    }

    public function getModels(Brands $brand)
    {
        $models = CarModels::where('id_marca', $brand->id)->get();
        return response()->json($models);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user) abort(401);
        if (!$user->customer) abort(403, 'El usuario no tiene un perfil de vendedor asociado.');

        $input = $request->all();
        if ($request->id_marca === 'other') $input['id_marca'] = null;
        if ($request->id_modelo === 'other') $input['id_modelo'] = null;
        if ($request->id_color === 'other') $input['id_color'] = null;

        $request->replace($input);

        $request->validate([
            'id_marca' => 'nullable|exists:brands,id',
            'id_modelo' => 'nullable|exists:car_models,id',
            'temp_brand' => 'required_without:id_marca|nullable|string|max:50',
            'temp_model' => 'required_without:id_modelo|nullable|string|max:50',
            'id_combustible' => 'required|integer|exists:fuels,id',
            'id_marcha' => 'required|integer|exists:gears,id',
            'precio' => 'required|numeric|min:0',
            'anyo_matri' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'km' => 'required|integer',
            'matricula' => ['required', 'string', 'max:255'],
            'id_color' => 'nullable|exists:colors,id',
            'temp_color' => 'required_without:id_color|nullable|string|max:50',
            'descripcion' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'id_listing_type' => 'required|exists:listing_types,id', // Validar ID
        ]);

        // Validaciones duplicados (Omitidas por brevedad)
        if ($request->temp_brand) {
            $tempBrand = trim($request->temp_brand);
            if (Brands::where('nombre', 'LIKE', $tempBrand)->exists()) {
                throw ValidationException::withMessages(['temp_brand' => 'La marca "' . $tempBrand . '" ya existe.']);
            }
        }
        // ...

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

    // ... show, edit, update, destroy ...
    public function show(Cars $car)
    {
        $car->load('vendedor', 'marcha', 'combustible', 'color', 'marca', 'modelo', 'status');
        return view('cars.show', compact('car'));
    }

    public function edit(Cars $car)
    {
        if ($car->id_estado == 1 && !Auth::user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'No puedes editar un coche aprobado.');
        }

        $brands = Brands::all();
        $fuels = Fuels::all();
        $gears = Gears::all();
        $colors = Color::all();
        return view('cars.edit', compact('car', 'brands', 'fuels', 'gears', 'colors'));
    }

    public function update(Request $request, Cars $car)
    {
        if ($car->id_estado == 1 && !Auth::user()->hasRole('admin')) {
            return redirect()->back()->with('error', 'No puedes editar un coche aprobado.');
        }

        $request->validate([
            'id_marca' => 'nullable|exists:brands,id',
            'id_modelo' => 'nullable|exists:car_models,id',
            'id_combustible' => 'required|integer|exists:fuels,id',
            'id_marcha' => 'required|integer|exists:gears,id',
            'precio' => 'required|numeric|min:0',
            'anyo_matri' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'km' => 'required|integer',
            'matricula' => 'required|string|max:255',
            'id_color' => 'nullable|exists:colors,id',
            'descripcion' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

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
        $car->delete();
        return redirect()->route('cars.index')->with('success', 'Coche eliminado correctamente.');
    }

    public function setStatusSale(Cars $car)
    {
        if ($car->id_vendedor !== Auth::user()->customer->id) abort(403);
        // Buscar ID de Venta
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
        // Buscar ID de Alquiler
        $rentType = ListingType::where('nombre', 'Alquiler')->first()->id;

        if ($car->id_estado == 1) {
             $car->update(['id_estado' => 3, 'id_listing_type' => $rentType]);
             return redirect()->back()->with('success', 'Coche puesto en alquiler.');
        }
        return redirect()->back()->with('error', 'No se puede cambiar el estado.');
    }
}
