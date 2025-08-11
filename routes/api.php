<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\FacturaApiController;
use App\Http\Controllers\Api\ClienteApiController;
use App\Http\Controllers\PagosController;

// Ruta pública para obtener información del usuario autenticado
Route::get('/user', function (Request $request) {
    return response()->json([
        'success' => true,
        'data' => $request->user(),
        'message' => 'Usuario autenticado obtenido exitosamente'
    ]);
})->middleware('multi.auth');

// RUTA DE PAGO (Cliente) protegida solo con Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/pagos', [PagosController::class, 'store'])->name('api.pagos.store');
});

// Rutas protegidas con autenticación personalizada
Route::middleware('multi.auth')->group(function () {

    // Rutas de facturas
    Route::prefix('facturas')->group(function () {
        Route::get('/', [FacturaApiController::class, 'index']);
        Route::get('/{id}', [FacturaApiController::class, 'show']);
        Route::post('/', [FacturaApiController::class, 'store']);
    });

    // Rutas de clientes
    Route::prefix('clientes')->group(function () {
        Route::get('/', [ClienteApiController::class, 'index']);
        Route::get('/{id}', [ClienteApiController::class, 'show']);
        // Listar facturas de un cliente específico
        Route::get('/{cliente}/facturas', [FacturaApiController::class, 'facturasPorCliente']);
    });


    // Ruta especial para que un cliente vea sus propias facturas
    Route::get('/mis-facturas', [FacturaApiController::class, 'misFacturas']);

    // Ruta de prueba para verificar tokens
    Route::middleware('auth:sanctum')->get('/test-auth', function (Request $request) {
        return response()->json([
            'success' => true,
            'authenticated' => true,
            'user_type' => get_class($request->user()),
            'user_data' => $request->user(),
            'message' => 'Token válido'
        ]);
    });;

    // Endpoint temporal para ver tokens disponibles (SOLO PARA DEBUGGING)
    Route::get('/debug-tokens', function () {
        $tokens = DB::table('personal_access_tokens')
            ->select('id', 'name', 'tokenable_type', 'tokenable_id', 'plain_text_token')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'tokens' => $tokens,
            'message' => 'Tokens disponibles (solo para debugging)'
        ]);
    });
});
