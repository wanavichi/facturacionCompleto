@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
    <h2 class="text-3xl font-bold mb-6 text-gray-800">Papelera de Usuarios</h2>

    <div class="flex justify-start mb-6">
        <a href="{{ route('usuarios.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-md font-semibold transition">
            ← Regresar
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded shadow-sm">
            {{ session('error') }}
        </div>  
    @endif

    <div class="overflow-x-auto bg-white shadow rounded-lg border border-gray-200">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Motivo de eliminación</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Eliminado el</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($usuarios as $usuario)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">{{ $usuario->name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $usuario->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 max-w-xs truncate" title="{{ $usuario->motivo_eliminacion }}">
                        {{ $usuario->motivo_eliminacion }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                        {{ $usuario->deleted_at->setTimezone(config('app.timezone'))->format('d/m/Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center space-x-3">
                        <button onclick="openModal('restaurarModal-{{ $usuario->id }}')"
                            class="text-green-600 hover:text-green-800 font-semibold focus:outline-none focus:underline">Restaurar</button>

                        <button onclick="openModal('eliminarDefinitivo-{{ $usuario->id }}')"
                            class="text-red-600 hover:text-red-800 font-semibold focus:outline-none focus:underline">Eliminar Definitivo</button>
                    </td>
                </tr>

                <!-- Modal Restaurar -->
                <div id="restaurarModal-{{ $usuario->id }}" 
                     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 px-4">
                    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                        <h3 class="text-xl font-bold mb-4 text-gray-800">Restaurar Usuario</h3>
                        <form method="POST" action="{{ route('usuarios.restaurar', $usuario->id) }}">
                            @csrf
                            <label class="block mb-2 text-sm font-medium text-gray-700">Motivo de restauración</label>
                            <textarea name="motivo_restauracion" required
                                class="w-full border border-gray-300 rounded-md px-3 py-2 mb-4 text-gray-700 focus:ring-2 focus:ring-green-500 focus:outline-none"
                                rows="3" placeholder="Explica por qué se restaura el usuario..."></textarea>

                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeModal('restaurarModal-{{ $usuario->id }}')"
                                    class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none">Cancelar</button>
                                <button type="submit"
                                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none">Restaurar</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Modal eliminar definitivo -->
                <div id="eliminarDefinitivo-{{ $usuario->id }}" 
                     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50 px-4">
                    <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                        <h3 class="text-xl font-bold mb-4 text-red-600">¿Eliminar Permanentemente?</h3>
                        <p class="mb-4 text-sm text-gray-700">Esta acción no se puede deshacer. Ingrese su contraseña para confirmar.</p>

                        <form method="POST" action="{{ route('usuarios.eliminarDefinitivo', $usuario->id) }}">
                            @csrf
                            @method('DELETE')

                            <label class="block mb-1 text-sm font-medium text-gray-700">Contraseña actual:</label>
                            <input type="password" name="password" required
                                class="w-full border border-gray-300 rounded-md px-3 py-2 mb-3 text-gray-700 focus:ring-2 focus:ring-red-500 focus:outline-none">

                            <label class="flex items-center mb-4 text-sm text-gray-700">
                                <input type="checkbox" name="confirmacion" class="mr-2" onchange="document.getElementById('btnDef-{{ $usuario->id }}').disabled = !this.checked">
                                Estoy consciente de que esta acción es irreversible.
                            </label>

                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeModal('eliminarDefinitivo-{{ $usuario->id }}')"
                                    class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300 focus:outline-none">Cancelar</button>
                                <button type="submit" id="btnDef-{{ $usuario->id }}" disabled
                                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none">Eliminar Definitivamente</button>
                            </div>
                        </form>
                    </div>
                </div>

                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 italic">No hay usuarios en papelera.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.body.style.overflow = 'hidden'; // Bloquear scroll al abrir modal
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.body.style.overflow = ''; // Restaurar scroll al cerrar modal
    }
</script>
@endsection
