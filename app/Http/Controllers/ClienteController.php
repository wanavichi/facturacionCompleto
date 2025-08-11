<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\ClienteAccessToken;
use App\Models\Factura;
use App\Notifications\VerificarCorreoCliente;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\PersonalAccessToken;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::with('facturas')->paginate(10);
        
        // Obtener tokens de clientes desde la nueva tabla
        $tokens_clientes = \App\Models\ClienteAccessToken::with('cliente')
            ->latest()
            ->get();
        
        return view('clientes.index', compact('clientes', 'tokens_clientes'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email',
            'telefono' => 'required|string|max:15',
            'direccion' => 'required|string|max:255',
        ]);

        Auditoria::create([
            'user_id' => Auth::id(),
            'accion' => 'Registrar Cliente',
            'descripcion' => "Cliente {$request->nombre} registrado.",
            'modulo' => 'Clientes',
        ]);

        $cliente = Cliente::create($request->only('nombre', 'email', 'telefono', 'direccion'));
        $cliente->notify(new VerificarCorreoCliente());

        return back()->with('success', 'Cliente registrado correctamente. Se envió un correo de verificación.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre' => 'required',
            'email' => 'required|email|unique:clientes,email,' . $cliente->id,
        ]);

        Auditoria::create([
            'user_id' => Auth::id(),
            'accion' => 'Actualizar Cliente',
            'descripcion' => "Cliente {$cliente->nombre} actualizado.",
            'modulo' => 'Clientes',
        ]);

        $cliente->update($request->only('nombre', 'email', 'telefono', 'direccion'));
        return back()->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        // Este método no se usa porque ahora hay eliminación suave con motivo
    }

    public function eliminar(Request $request, Cliente $cliente)
    {
        $request->validate([
            'motivo_eliminacion' => 'required|string|max:255',
            'confirmacion' => 'accepted'
        ]);

        $cliente->motivo_eliminacion = $request->input('motivo_eliminacion');
        $cliente->delete();

        Auditoria::create([
            'user_id' => Auth::id(),
            'accion' => 'Eliminar Cliente',
            'descripcion' => "Cliente {$cliente->nombre} movido a papelera. Motivo: {$request->input('motivo_eliminacion')}",
            'modulo' => 'Clientes',
        ]);

        return back()->with('success', 'Cliente movido a papelera.');
    }

    public function restaurar(Request $request, $id)
    {
        $request->validate([
            'motivo_restauracion' => 'required|string|max:255',
        ]);

        $cliente = Cliente::onlyTrashed()->findOrFail($id);
        $cliente->restore();

        Auditoria::create([
            'user_id' => Auth::id(),
            'accion' => 'Restaurar Cliente',
            'descripcion' => "Cliente {$cliente->nombre} restaurado. Motivo: {$request->motivo_restauracion}",
            'modulo' => 'Clientes',
        ]);

        return back()->with('success', 'Cliente restaurado correctamente.');
    }

    public function eliminarDefinitivo(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string',
            'confirmacion' => 'accepted'
        ]);

        $usuarioAuth = Auth::user();

        if (!Hash::check($request->password, $usuarioAuth->password)) {
            return back()->withErrors(['password' => 'Contraseña incorrecta.']);
        }

        $cliente = Cliente::onlyTrashed()->findOrFail($id);
        $nombre = $cliente->nombre;
        $cliente->forceDelete();

        Auditoria::create([
            'user_id' => $usuarioAuth->id,
            'accion' => 'Eliminación definitiva de cliente',
            'descripcion' => "El cliente {$nombre} fue eliminado permanentemente desde la papelera.",
            'modulo' => 'Clientes',
        ]);

        return back()->with('success', 'Cliente eliminado permanentemente.');
    }

    public function verificarCorreo(Cliente $cliente, $hash)
    {
        if (sha1($cliente->email) === $hash) {
            $cliente->email_verified_at = now();
            $cliente->save();

            return redirect('/')->with('success', 'Correo verificado correctamente.');
        }

        return abort(403, 'Hash inválido');
    }

    public function papelera()
    {
        $clientes = Cliente::onlyTrashed()->paginate(10);
        return view('clientes.papelera', compact('clientes'));
    }

    /**
     * Crear token de acceso para un cliente
     */
    public function crearTokenCliente(Request $request)
    {
        $request->validate([
            'cliente' => 'required|exists:clientes,id',
            'token_name' => 'required|string|max:255',
        ]);

        /** @var Cliente $cliente */
        $cliente = Cliente::find($request->input('cliente'));
        
        // Usar el nuevo sistema de tokens para clientes
        $tokenData = ClienteAccessToken::createToken($cliente, $request->input('token_name'));
        
        // Registrar auditoría
        Auditoria::create([
            'user_id' => Auth::id(),
            'accion' => 'Crear Token Cliente',
            'descripcion' => "Token '{$request->input('token_name')}' creado para cliente {$cliente->nombre}",
            'modulo' => 'Clientes',
        ]);
        
        // Almacenar el token recién creado en la sesión para mostrarlo
        return redirect()->route('clientes.index')
            ->with('success', 'Token de acceso creado correctamente para el cliente.')
            ->with('nuevo_token_cliente', [
                'nombre' => $request->input('token_name'),
                'cliente' => $cliente->nombre,
                'token' => $tokenData['plainTextToken']
            ]);
    }
}
