@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-12 px-4">

  <!-- Título y botón regresar -->
  <div class="flex justify-between items-center mb-10">
    <div class="flex items-center space-x-3">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-9 w-9 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
      </svg>
      <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight">Gestión de Usuarios</h2>
    </div>
    <a href="{{ route('dashboard') }}" 
       class="inline-flex items-center px-5 py-2 border-2 border-indigo-600 text-indigo-600 rounded-xl font-semibold transition hover:bg-indigo-600 hover:text-white shadow-md">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
      </svg>
      Volver
    </a>
  </div>

  <!-- Buscador -->
  <form action="{{ route('usuarios.index') }}" method="GET" class="flex space-x-4 mb-8">
    <div class="relative flex-grow">
      <input 
        type="search" name="search" id="search"
        class="w-full border-b-2 border-gray-300 focus:border-indigo-600 bg-transparent py-3 px-4 text-lg placeholder-gray-400 text-gray-900 font-semibold outline-none transition"
        placeholder="Buscar usuarios..."
        value="{{ request('search') }}"
      >
      <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
        <svg class="w-6 h-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <circle cx="11" cy="11" r="7" stroke-linecap="round" stroke-linejoin="round"/>
          <line x1="21" y1="21" x2="16.65" y2="16.65" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </div>
    </div>
    <button type="submit" 
            class="bg-indigo-600 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:bg-indigo-700 transition">
      Buscar
    </button>
    @if(request('search'))
      <a href="{{ route('usuarios.index') }}" 
         class="px-6 py-3 border border-gray-300 rounded-xl font-semibold text-gray-600 hover:bg-gray-100 transition">
        Limpiar
      </a>
    @endif
  </form>

  <!-- Botón Nuevo Usuario -->
  <button onclick="openModal('crearModal')" 
          class="mb-10 px-7 py-3 bg-gradient-to-r from-green-400 to-blue-500 text-white font-bold rounded-2xl shadow-lg hover:from-green-500 hover:to-blue-600 transition">
    + Nuevo Usuario
  </button>

  @if(session('success'))
    <div class="mb-10 p-4 bg-green-100 text-green-900 rounded-xl shadow-inner font-semibold text-center">
      {{ session('success') }}
    </div>
  @endif

  <!-- Tabla -->
  <div class="bg-white rounded-3xl shadow-xl overflow-x-auto">
    <table class="w-full min-w-[700px] text-gray-900">
      <thead>
        <tr class="bg-indigo-50 text-indigo-700 uppercase text-sm font-semibold tracking-wide rounded-t-3xl">
          <th class="py-4 px-6">Nombre</th>
          <th class="py-4 px-6">Email</th>
          <th class="py-4 px-6">Roles</th>
          <th class="py-4 px-6">Estado</th>
          <th class="py-4 px-6">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($usuarios as $usuario)
          <tr class="border-b border-gray-200 hover:bg-indigo-50 transition cursor-default">
            <td class="py-4 px-6 font-medium">{{ $usuario->name }}</td>
            <td class="py-4 px-6">{{ $usuario->email }}</td>
            <td class="py-4 px-6 w-52">
              <form method="POST" action="{{ route('usuarios.asignarRol', $usuario) }}">
                @csrf
                <select name="roles[]" multiple 
                  class="w-full border-0 border-b-2 border-gray-300 focus:border-green-400 focus:outline-none text-gray-800 font-semibold rounded-none">
                  @foreach(\App\Models\Role::all() as $rol)
                    <option value="{{ $rol->id }}" @if($usuario->roles->contains($rol)) selected @endif>
                      {{ $rol->nombre }}
                    </option>
                  @endforeach
                </select>
                <button type="submit"
                  class="mt-3 w-full bg-green-400 text-white py-2 rounded-xl font-bold shadow-md hover:bg-green-500 transition">
                  Actualizar
                </button>
              </form>
            </td>
            <td class="py-4 px-6 font-semibold">
              @if($usuario->activo)
                <span class="text-green-600 bg-green-100 px-3 py-1 rounded-full">Activo</span>
              @else
                <span class="text-red-600 bg-red-100 px-3 py-1 rounded-full">Inactivo</span>
              @endif
            </td>
            <td class="py-4 px-6 space-x-3 whitespace-nowrap text-indigo-700 font-semibold">
              <button onclick="openModal('editarModal-{{ $usuario->id }}')" 
                      class="hover:underline underline-offset-2">Editar</button>

              <button onclick="openModal('eliminarPapelera-{{ $usuario->id }}')" 
                      class="hover:underline underline-offset-2 text-red-600">Eliminar</button>

              @if($usuario->activo)
                <button onclick="openModal('inactivarModal-{{ $usuario->id }}')" 
                        class="hover:underline underline-offset-2 text-yellow-600">Inactivar</button>
              @else
                <form method="POST" action="{{ route('usuarios.toggleEstado', $usuario) }}" class="inline">
                  @csrf @method('PATCH')
                  <button type="submit" class="hover:underline underline-offset-2 text-green-600">Activar</button>
                </form>
              @endif
            </td>
          </tr>

          <!-- Aquí irían los modales, manteniendo estilo consistente -->
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- Modales (igual estilo que el anterior, con fondos translucidos, sombras suaves y bordes redondeados) -->
<!-- Ejemplo Modal Crear -->
<div id="crearModal" 
     class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300 z-50">
  <div class="bg-white rounded-3xl shadow-xl w-full max-w-md mx-4 p-8 relative">
    <h3 class="text-2xl font-extrabold mb-6 text-indigo-900">Nuevo Usuario</h3>
    <form method="POST" action="{{ route('usuarios.store') }}">
      @csrf
      <input type="text" name="name" placeholder="Nombre" required
             class="w-full mb-5 text-lg border-b-2 border-gray-300 focus:border-indigo-600 outline-none font-semibold text-gray-900">
      <input type="email" name="email" placeholder="Correo" required
             class="w-full mb-5 text-lg border-b-2 border-gray-300 focus:border-indigo-600 outline-none font-semibold text-gray-900">
      <input type="password" name="password" placeholder="Contraseña" required
             class="w-full mb-8 text-lg border-b-2 border-gray-300 focus:border-indigo-600 outline-none font-semibold text-gray-900">

      <div class="flex justify-end space-x-4">
        <button type="button" onclick="closeModal('crearModal')"
                class="px-6 py-2 rounded-xl border border-gray-300 hover:bg-gray-100 transition font-semibold">
          Cancelar
        </button>
        <button type="submit" 
                class="px-6 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold shadow-md transition">
          Guardar
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  // Abrir/Cerrar modales con fade
  function openModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove('opacity-0', 'pointer-events-none');
  }
  function closeModal(id) {
    const modal = document.getElementById(id);
    modal.classList.add('opacity-0', 'pointer-events-none');
  }
</script>
@endsection
