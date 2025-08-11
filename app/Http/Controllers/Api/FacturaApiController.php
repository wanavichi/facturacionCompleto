<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Factura;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\FacturaDetalle;
use App\Models\Auditoria;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FacturaApiController extends Controller
{
    
    public function index(Request $request): JsonResponse
    {
        try {
            /** @var \App\Models\User|\App\Models\Cliente|null $user */
            $user = $request->user();
            
            // Verificar si el usuario autenticado es un cliente
            if (!$user instanceof \App\Models\Cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acceso denegado. Solo los clientes pueden acceder a esta API.'
                ], 403);
            }
            
            // En este punto, $user es definitivamente un Cliente
            $perPage = min($request->input('per_page', 10), 50); // Máximo 50 por página
            
            // Solo mostrar facturas del cliente autenticado
            $query = Factura::with(['cliente:id,nombre,email,telefono', 'detalles.producto:id,nombre'])
                ->where('cliente_id', $user->id)  // Usar $user después de verificación
                ->orderBy('created_at', 'desc');
            
            // Filtros opcionales
            if ($request->has('fecha_desde')) {
                $query->whereDate('created_at', '>=', $request->input('fecha_desde'));
            }
            
            if ($request->has('fecha_hasta')) {
                $query->whereDate('created_at', '<=', $request->input('fecha_hasta'));
            }
            
            $facturas = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $facturas,
                'message' => 'Facturas obtenidas exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las facturas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar facturas de un cliente específico
     * 
     * Retorna todas las facturas asociadas a un cliente específico.
     * Si el token pertenece a un cliente, solo puede ver sus propias facturas.
     * 
     * @authenticated
     * 
     * @urlParam cliente int required ID del cliente. Example: 1
     * @queryParam page int Número de página para paginación. Example: 1
     * @queryParam per_page int Cantidad de facturas por página (máximo 50). Example: 10
     * 
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "cliente": {
     *       "id": 1,
     *       "nombre": "Juan Pérez",
     *       "email": "juan@email.com",
     *       "telefono": "123456789"
     *     },
     *     "facturas": {
     *       "current_page": 1,
     *       "data": [
     *         {
     *           "id": 1,
     *           "total": 150.00,
     *           "anulada": false,
     *           "created_at": "2025-07-24T10:00:00.000000Z",
     *           "detalles": []
     *         }
     *       ],
     *       "per_page": 10,
     *       "total": 1
     *     }
     *   },
     *   "message": "Facturas del cliente obtenidas exitosamente"
     * }
     * 
     * @response 404 {
     *   "success": false,
     *   "message": "Cliente no encontrado"
     * }
     * 
     * @response 403 {
     *   "success": false,
     *   "message": "No tiene permisos para ver las facturas de este cliente"
     * }
     */
    public function facturasPorCliente(Request $request, int $clienteId): JsonResponse
    {
        try {
            /** @var Cliente|null $cliente */
            $cliente = Cliente::find($clienteId);
            
            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }
            
            // Obtener el usuario autenticado
            /** @var \App\Models\User|\App\Models\Cliente|null $authenticatedUser */
            $authenticatedUser = $request->user();
            
            if (!$authenticatedUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado'
                ], 401);
            }
            
            // Verificar permisos: si es un cliente, solo puede ver sus propias facturas
            if ($authenticatedUser instanceof Cliente && $authenticatedUser->id !== $clienteId) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permisos para ver las facturas de este cliente'
                ], 403);
            }
            
            $perPage = min($request->input('per_page', 10), 50);
            
            $facturas = Factura::where('cliente_id', $clienteId)
                ->with(['detalles.producto:id,nombre'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'cliente' => [
                        'id' => $cliente->id,
                        'nombre' => $cliente->nombre,
                        'email' => $cliente->email,
                        'telefono' => $cliente->telefono
                    ],
                    'facturas' => $facturas
                ],
                'message' => 'Facturas del cliente obtenidas exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las facturas del cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    public function show(int $facturaId): JsonResponse
    {
        try {
            /** @var Factura|null $factura */
            $factura = Factura::with([
                'cliente:id,nombre,email,telefono,direccion',
                'detalles.producto:id,nombre,descripcion',
                'usuario:id,name,email'
            ])->find($facturaId);
            
            if (!$factura) {
                return response()->json([
                    'success' => false,
                    'message' => 'Factura no encontrada'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $factura,
                'message' => 'Factura obtenida exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la factura',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'cliente_id' => 'required|exists:clientes,id',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            /** @var Factura $factura */
            $factura = Factura::create([
                'cliente_id' => $request->input('cliente_id'),
                'user_id' => Auth::id(),
                'total' => 0,
            ]);

            $total = 0;
            foreach ($request->input('productos', []) as $item) {
                /** @var Producto $producto */
                $producto = Producto::findOrFail($item['producto_id']);

                if ($producto->stock < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para {$producto->nombre}");
                }

                $subtotal = $producto->precio * $item['cantidad'];
                FacturaDetalle::create([
                    'factura_id' => $factura->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                    'subtotal' => $subtotal,
                ]);

                $producto->decrement('stock', $item['cantidad']);
                $total += $subtotal;
            }

            $factura->update(['total' => $total]);

            // Registrar auditoría
            Auditoria::create([
                'user_id' => Auth::id(),
                'accion' => 'Creación de factura vía API',
                'descripcion' => "Factura #{$factura->id} por \${$factura->total} para cliente {$factura->cliente->nombre}",
                'modulo' => 'Facturas API'
            ]);

            // Cargar relaciones para la respuesta
            $factura->load('cliente:id,nombre', 'detalles');

            DB::commit();
            
            return response()->json([
                'success' => true,
                'data' => $factura,
                'message' => 'Factura creada exitosamente'
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Obtener las facturas del cliente autenticado
     * 
     * Permite a un cliente obtener sus propias facturas usando su token.
     * 
     * @authenticated
     * 
     * @queryParam page int Número de página para paginación. Example: 1
     * @queryParam per_page int Cantidad de facturas por página (máximo 50). Example: 10
     * 
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "cliente": {
     *       "id": 1,
     *       "nombre": "Juan Pérez",
     *       "email": "juan@email.com",
     *       "telefono": "123456789"
     *     },
     *     "facturas": {
     *       "current_page": 1,
     *       "data": [
     *         {
     *           "id": 1,
     *           "total": 150.00,
     *           "anulada": false,
     *           "created_at": "2025-07-24T10:00:00.000000Z",
     *           "detalles": []
     *         }
     *       ],
     *       "per_page": 10,
     *       "total": 1
     *     }
     *   },
     *   "message": "Sus facturas obtenidas exitosamente"
     * }
     * 
     * @response 401 {
     *   "success": false,
     *   "message": "Token no válido o no pertenece a un cliente"
     * }
     */
    /**
     * Obtener las facturas del cliente autenticado
     * 
     * Permite a un cliente obtener sus propias facturas usando su token.
     * 
     * @authenticated
     * 
     * @queryParam page int Número de página para paginación. Example: 1
     * @queryParam per_page int Cantidad de facturas por página (máximo 50). Example: 10
     */
    public function misFacturas(Request $request): JsonResponse
    {
        try {
            // Obtener el usuario autenticado (puede ser User o Cliente)
            /** @var \App\Models\User|\App\Models\Cliente|null $authenticatedUser */
            $authenticatedUser = $request->user();
            
            if (!$authenticatedUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'No autenticado'
                ], 401);
            }
            
            // Verificar si es un cliente
            if (!($authenticatedUser instanceof Cliente)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este endpoint es solo para clientes',
                    'debug' => [
                        'tipo_usuario' => get_class($authenticatedUser),
                        'esperado' => 'App\\Models\\Cliente'
                    ]
                ], 403);
            }
            
            $perPage = min($request->input('per_page', 10), 50);
            
            $facturas = Factura::where('cliente_id', $authenticatedUser->id)
                ->with(['detalles.producto:id,nombre'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'cliente' => [
                        'id' => $authenticatedUser->id,
                        'nombre' => $authenticatedUser->nombre,
                        'email' => $authenticatedUser->email,
                        'telefono' => $authenticatedUser->telefono
                    ],
                    'facturas' => $facturas
                ],
                'message' => 'Sus facturas obtenidas exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener sus facturas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
