<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pago;
use App\Models\Factura;
use App\Models\Pagos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class PagoApiController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum','role:cliente']);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'factura_id' => 'required|integer|exists:facturas,id',
            'tipo_pago' => 'required|in:efectivo,tarjeta,transferencia,cheque',
            'numero_transaccion' => 'nullable|string',
            'monto' => 'required|numeric|min:0.01',
            'observacion' => 'nullable|string',
        ]);

        $cliente = Auth::user();
        $factura = Factura::find($request->factura_id);

        if ($factura->cliente_id !== $cliente->id) {
            return response()->json(['message' => 'No puede pagar una factura que no le pertenece.'], 403);
        }

        if ($factura->estado === 'pagada') {
            return response()->json(['message' => 'La factura ya está pagada.'], 400);
        }

        if (floatval($request->monto) != floatval($factura->total)) {
            return response()->json(['message' => 'El monto del pago no coincide con el total de la factura.'], 400);
        }

        $pago = Pagos::create([
            'factura_id' => $factura->id,
            'tipo_pago' => $request->tipo_pago,
            'numero_transaccion' => $request->numero_transaccion,
            'monto' => $request->monto,
            'observacion' => $request->observacion,
            'pagado_por' => $cliente->id,
            'estado' => 'pendiente',
        ]);

        return response()->json(['message' => 'Pago registrado y en estado pendiente.','pago' => $pago], 201);
    }
}
