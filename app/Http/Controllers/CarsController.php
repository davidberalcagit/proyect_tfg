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
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CarsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    /**
     * Display a listing of the resource.
     */
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
        $user = Auth::user();

        if (!$user) {
            abort(401);
        }

        if (!$user->customer) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'El usuario no tiene un perfil de vendedor asociado.'], 403);
            }
            abort(403, 'El usuario no tiene un perfil de vendedor asociado.');
        }

        // Pre-procesar request: Si es "other", convertir a null para que pase la validación 'exists'
        $input = $request->all();
        if ($request->id_marca === 'other') $input['id_marca'] = null;
        if ($request->id_modelo === 'other') $input['id_modelo'] = null;
        if ($request->id_color === 'other') $input['id_color'] = null;

        $request->replace($input);

        $request->validate([
            'id_marca' => 'nullable|exists:brands,id',
            'id_modelo' => 'nullable|exists:car_models,id',

            // Si id_marca es null (porque era "other" o vacío), temp_brand es requerido
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
        ]);

        // Validación personalizada para duplicados en "Otro"
        if ($request->temp_brand) {
            $tempBrand = trim($request->temp_brand);
            if (Brands::where('nombre', 'LIKE', $tempBrand)->exists()) {
                throw ValidationException::withMessages(['temp_brand' => 'La marca "' . $tempBrand . '" ya existe. Por favor selecciónela de la lista.']);
            }
        }

        if ($request->temp_model) {
            $tempModel = trim($request->temp_model);
            if ($request->id_marca) {
                if (CarModels::where('nombre', 'LIKE', $tempModel)->where('id_marca', $request->id_marca)->exists()) {
                    throw ValidationException::withMessages(['temp_model' => 'El modelo "' . $tempModel . '" ya existe para esta marca.']);
                }
            }
        }

        if ($request->temp_color) {
            $tempColor = trim($request->temp_color);
            if (Color::where('nombre', 'LIKE', $tempColor)->exists()) {
                throw ValidationException::withMessages(['temp_color' => 'El color "' . $tempColor . '" ya existe. Por favor selecciónelo de la lista.']);
            }
        }

        // Generar Título Automático
        $brandName = '';
        $modelName = '';

        if ($request->temp_brand) {
            $brandName = trim($request->temp_brand);
        } elseif ($request->id_marca) {
            $brandName = Brands::find($request->id_marca)->nombre;
        }

        if ($request->temp_model) {
            $modelName = trim($request->temp_model);
        } elseif ($request->id_modelo) {
            $modelName = CarModels::find($request->id_modelo)->nombre;
        }

        $title = trim("$brandName $modelName " . $request->anyo_matri);

        $car = new Cars($request->all());
        $car->title = $title;
        $car->id_vendedor = $user->customer->id;
        $car->id_estado = 4;

        // Asegurar que los IDs sean null si usamos temporales
        if ($request->temp_brand) $car->id_marca = null;
        if ($request->temp_model) $car->id_modelo = null;
        if ($request->temp_color) $car->id_color = null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('cars', 'public');
            $car->image = $path;
        }

        $car->save();

        Log::info('Coche creado con ID: ' . $car->id);

        // DEBUG: Ver a dónde redirige
        // dd('Redirigiendo a show', route('cars.show', $car));

        return redirect()->route('cars.show', $car)->with('success', 'Coche creado correctamente. Está pendiente de revisión.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Cars $car)
    {
        // DEBUG: Ver si llega al show
        // dd('Llegó al show', $car);

        $car->load('vendedor', 'marcha', 'combustible', 'color', 'marca', 'modelo', 'status');
        return view('cars.show', compact('car'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cars $car)
    {
        if ($car->id_estado == 1) {
            return redirect()->back()->with('error', 'No puedes editar un coche aprobado.');
        }

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
        if ($car->id_estado == 1) {
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
        }

        $car->save();

        return redirect()->route('cars.index')->with('success', 'Coche actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cars $car)
    {
        $car->delete();
        return redirect()->route('cars.index')->with('success', 'Coche eliminado correctamente.');
    }
}
