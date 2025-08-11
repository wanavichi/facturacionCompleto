<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Pagos;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PagoValidationController extends Controller
{
    

    public function index()
    {
        $pagos = Pagos::where('estado','pendiente')->with('factura','pagador')->latest()->paginate(15);
        return view('pagos.index', compact('pagos'));
    }

    public function approve($id)
    {
        $pago = Pagos::findOrFail($id);

        if ($pago->estado !== 'pendiente') {
            return redirect()->back()->with('error', 'El pago no está en estado pendiente.');
        }

        DB::transaction(function() use ($pago) {
            $pago->estado = 'aprobado';
            $pago->validado_por = Auth::id();
            $pago->validated_at = Carbon::now();
            $pago->save();

            $factura = $pago->factura;
            $factura->estado = 'pagada';
            $factura->save();
        });

        return redirect()->back()->with('success', 'Pago aprobado y factura marcada como pagada.');
    }

    public function reject($id)
    {
        $pago = Pagos::findOrFail($id);

        if ($pago->estado !== 'pendiente') {
            return redirect()->back()->with('error', 'El pago no está en estado pendiente.');
        }

        $pago->estado = 'rechazado';
        $pago->validado_por = Auth::id();
        $pago->validated_at = Carbon::now();
        $pago->save();

        return redirect()->back()->with('success', 'Pago rechazado.');
    }
}
