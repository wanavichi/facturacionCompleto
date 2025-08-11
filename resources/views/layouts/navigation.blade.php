<aside
  class="fixed top-0 left-0 h-full w-64 bg-white shadow-md border-r border-gray-200 dark:bg-gray-900 dark:border-gray-700 z-50 overflow-y-auto"
  id="sidebar"
>
  <style>
    .sidebar-link {
      position: relative;
      display: flex;
      align-items: center;
      padding: 0.75rem 1rem;
      font-weight: 600;
      font-size: 0.9rem;
      color: #374151;
      border-left: 4px solid transparent;
      border-radius: 0 0.5rem 0.5rem 0;
      transition:
        background-color 0.25s ease,
        color 0.25s ease,
        border-color 0.3s ease;
      user-select: none;
      gap: 0.75rem;
    }

    .sidebar-link:hover,
    .sidebar-link:focus {
      background-color: #e0e7ff;
      color: #4338ca;
      border-left-color: #4338ca;
      outline: none;
      cursor: pointer;
    }

    .sidebar-link.active {
      background-color: #c7d2fe;
      color: #312e81;
      border-left-color: #312e81;
      font-weight: 700;
    }

    .sidebar-link svg {
      width: 20px;
      height: 20px;
      fill: none;
      stroke: currentColor;
      stroke-width: 2;
      flex-shrink: 0;
      transition: stroke 0.25s ease;
    }

    .sidebar-section {
      font-size: 0.75rem;
      font-weight: 700;
      color: #6b7280;
      padding: 1rem 1rem 0.5rem 1rem;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      user-select: none;
    }

    .sidebar-divider {
      border-top: 1px solid #d1d5db;
      margin: 1rem 0;
    }

    form button.sidebar-link {
      background: none;
      border: none;
      width: 100%;
      text-align: left;
      padding-left: 1rem;
      font-weight: 600;
      color: #374151;
      border-left: 4px solid transparent;
      border-radius: 0 0.5rem 0.5rem 0;
      transition: background-color 0.25s ease, color 0.25s ease, border-color 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    form button.sidebar-link:hover,
    form button.sidebar-link:focus {
      background-color: #e0e7ff;
      color: #4338ca;
      border-left-color: #4338ca;
      cursor: pointer;
      outline: none;
    }
  </style>

<!-- Logo -->
<div class="flex items-center justify-center h-16 border-b border-gray-300 px-4">
  <a href="{{ route('dashboard') }}" class="flex items-center">
    <svg
      class="h-8 w-8 text-indigo-600"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
    >
      <!-- Icono de marcador de ubicación (pin) -->
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M12 11c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2z"
      />
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M12 21c-4-3.333-7-8-7-11a7 7 0 1114 0c0 3-3 7.667-7 11z"
      />
    </svg>
    <span class="ml-3 text-xl font-bold text-gray-800 dark:text-gray-200">GeoFacturas</span>
  </a>
</div>


  <!-- Menú de opciones -->
  <nav class="mt-4 space-y-1" role="menu">
    <a
      href="{{ route('dashboard') }}"
      class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
      tabindex="0"
      role="menuitem"
    >
      <!-- Icono Home -->
      <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        stroke="currentColor"
        fill="none"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <path d="M3 12l9-9 9 9" />
        <path d="M9 21V12H15V21" />
      </svg>
      Inicio
    </a>

    @if(Auth::user()->roles->contains('nombre', 'Administrador'))
    <a
      href="{{ route('usuarios.index') }}"
      class="sidebar-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}"
      role="menuitem"
    >
      <!-- Icono Usuarios -->
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <circle cx="12" cy="7" r="4" />
        <path d="M5.5 21a7.5 7.5 0 0 1 13 0" />
      </svg>
      Usuarios
    </a>
    @endif

    @if(Auth::user()->roles->contains('nombre', 'Administrador') || Auth::user()->roles->contains('nombre', 'Secretario'))
    <a
      href="{{ route('clientes.index') }}"
      class="sidebar-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}"
      role="menuitem"
    >
      <!-- Icono Clientes -->
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <circle cx="9" cy="7" r="4" />
        <path d="M17 11v-1a4 4 0 0 0-4-4H9" />
        <path d="M17 21v-2a4 4 0 0 0-4-4H9" />
      </svg>
      Clientes
    </a>
    @endif

    @if(Auth::user()->roles->contains('nombre', 'Administrador') || Auth::user()->roles->contains('nombre', 'Bodega'))
    <a
      href="{{ route('productos.index') }}"
      class="sidebar-link {{ request()->routeIs('productos.*') ? 'active' : '' }}"
      role="menuitem"
    >
      <!-- Icono Productos -->
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <rect x="3" y="3" width="7" height="7" />
        <rect x="14" y="3" width="7" height="7" />
        <rect x="14" y="14" width="7" height="7" />
        <rect x="3" y="14" width="7" height="7" />
      </svg>
      Productos
    </a>
    @endif

    @if(Auth::user()->roles->contains('nombre', 'Administrador') || Auth::user()->roles->contains('nombre', 'Ventas'))
    <a
      href="{{ route('facturas.index') }}"
      class="sidebar-link {{ request()->routeIs('facturas.*') ? 'active' : '' }}"
      role="menuitem"
    >
      <!-- Icono Facturas -->
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <path d="M4 4h16v16H4z" />
        <path d="M4 8h16" />
        <path d="M8 12h8" />
        <path d="M8 16h5" />
      </svg>
      Facturas
    </a>
    @endif

    @if(Auth::user()->roles->contains('nombre', 'Administrador'))
    <div class="sidebar-divider"></div>
    <div class="sidebar-section">Administración</div>

    <a
      href="{{ route('auditoria.index') }}"
      class="sidebar-link {{ request()->routeIs('auditoria.*') ? 'active' : '' }}"
      role="menuitem"
    >
      <!-- Icono Auditoría -->
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <circle cx="12" cy="12" r="10" />
        <path d="M12 6v6l4 2" />
      </svg>
      Auditoría
    </a>

    <a
      href="{{ route('usuarios.papelera') }}"
      class="sidebar-link {{ request()->routeIs('usuarios.papelera') ? 'active' : '' }}"
      role="menuitem"
    >
      <!-- Icono Papelera usuarios -->
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <polyline points="3 6 5 6 21 6" />
        <path d="M19 6l-2 14H7L5 6" />
        <path d="M10 11v6" />
        <path d="M14 11v6" />
        <path d="M9 6V4h6v2" />
      </svg>
      Papelera de usuarios
    </a>

    <a
      href="{{ route('clientes.papelera') }}"
      class="sidebar-link {{ request()->routeIs('clientes.papelera') ? 'active' : '' }}"
      role="menuitem"
    >
      <!-- Icono Papelera clientes -->
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <rect x="3" y="6" width="18" height="13" rx="2" ry="2" />
        <line x1="9" y1="6" x2="9" y2="19" />
        <line x1="15" y1="6" x2="15" y2="19" />
        <path d="M10 11h4" />
      </svg>
      Papelera de clientes
    </a>

    <a
      href="{{ route('productos.papelera') }}"
      class="sidebar-link {{ request()->routeIs('productos.papelera') ? 'active' : '' }}"
      role="menuitem"
    >
      <!-- Icono Papelera productos -->
      <svg
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
        <line x1="9" y1="4" x2="9" y2="22" />
        <line x1="15" y1="4" x2="15" y2="22" />
        <path d="M10 11h4" />
      </svg>
      Papelera de productos
    </a>
    @endif

  

    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="sidebar-link" role="menuitem">
        <!-- Icono Logout -->
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <path d="M17 16l4-4m0 0l-4-4m4 4H7" />
          <path d="M7 8v8" />
        </svg>
        Cerrar sesión
      </button>
    </form>
  </nav>
</aside>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('sidebar').scrollTop = 0;
  });
</script>
