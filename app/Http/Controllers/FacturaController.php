<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Factura;
use App\Models\User;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use App\Mail\FacturaNotificacion;
use App\Notifications\FacturaCreada;
use App\Models\FacturaDetalle;

class FacturaController extends Controller
{

    public function index()
    {
        $facturas = Factura::with('cliente','usuario','pagos')->latest()->paginate(15);
        $usuarioId = Auth::id();
        $roles = Auth::user() ? Auth::user()->roles->pluck('name') : collect();
        return view('facturas.index', compact('facturas','usuarioId','roles'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre')->get();
        $productos = Producto::select('id','nombre','precio')->orderBy('nombre')->get();
        return view('facturas.create', compact('clientes','productos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'productos' => 'required|array|min:1',
            'productos.*.producto_id' => 'required|exists:productos,id',
            'productos.*.cantidad' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0.0;
            $detalles = [];

            foreach ($request->input('productos', []) as $item) {
                $producto = Producto::findOrFail($item['producto_id']);
                $cantidad = (int) $item['cantidad'];

                // Validar stock suficiente (opcional, descomentar si es necesario)
                if ($producto->stock < $cantidad) {
                    throw new \RuntimeException("Stock insuficiente para {$producto->nombre}");
                }

                $precio = (float) $producto->precio;
                $linea = round($precio * $cantidad, 2);
                $subtotal += $linea;

                $detalles[] = [
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal' => $linea,
                ];

                // Descontar stock (opcional)
                $producto->decrement('stock', $cantidad);
            }

            // Regla: descuento 12% si subtotal > 100
            $descuento = $subtotal > 100 ? round($subtotal * 0.12, 2) : 0.0;
            $baseImponible = max($subtotal - $descuento, 0);
            $iva = round($baseImponible * 0.12, 2);
            $total = round($baseImponible + $iva, 2);

            $factura = Factura::create([
                'cliente_id' => $request->cliente_id,
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'descuento' => $descuento,
                'iva' => $iva,
                'total' => $total,
                'anulada' => false,
                'pagada' => false,
                'estado' => 'pendiente',
                'created_by' => Auth::id(),
            ]);

            foreach ($detalles as $d) {
                FacturaDetalle::create($d + ['factura_id' => $factura->id]);
            }

            DB::commit();
            return redirect()->route('facturas.index')->with('success','Factura creada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => $e->getMessage()])->withInput();
        }
    }

    public function descargarPDF(Factura $factura)
    {
        $factura->load(['cliente','detalles.producto','pagos']);
        $pdf = Pdf::loadView('facturas.pdf', ['factura' => $factura, 'isPdf' => true]);
        $nombre = 'Factura_' . $factura->id . '.pdf';
        return $pdf->download($nombre);
    }

    public function enviarPDF(Factura $factura)
    {
        try {
            $factura->load(['cliente','detalles.producto','pagos']);
            Mail::to($factura->cliente->email)->send(new FacturaNotificacion($factura));
            return back()->with('success', 'Factura enviada por correo correctamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo enviar el correo: ' . $e->getMessage());
        }
    }

    public function notificar(Factura $factura)
    {
        try {
            $factura->load(['cliente','detalles.producto','pagos']);
            Notification::send($factura->cliente, new FacturaCreada($factura));
            return back()->with('success', 'Notificación enviada correctamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo enviar la notificación: ' . $e->getMessage());
        }
    }

    public function anular(Factura $factura)
    {
        $user = Auth::user();

        if ($factura->anulada) {
            return back()->with('error', 'La factura ya está anulada.');
        }

        $esAdmin = $user->roles->pluck('name')->contains('Administrador');
        if ($factura->user_id !== $user->id && !$esAdmin) {
            abort(403, 'No tiene permisos para anular esta factura.');
        }

        $factura->anulada = true;
        $factura->estado = 'anulada';
        $factura->save();

        return back()->with('success', 'Factura anulada correctamente.');
    }
}
