<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Factura;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FacturaController extends Controller
{

    public function index()
    {
        $facturas = Factura::with('cliente','creador')->latest()->paginate(15);
        return view('facturas.index', compact('facturas'));
    }

    public function create()
    {
        $clientes = User::role('cliente')->get();
        return view('facturas.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:users,id',
            'total' => 'required|numeric|min:0.01',
        ]);

        Factura::create([
            'cliente_id' => $request->cliente_id,
            'total' => $request->total,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('facturas.index')->with('success','Factura creada correctamente.');
    }
}
