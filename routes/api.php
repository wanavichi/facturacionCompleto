<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FacturaApiController;
use App\Http\Controllers\Api\ClienteApiController;
use App\Http\Controllers\PagosController;

// Perfil del autenticado (requiere token Sanctum)
Route::middleware('auth:sanctum')->get('/auth/me', function (Request $request) {
    return response()->json([
        'success' => true,
        'data' => $request->user(),
        'message' => 'Usuario autenticado obtenido exitosamente'
    ]);
});

// Grupo protegido con Sanctum + rate limiting para toda la API
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

    // Facturas
    Route::prefix('facturas')->group(function () {
        Route::get('/', [FacturaApiController::class, 'index']);
        Route::get('/{id}', [FacturaApiController::class, 'show'])->whereNumber('id');
        Route::post('/', [FacturaApiController::class, 'store']);
    });

    // Clientes
    Route::prefix('clientes')->group(function () {
        Route::get('/', [ClienteApiController::class, 'index']);
        Route::get('/{id}', [ClienteApiController::class, 'show'])->whereNumber('id');
        // Listar facturas de un cliente específico
        Route::get('/{cliente}/facturas', [FacturaApiController::class, 'facturasPorCliente'])->whereNumber('cliente');
    });

    // Facturas del cliente autenticado
    Route::get('/mis-facturas', [FacturaApiController::class, 'misFacturas']);

    // Pagos (Cliente)
    Route::post('/pagos', [PagosController::class, 'store'])->name('api.pagos.store');

    // Ruta de prueba de autenticación
    Route::get('/test-auth', function (Request $request) {
        return response()->json([
            'success' => true,
            'authenticated' => true,
            'user_type' => get_class($request->user()),
            'user_data' => $request->user(),
            'message' => 'Token válido'
        ]);
    });
});
