<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Pagos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PagosController extends Controller
{

    /**
     * Mostrar lista de pagos pendientes de revisión
     */
    public function index()
    {
        $pagos = Pagos::with(['factura.cliente', 'pagador'])
            ->where('estado', 'pendiente')
            ->latest()
            ->paginate(10);

        return view('pagos.index', compact('pagos'));
    }

    /**
     * Registrar un pago (API / Web con Sanctum)
     */
    public function store(Request $request)
    {
        $user = $request->user(); // Usuario autenticado via Sanctum

        $validated = $request->validate([
            'factura_id' => 'sometimes|integer|exists:facturas,id',
            'tipo_pago' => 'required|string|in:efectivo,tarjeta,transferencia,cheque',
            'numero_transaccion' => 'nullable|string|max:255',
            'monto' => 'required|numeric|min:0.01',
            'observacion' => 'nullable|string',
        ]);

        $facturaId = $validated['factura_id'] ?? (int) $request->route('factura');
        $factura = Factura::findOrFail($facturaId);

        // Seguridad: un cliente solo puede pagar sus propias facturas
        if ((int) $factura->cliente_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permiso para pagar esta factura.'
            ], 403);
        }

        // Validaciones de estado
        if ($factura->anulada) {
            return response()->json([
                'success' => false,
                'message' => 'La factura está anulada.'
            ], 422);
        }

        if ($factura->pagada) {
            return response()->json([
                'success' => false,
                'message' => 'La factura ya está pagada.'
            ], 422);
        }

        // Validar que el monto coincida con el total de la factura
        if (round((float) $validated['monto'], 2) !== round((float) $factura->total, 2)) {
            return response()->json([
                'success' => false,
                'message' => 'El monto pagado debe coincidir con el total de la factura.'
            ], 422);
        }

        // Crear pago en estado pendiente
        $pago = Pagos::create([
            'factura_id' => $factura->id,
            'tipo_pago' => $validated['tipo_pago'],
            'numero_transaccion' => $validated['numero_transaccion'] ?? null,
            'monto' => $validated['monto'],
            'observacion' => $validated['observacion'] ?? null,
            'estado' => 'pendiente',
            'pagado_por' => $user->id,
        ]);

        return response()->json([
            'success' => true,
            'data' => $pago->fresh(),
            'message' => 'Pago registrado correctamente y pendiente de validación.'
        ], 201);
    }

    /**
     * Aprobar un pago
     */
    public function approve($id)
    {
        $pago = Pagos::with('factura')->findOrFail($id);

        if ($pago->estado !== 'pendiente') {
            return redirect()->back()->with('error', 'Este pago ya fue procesado.');
        }

        // Marcar el pago como aprobado
        $pago->estado = 'aprobado';
        $pago->validado_por = Auth::id();
        $pago->validated_at = now();
        $pago->save();

        // Actualizar estado de factura como pagada
        if ($pago->factura) {
            $pago->factura->pagada = true;
            $pago->factura->estado = 'aprobado'; // mantener consistencia con migración existente
            $pago->factura->save();
        }

        // TODO: Notificar al cliente vía correo electrónico
        // Notification::send($pago->factura->cliente, new PagoAprobadoNotification($pago));

        return redirect()->back()->with('success', 'Pago aprobado correctamente.');
    }

    /**
     * Rechazar un pago
     */
    public function reject($id)
    {
        $pago = Pagos::with('factura')->findOrFail($id);

        if ($pago->estado !== 'pendiente') {
            return redirect()->back()->with('error', 'Este pago ya fue procesado.');
        }

        $pago->estado = 'rechazado';
        $pago->validado_por = Auth::id();
        $pago->validated_at = now();
        $pago->save();

        // Si no existen otros pagos aprobados, mantener la factura como pendiente
        if ($pago->factura) {
            $tieneOtroAprobado = $pago->factura->pagos()->where('estado', 'aprobado')->exists();
            if (!$tieneOtroAprobado) {
                $pago->factura->pagada = false;
                $pago->factura->estado = 'pendiente';
                $pago->factura->save();
            }
        }

        // TODO: Notificar al cliente vía correo electrónico
        // Notification::send($pago->factura->cliente, new PagoRechazadoNotification($pago));

        return redirect()->back()->with('success', 'Pago rechazado correctamente.');
    }
}
