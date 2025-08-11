<?php

namespace App\Http\Controllers;

use App\Models\Pago;
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
        $pago->procesado_por = Auth::id();
        $pago->save();

        // Actualizar estado de factura como pagada
        if ($pago->factura) {
            $pago->factura->pagada = true;
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
        $pago->procesado_por = Auth::id();
        $pago->save();

        // Si la factura estaba marcada como pagada por error, la revertimos
        if ($pago->factura && $pago->factura->pagada) {
            $pago->factura->pagada = false;
            $pago->factura->save();
        }

        // TODO: Notificar al cliente vía correo electrónico
        // Notification::send($pago->factura->cliente, new PagoRechazadoNotification($pago));

        return redirect()->back()->with('success', 'Pago rechazado correctamente.');
    }
}
