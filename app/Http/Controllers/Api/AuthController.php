<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Jobs\SendWelcomeEmailJob;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * @group Autenticación
 * Endpoints para gestion de sesion de usuarios
 */
class AuthController extends Controller
{
    /**
     * Iniciar Sesión
     *
     * Autentica a un usuario y devuelve un token de acceso.
     *
     * @bodyParam email string required El email del usuario. Example: admin@example.com
     * @bodyParam password string required La contraseña del usuario. Example: password
     *
     * @response {
     *  "message": "Hola Admin",
     *  "accessToken": "1|laravel_sanctum_token...",
     *  "token_type": "Bearer",
     *  "user": { "id": 1, "name": "Admin", "email": "admin@example.com" }
     * }
     * @response 401 {
     *  "message": "Credenciales incorrectas"
     * }
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $user = User::where('email', $request['email'])->firstOrFail();
        $token = $user->createToken('auth_token')->plainTextToken;
        // SendWelcomeEmailJob::dispatch($user); // Deshabilitado para evitar envío de correos en login API

        return response()->json([
            'message' => 'Hola ' . $user->name,
            'accessToken' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    /**
     * Cerrar Sesión
     *
     * Revoca el token actual del usuario.
     *
     * @authenticated
     * @response {
     *  "message": "Sesión cerrada correctamente"
     * }
     */
    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente'
        ]);
    }
}
