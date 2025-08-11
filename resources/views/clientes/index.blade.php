@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-10">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">👥 Gestión de Clientes</h2>

    <!-- Botón volver -->
    <div class="mb-6">
        <a href="{{ route('dashboard') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
            </svg>
            Regresar
        </a>
    </div>

    <!-- Botón Crear Cliente -->
    <div class="mb-6">
        <button onclick="openModal('crearModal')"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg shadow-md transition">
            ➕ Nuevo Cliente
        </button>
    </div>

<!-- Modal Crear Cliente -->
<div id="crearModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
        <!-- Botón Cerrar -->
        <button onclick="closeModal('crearModal')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>
        
        <!-- Título -->
        <h2 class="text-xl font-bold text-gray-800 mb-4">➕ Nuevo Cliente</h2>
        
        <!-- Formulario -->
        <form action="{{ route('clientes.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Nombre -->
            <div>
                <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre</label>
                <div class="relative">
                    <input type="text" name="nombre" id="nombre" 
                           placeholder="Ej: Juan Pérez" 
                           class="w-full border border-gray-300 rounded-md px-10 py-2 focus:ring-indigo-500 focus:outline-none" 
                           required>
                    <span class="absolute left-3 top-2.5 text-gray-400">
                        <i class="fas fa-user"></i>
                    </span>
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <div class="relative">
                    <input type="email" name="email" id="email" 
                           placeholder="Ej: juan@example.com" 
                           class="w-full border border-gray-300 rounded-md px-10 py-2 focus:ring-indigo-500 focus:outline-none" 
                           required>
                    <span class="absolute left-3 top-2.5 text-gray-400">
                        <i class="fas fa-envelope"></i>
                    </span>
                </div>
            </div>

            <!-- Teléfono -->
            <div>
                <label for="telefono" class="block text-sm font-medium text-gray-700">Teléfono</label>
                <div class="relative">
                    <input type="text" name="telefono" id="telefono" 
                           placeholder="Ej: +593 987654321" 
                           class="w-full border border-gray-300 rounded-md px-10 py-2 focus:ring-indigo-500 focus:outline-none">
                    <span class="absolute left-3 top-2.5 text-gray-400">
                        <i class="fas fa-phone-alt"></i>
                    </span>
                </div>
            </div>

            <!-- Dirección -->
            <div>
                <label for="direccion" class="block text-sm font-medium text-gray-700">Dirección</label>
                <div class="relative">
                    <input type="text" name="direccion" id="direccion" 
                           placeholder="Ej: Av. Siempre Viva 742" 
                           class="w-full border border-gray-300 rounded-md px-10 py-2 focus:ring-indigo-500 focus:outline-none">
                    <span class="absolute left-3 top-2.5 text-gray-400">
                        <i class="fas fa-map-marker-alt"></i>
                    </span>
                </div>
            </div>

            <!-- Botón Guardar -->
            <div class="flex justify-end">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>


    <!-- Mensaje de éxito -->
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-800 p-4 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Token para Cliente -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-10">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">🔐 Generar Token para Cliente</h3>
        <form action="{{ route('clientes.crearToken') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="cliente" class="block text-sm font-medium text-gray-700 mb-1">Cliente</label>
                <select name="cliente" id="cliente" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:outline-none" required>
                    <option value="">Seleccione un cliente</option>
                    @foreach ($clientes as $clienteItem)
                        <option value="{{ $clienteItem->id }}">{{ $clienteItem->nombre }} ({{ $clienteItem->email }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="token_name" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Token</label>
                <input type="text" name="token_name" id="token_name" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:outline-none" placeholder="Ej: API Facturas" required>
            </div>

            <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded-lg hover:bg-green-700 transition">
                Crear Token
            </button>
        </form>

        @if(session('nuevo_token_cliente'))
            <div class="mt-6 bg-blue-50 border border-blue-300 p-4 rounded">
                <h4 class="font-bold mb-2">✅ Token creado:</h4>
                <p><strong>Cliente:</strong> {{ session('nuevo_token_cliente.cliente') }}</p>
                <p><strong>Token:</strong></p>
                <div class="mt-2 bg-white border px-4 py-2 rounded font-mono text-sm break-all">
                    {{ session('nuevo_token_cliente.token') }}
                </div>
                <p class="text-sm mt-2 text-gray-600">
                    <strong>Endpoint:</strong> <code>GET /api/clientes/{cliente_id}/facturas</code><br>
                    <strong>Header:</strong> <code>Authorization: Bearer {token}</code>
                </p>
            </div>
        @endif
    </div>

    <!-- Tabla de Clientes -->
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">📋 Lista de Clientes</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="px-4 py-3">Nombre</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Teléfono</th>
                        <th class="px-4 py-3">Dirección</th>
                        <th class="px-4 py-3">Verificación</th>
                        <th class="px-4 py-3">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($clientes as $cliente)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2">{{ $cliente->nombre }}</td>
                            <td class="px-4 py-2">{{ $cliente->email }}</td>
                            <td class="px-4 py-2">{{ $cliente->telefono }}</td>
                            <td class="px-4 py-2">{{ $cliente->direccion }}</td>
                            <td class="px-4 py-2">
                                @if($cliente->email_verified_at)
                                    <span class="inline-block px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">Verificado</span>
                                @else
                                    <span class="inline-block px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">No Verificado</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 space-x-2">
                                <button onclick="openModal('editarModal-{{ $cliente->id }}')" class="text-indigo-600 hover:underline">Editar</button>
                                <button onclick="openModal('eliminarModal-{{ $cliente->id }}')" class="text-red-600 hover:underline">Eliminar</button>
                            </td>
                        </tr>

                        <!-- Modal Editar Cliente -->
                        <div id="editarModal-{{ $cliente->id }}" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
                                <button onclick="closeModal('editarModal-{{ $cliente->id }}')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>
                                <h2 class="text-xl font-bold text-gray-800 mb-4">✏️ Editar Cliente</h2>
                                <form action="{{ route('clientes.update', $cliente->id) }}" method="POST" class="space-y-4">
                                    @csrf
                                    @method('PUT')
                                    <div>
                                        <label for="nombre-{{ $cliente->id }}" class="block text-sm font-medium text-gray-700">Nombre</label>
                                        <input type="text" name="nombre" id="nombre-{{ $cliente->id }}" value="{{ $cliente->nombre }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:outline-none" required>
                                    </div>
                                    <div>
                                        <label for="email-{{ $cliente->id }}" class="block text-sm font-medium text-gray-700">Email</label>
                                        <input type="email" name="email" id="email-{{ $cliente->id }}" value="{{ $cliente->email }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:outline-none" required>
                                    </div>
                                    <div>
                                        <label for="telefono-{{ $cliente->id }}" class="block text-sm font-medium text-gray-700">Teléfono</label>
                                        <input type="text" name="telefono" id="telefono-{{ $cliente->id }}" value="{{ $cliente->telefono }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:outline-none">
                                    </div>
                                    <div>
                                        <label for="direccion-{{ $cliente->id }}" class="block text-sm font-medium text-gray-700">Dirección</label>
                                        <input type="text" name="direccion" id="direccion-{{ $cliente->id }}" value="{{ $cliente->direccion }}" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:outline-none">
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">Actualizar</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Modal Eliminar Cliente -->
                        <div id="eliminarModal-{{ $cliente->id }}" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md relative">
                                <button onclick="closeModal('eliminarModal-{{ $cliente->id }}')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl font-bold">&times;</button>
                                <h2 class="text-xl font-bold text-red-600 mb-4">⚠️ Eliminar Cliente</h2>
                                <form action="{{ route('clientes.eliminar', $cliente->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="motivo_eliminacion" class="block text-sm font-medium text-gray-700">Motivo de eliminación</label>
                                        <input type="text" name="motivo_eliminacion" id="motivo_eliminacion" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-red-500 focus:outline-none" required>
                                    </div>
                                    <div class="mb-4 flex items-center">
                                        <input type="checkbox" name="confirmacion" id="confirmacion" required>
                                        <label for="confirmacion" class="ml-2 text-sm text-gray-700">Confirmo que deseo eliminar este cliente.</label>
                                    </div>
                                    <div class="flex justify-end space-x-2">
                                        <button type="button" onclick="closeModal('eliminarModal-{{ $cliente->id }}')" class="px-4 py-2 border rounded hover:bg-gray-100">Cancelar</button>
                                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">Eliminar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scripts para abrir/cerrar modales -->
<script>
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }
</script>
@endsection
