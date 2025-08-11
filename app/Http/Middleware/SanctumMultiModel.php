<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;
use App\Models\User;
use App\Models\Cliente;

/**
 * Middleware para autenticación con múltiples modelos usando Sanctum
 * Permite autenticar tanto usuarios como clientes mediante tokens
 */
class SanctumMultiModel
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token no proporcionado'
            ], 401);
        }
        
        // Buscar el token en la tabla personal_access_tokens
        $accessToken = PersonalAccessToken::findToken($token);
        
        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token inválido'
            ], 401);
        }
        
        // Obtener el modelo tokenable
        $tokenable = $accessToken->tokenable;
        
        if (!$tokenable) {
            return response()->json([
                'success' => false,
                'message' => 'Token sin modelo asociado'
            ], 401);
        }
        
        // Establecer el usuario autenticado según el tipo de modelo
        if ($tokenable instanceof User) {
            Auth::setUser($tokenable);
        } elseif ($tokenable instanceof Cliente) {
            // Para clientes, establecemos manualmente el usuario en el request
            $request->setUserResolver(function () use ($tokenable) {
                return $tokenable;
            });
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de token no válido'
            ], 401);
        }
        
        // Actualizar last_used_at
        $accessToken->update(['last_used_at' => now()]);
        
        return $next($request);
    }
}
