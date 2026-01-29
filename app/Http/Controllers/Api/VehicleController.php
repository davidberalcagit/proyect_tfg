<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * @group Vehículos (Placeholder)
 *
 * Controlador reservado para futura expansión de tipos de vehículos.
 */
class VehicleController extends Controller
{
    /**
     * Listar Vehículos
     *
     * @response 200 []
     */
    public function index()
    {
        return response()->json([], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
