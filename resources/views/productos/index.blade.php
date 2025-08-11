@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-3xl font-semibold text-gray-800">Gestión de Productos</h2>
        <a href="{{ route('dashboard') }}" class="inline-flex items-center bg-gray-700 hover:bg-gray-800 text-white font-medium py-2 px-4 rounded-md shadow">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Regresar
        </a>
    </div>

    <button onclick="openModal('crearModal')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-5 rounded-md shadow mb-6">
        + Nuevo Producto
    </button>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4 border border-green-300">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="w-full table-auto divide-y divide-gray-200">
            <thead class="bg-gray-100 text-gray-700 text-sm uppercase tracking-wider">
                <tr>
                    <th class="px-6 py-3 text-left">Nombre</th>
                    <th class="px-6 py-3 text-left">Descripción</th>
                    <th class="px-6 py-3 text-left">Precio</th>
                    <th class="px-6 py-3 text-left">Stock</th>
                    <th class="px-6 py-3 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-sm text-gray-700">
                @foreach($productos as $producto)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $producto->nombre }}</td>
                        <td class="px-6 py-4">{{ $producto->descripcion }}</td>
                        <td class="px-6 py-4">${{ number_format($producto->precio, 2) }}</td>
                        <td class="px-6 py-4">{{ $producto->stock }}</td>
                        <td class="px-6 py-4 flex space-x-2">
                            <button onclick="openModal('editarModal-{{ $producto->id }}')" class="text-indigo-600 hover:underline">Editar</button>
                            <button onclick="openModal('eliminarModal-{{ $producto->id }}')" class="text-red-600 hover:underline">Eliminar</button>
                        </td>
                    </tr>

                    <!-- Modal Editar -->
                    <div id="editarModal-{{ $producto->id }}" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
                        <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md">
                            <h3 class="text-xl font-bold mb-4 text-indigo-700">Editar Producto</h3>
                            <form method="POST" action="{{ route('productos.update', $producto) }}">
                                @csrf @method('PUT')
                                <input type="text" name="nombre" value="{{ $producto->nombre }}" class="w-full mb-3 px-4 py-2 border rounded-md focus:outline-none focus:ring focus:ring-indigo-300" placeholder="Nombre">
                                <textarea name="descripcion" class="w-full mb-3 px-4 py-2 border rounded-md focus:outline-none focus:ring focus:ring-indigo-300" placeholder="Descripción">{{ $producto->descripcion }}</textarea>
                                <input type="number" step="0.01" name="precio" value="{{ $producto->precio }}" class="w-full mb-3 px-4 py-2 border rounded-md" placeholder="Precio">
                                <input type="number" name="stock" value="{{ $producto->stock }}" class="w-full mb-3 px-4 py-2 border rounded-md" placeholder="Stock">
                                <div class="flex justify-end">
                                    <button type="button" onclick="closeModal('editarModal-{{ $producto->id }}')" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                                    <button type="submit" class="bg-green-600 text-white px-4 py-2 ml-2 rounded-md hover:bg-green-700">Actualizar</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Modal Eliminar -->
                    <div id="eliminarModal-{{ $producto->id }}" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
                        <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md">
                            <h3 class="text-xl font-bold mb-3 text-red-600">¿Eliminar Producto?</h3>
                            <p class="text-gray-600 mb-4">Esta acción no se puede deshacer.</p>
                            <form method="POST" action="{{ route('productos.destroy', $producto) }}">
                                @csrf @method('DELETE')
                                <div class="flex justify-end">
                                    <button type="button" onclick="closeModal('eliminarModal-{{ $producto->id }}')" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancelar</button>
                                    <button type="submit" class="bg-red-600 text-white px-4 py-2 ml-2 rounded-md hover:bg-red-700">Eliminar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Crear con íconos bonitos -->
<div id="crearModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-md">
        <h3 class="text-2xl font-bold mb-6 text-indigo-700 flex items-center gap-2">
            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo Producto
        </h3>
        <form method="POST" action="{{ route('productos.store') }}">
            @csrf

            <!-- Nombre -->
            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-1">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M5.121 17.804A4 4 0 017 15h10a4 4 0 011.879 2.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Nombre
                </label>
                <input type="text" name="nombre" placeholder="Nombre del producto"
                       class="w-full px-4 py-2 border rounded-md focus:ring-indigo-300 focus:border-indigo-500 shadow-sm">
            </div>

            <!-- Descripción -->
            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-1">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M8 16h8M8 12h8m-8-4h8M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Descripción
                </label>
                <textarea name="descripcion" placeholder="Breve descripción"
                          class="w-full px-4 py-2 border rounded-md focus:ring-indigo-300 focus:border-indigo-500 shadow-sm"></textarea>
            </div>

            <!-- Precio -->
            <div class="mb-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-1">
                    <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M12 8c-1.333-2-4-2-4 1s2 4 4 4 4 0 4-2-2-3-4-3zM12 14h.01"/>
                    </svg>
                    Precio
                </label>
                <input type="number" step="0.01" name="precio" placeholder="$0.00"
                       class="w-full px-4 py-2 border rounded-md focus:ring-indigo-300 focus:border-indigo-500 shadow-sm">
            </div>

            <!-- Stock -->
            <div class="mb-6">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-1">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M3 3h18v18H3V3zm4 4h10v10H7V7z"/>
                    </svg>
                    Stock
                </label>
                <input type="number" name="stock" placeholder="Cantidad disponible"
                       class="w-full px-4 py-2 border rounded-md focus:ring-indigo-300 focus:border-indigo-500 shadow-sm">
            </div>

            <!-- Botones -->
            <div class="flex justify-end">
                <button type="button" onclick="closeModal('crearModal')"
                        class="px-4 py-2 text-gray-600 hover:text-gray-900 transition">Cancelar</button>
                <button type="submit"
                        class="ml-3 inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-md shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>


<!-- Scripts -->
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
@endsection
