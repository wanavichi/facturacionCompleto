<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class VerificarRol
{
    /**
     * Maneja una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles  Roles requeridos (admin, usuario, etc.)
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $usuario = Auth::user();

        // Verifica si el usuario está autenticado y activo
        if (!$usuario || !$usuario->activo) {
            Auth::logout();
            return redirect('/login')->withErrors([
                'msg' => 'Su sesión ha finalizado, contacte con el administrador.'
            ]);
        }

        // Verifica si el usuario tiene al menos uno de los roles requeridos
        // Asegúrate de que 'name' sea el nombre real del campo en tu tabla roles
        if (!$usuario->roles->pluck('name')->intersect($roles)->isNotEmpty()) {
            abort(403, 'Acceso no autorizado.');
        }

        return $next($request);
    }
}
