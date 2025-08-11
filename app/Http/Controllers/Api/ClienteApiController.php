<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Cliente;

/**
 * @group Clientes API
 * 
 * API endpoints para gestionar clientes
 */
class ClienteApiController extends Controller
{
    
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = min($request->input('per_page', 10), 50);
            
            $query = Cliente::select('id', 'nombre', 'email', 'telefono', 'direccion', 'created_at')
                ->orderBy('nombre', 'asc');
            
            // Filtro de búsqueda
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'ILIKE', "%{$search}%")
                      ->orWhere('email', 'ILIKE', "%{$search}%");
                });
            }
            
            $clientes = $query->paginate($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $clientes,
                'message' => 'Clientes obtenidos exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los clientes',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function show(Request $request, int $clienteId): JsonResponse
    {
        try {
            $query = Cliente::where('id', $clienteId);
            
            // Incluir facturas si se solicita
            if ($request->boolean('include_facturas')) {
                $query->with(['facturas:id,cliente_id,total,anulada,created_at']);
            }
            
            /** @var Cliente|null $cliente */
            $cliente = $query->first();
            
            if (!$cliente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cliente no encontrado'
                ], 404);
            }
            
            // Calcular estadísticas
            $facturas = $cliente->facturas ?? collect();
            $facturasActivas = $facturas->where('anulada', false);
            
            $clienteData = [
                'id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'email' => $cliente->email,
                'telefono' => $cliente->telefono,
                'direccion' => $cliente->direccion,
                'created_at' => $cliente->created_at,
                'facturas_count' => $facturasActivas->count(),
                'total_facturado' => $facturasActivas->sum('total'),
            ];
            
            if ($request->boolean('include_facturas')) {
                $clienteData['facturas'] = $facturas;
            }
            
            return response()->json([
                'success' => true,
                'data' => $clienteData,
                'message' => 'Cliente obtenido exitosamente'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
