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
     *          Iniciar sesion
     * Autenticación de un usuario y devuelve un token de acceso
     * @bodyParam email string required El email del usuario. Ejemplo: user@example.com
     * @bodyParam password string required La contraseña del usuario. Ejemplo: password
     *
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
        SendWelcomeEmailJob::dispatch($user);

        return response()->json([
            'message' => 'Hola ' . $user->name,
            'accessToken' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }
    /**
     *          Cerrar Sesión
     * Revoca el token actual del usuario.
     * @authenticated
     * @response {
     *  "message": "Logged out"
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
