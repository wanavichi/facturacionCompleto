@extends('layouts.app')

@section('content')
<style>
    body {
        background: #f8fafc;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #1f2937;
        margin: 0;
        padding: 0;
    }

    h2 {
        font-size: 2rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 2rem;
        text-align: center;
    }

    .container-main {
        max-width: 1100px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    /* MÉTRICAS */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 3rem;
    }

    .metric-card-new {
        background: #ffffff;
        border-radius: 12px;
        padding: 1rem 1.2rem;
        box-shadow: 0 4px 8px rgb(0 0 0 / 0.1);
        color: #374151;
        display: flex;
        align-items: center;
        gap: 0.9rem;
        transition: background 0.3s ease, transform 0.3s ease;
        font-size: 0.85rem;
    }

    .metric-card-new:hover {
        background: #e0e7ff;
        transform: translateY(-5px);
        color: #1e40af;
    }

    .icon-wrapper {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: #4338ca;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        color: white;
        box-shadow: 0 3px 6px rgb(0 0 0 / 0.1);
        flex-shrink: 0;
    }

    .metric-text h3 {
        margin: 0 0 0.15rem 0;
        font-weight: 600;
        font-size: 1rem;
    }

    .metric-text p {
        font-weight: 700;
        font-size: 1.4rem;
        margin: 0;
    }


    /* ACCESOS RÁPIDOS - estilo nuevo: tarjetas verticales con icono arriba */
    .access-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1.6rem;
        justify-items: center;
    }

    .access-card-new {
        background: #ffffff;
        border-radius: 16px;
        width: 180px;
        padding: 1.5rem 1rem 2rem 1rem;
        color: #1f2937;
        font-weight: 600;
        text-decoration: none;
        box-shadow: 0 2px 6px rgb(0 0 0 / 0.08);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.9rem;
        transition: box-shadow 0.25s ease, transform 0.25s ease;
    }

    .access-card-new:hover {
        box-shadow: 0 8px 20px rgb(0 0 0 / 0.15);
        transform: translateY(-6px);
        color: #4338ca;
    }

    .icon-quickaccess {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background-color: #4338ca;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        color: white;
        box-shadow: 0 3px 10px rgb(0 0 0 / 0.1);
        transition: background-color 0.3s ease;
        flex-shrink: 0;
    }

    .access-card-new:hover .icon-quickaccess {
        background-color: #312e81;
    }

    .access-card-new span {
        font-size: 1rem;
        text-align: center;
        user-select: none;
    }

    /* GRÁFICO */
    .chart-container {
        background: #ffffff;
        padding: 2rem;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgb(0 0 0 / 0.08);
        margin-top: 3rem;
        max-width: 1100px;
        margin-left: auto;
        margin-right: auto;
    }

    .chart-container h3 {
        color: #4338ca;
        margin-bottom: 1rem;
        font-weight: 700;
        font-size: 1.4rem;
        text-align: center;
    }

    /* Mantengo los estilos originales para tabla y badges */
    table {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    th {
        background-color: #f3f4f6;
        color: #374151;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.75rem 1rem;
        text-align: left;
    }

    td {
        font-size: 0.95rem;
        color: #1f2937;
        padding: 0.65rem 1rem;
        vertical-align: middle;
    }

    .badge-activo {
        background: #d1fae5;
        color: #065f46;
        padding: 0.2rem 0.5rem;
        border-radius: 9999px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-block;
    }

    .badge-expirado {
        background: #fee2e2;
        color: #991b1b;
        padding: 0.2rem 0.5rem;
        border-radius: 9999px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-block;
    }

    /* --- MEJORAS FORMULARIO Y TABLA TOKENS --- */

    .token-form-container {
        background: #fff;
        border-radius: 12px;
        padding: 1.5rem 2rem;
        box-shadow: 0 6px 15px rgb(0 0 0 / 0.07);
        max-width: 600px;
        margin: 0 auto 2rem auto;
    }

    .token-form-container label {
        display: block;
        font-weight: 600;
        margin-bottom: 0.4rem;
        color: #4b5563;
        font-size: 0.9rem;
    }

    .token-form-container select,
    .token-form-container input[type="text"] {
        width: 100%;
        padding: 0.5rem 0.75rem;
        font-size: 1rem;
        border-radius: 0.5rem;
        border: 1.5px solid #d1d5db;
        transition: border-color 0.3s ease;
        outline-offset: 2px;
        box-sizing: border-box;
    }

    .token-form-container select:focus,
    .token-form-container input[type="text"]:focus {
        border-color: #4338ca;
        outline: none;
        box-shadow: 0 0 6px rgba(67, 56, 202, 0.35);
    }

    .token-form-container button[type="submit"] {
        background-color: #4338ca;
        color: white;
        font-weight: 600;
        padding: 0.55rem 1.5rem;
        border: none;
        border-radius: 0.6rem;
        font-size: 1rem;
        cursor: pointer;
        margin-top: 1rem;
        transition: background-color 0.3s ease;
        box-shadow: 0 5px 12px rgb(67 56 202 / 0.3);
        display: inline-block;
    }

    .token-form-container button[type="submit"]:hover {
        background-color: #312e81;
    }

    /* Mensajes éxito */
    .alert-success,
    .alert-info {
        max-width: 600px;
        margin: 1rem auto 2rem auto;
        padding: 0.75rem 1rem;
        border-radius: 0.6rem;
        font-weight: 600;
        box-shadow: 0 4px 12px rgb(0 0 0 / 0.08);
        text-align: center;
    }

    .alert-success {
        background-color: #dcfce7;
        border: 1.5px solid #22c55e;
        color: #166534;
    }

    .alert-info {
        background-color: #dbeafe;
        border: 1.5px solid #3b82f6;
        color: #1e40af;
    }

    /* Token creado */
    .token-created {
        background-color: #eff6ff;
        border: 1.5px solid #2563eb;
        padding: 1rem 1.2rem;
        border-radius: 0.75rem;
        max-width: 600px;
        margin: 1rem auto 2rem auto;
        box-shadow: 0 6px 18px rgb(37 99 235 / 0.25);
        font-size: 0.9rem;
        text-align: center;
    }

    .token-created h4 {
        font-weight: 700;
        color: #1e3a8a;
        margin-bottom: 0.5rem;
    }

    .token-created p {
        margin-bottom: 0.25rem;
        color: #1e40af;
    }

    .token-string {
        background-color: #e0e7ff;
        color: #312e81;
        padding: 0.6rem 0.8rem;
        border-radius: 0.5rem;
        font-family: 'Courier New', Courier, monospace;
        font-size: 0.85rem;
        user-select: all;
        word-break: break-word;
        margin-top: 0.7rem;
        box-shadow: inset 0 1px 4px rgb(49 46 129 / 0.15);
        display: inline-block;
        max-width: 100%;
    }

    /* Tabla tokens */
    .table-wrapper {
        max-width: 100%;
        overflow-x: auto;
        margin-bottom: 3rem;
        box-shadow: 0 6px 20px rgb(0 0 0 / 0.08);
        border-radius: 0.75rem;
        background: white;
        max-width: 1000px;
        margin-left: auto;
        margin-right: auto;
    }

    table.token-table {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        min-width: 700px;
        border-radius: 0.75rem;
        overflow: hidden;
    }

    thead tr.token-table-header {
        background-color: #4338ca;
        color: white;
        text-align: left;
    }

    th.token-table-header,
    td.token-table-cell {
        padding: 0.55rem 1rem;
        font-size: 0.9rem;
        vertical-align: middle;
        border-bottom: 1px solid #e5e7eb;
    }

    tbody tr.token-table-row:hover {
        background-color: #f0f4ff;
    }

    tbody td.token-table-cell {
        color: #374151;
    }

    .badge-activo {
        background-color: #22c55e;
        color: white;
        padding: 0.25rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-block;
    }

    .badge-expirado {
        background-color: #ef4444;
        color: white;
        padding: 0.25rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-block;
    }

    .token-hash-note {
        font-size: 0.75rem;
        color: #b91c1c;
        margin-top: 0.1rem;
        font-style: italic;
    }

    /* Responsive ajustes mínimos */
    @media (max-width: 768px) {
        h2 {
            font-size: 1.6rem;
        }

        .token-form-container {
            max-width: 100%;
            padding: 1rem 1.2rem;
        }

        table.token-table {
            min-width: unset;
        }

        th.token-table-header,
        td.token-table-cell {
            padding: 0.4rem 0.6rem;
            font-size: 0.85rem;
        }
    }
</style>

<div class="container-main">
    <h2>Bienvenido, {{ $usuario->name }}</h2>

    {{-- Formulario de creación de tokens --}}
    <section class="token-form-container" aria-label="Crear nuevo token de acceso">
        <form action="{{ route('usuarios.crearToken') }}" method="POST" novalidate>
            @csrf
            <div class="form-group">
                <label for="usuario" class="form-label">Seleccione un usuario</label>
                <select name="usuario" id="usuario" required aria-required="true" aria-describedby="usuarioHelp" class="form-select">
                    <option value="" disabled selected>Seleccione un usuario</option>
                    @foreach ($usuarios as $usuarioItem)
                    <option value="{{ $usuarioItem->id }}">{{ $usuarioItem->email }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mt-6">
                <label for="token_name" class="form-label">Nombre del Token de Acceso</label>
                <input type="text" name="token_name" id="token_name" placeholder="Ingrese un nombre para el token" required aria-required="true" autocomplete="off" maxlength="40" class="form-input" />
            </div>

            <button type="submit" aria-label="Crear Token de Acceso" class="btn-submit mt-6">Crear Token</button>
        </form>
    </section>

    {{-- Mensajes de éxito --}}
    @if(session('success'))
    <div class="alert-success" role="alert">
        {{ session('success') }}
    </div>
    @endif

    @if(session('nuevo_token'))
    <section class="token-created" role="region" aria-live="polite">
        <h4>🎉 ¡Token creado exitosamente!</h4>
        <p><strong>Usuario:</strong> <span class="highlight">{{ session('nuevo_token.usuario') }}</span></p>
        <p><strong>Nombre:</strong> <span class="highlight">{{ session('nuevo_token.nombre') }}</span></p>
        <p><strong>Token Original:</strong></p>
        <div class="token-string" tabindex="0" title="Token de acceso generado" aria-label="Token de acceso generado">
            {{ session('nuevo_token.token') }}
        </div>
    </section>
    @endif

    {{-- Tabla de tokens --}}
    <div class="table-wrapper" role="region" aria-label="Lista de tokens de acceso">
        <table class="token-table" role="table">
            <thead>
                <tr class="token-table-header">
                    <th class="token-table-header" scope="col">ID</th>
                    <th class="token-table-header" scope="col">Usuario</th>
                    <th class="token-table-header" scope="col">Nombre del Token</th>
                    <th class="token-table-header" scope="col">Token</th>
                    <th class="token-table-header" scope="col">Creación</th>
                    <th class="token-table-header" scope="col">Último Uso</th>
                    <th class="token-table-header" scope="col">Estado</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tokens as $token)
                <tr class="token-table-row">
                    <td class="token-table-cell">{{ $token->id }}</td>
                    <td class="token-table-cell">
                        <div>{{ $token->tokenable->email ?? 'Usuario no encontrado' }}</div>
                        <small style="color:#6b7280;">{{ $token->tokenable->name ?? '' }}</small>
                    </td>
                    <td class="token-table-cell">{{ $token->name }}</td>
                    <td class="token-table-cell">
                        @if($token->plain_text_token)
                        <span class="token-string" style="background:#dcfce7; color:#166534; font-size:0.85rem; padding: 0.2rem 0.5rem; border-radius:6px; display:inline-block;">
                            {{ $token->plain_text_token }}
                        </span>
                        @else
                        <span class="token-string" style="background:#fef2f2; color:#991b1b; font-size:0.85rem; padding: 0.2rem 0.5rem; border-radius:6px; display:inline-block;">
                            {{ $token->token }}
                        </span>
                        <div class="token-hash-note">Token anterior (solo hash)</div>
                        @endif
                    </td>
                    <td class="token-table-cell">{{ $token->created_at->format('d/m/Y H:i') }}</td>
                    <td class="token-table-cell">{{ $token->last_used_at ? $token->last_used_at->format('d/m/Y H:i') : 'Nunca' }}</td>
                    <td class="token-table-cell">
                        @if($token->expires_at && $token->expires_at->isPast())
                        <span class="badge-expirado">Expirado</span>
                        @else
                        <span class="badge-activo">Activo</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center; padding: 1.5rem 0; color:#9ca3af; font-style: italic;">
                        No hay tokens de acceso creados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MÉTRICAS CON DISEÑO NUEVO --}}
    <div class="metrics-grid">
        <div class="metric-card-new">
            <div class="icon-wrapper">📄</div>
            <div class="metric-text">
                <h3>Facturas emitidas</h3>
                <p>{{ $totalFacturas }}</p>
            </div>
        </div>

        <div class="metric-card-new">
            <div class="icon-wrapper" style="background-color:#059669;">💰</div>
            <div class="metric-text">
                <h3>Total vendido</h3>
                <p>${{ number_format($totalVentas, 2) }}</p>
            </div>
        </div>

        <div class="metric-card-new">
            <div class="icon-wrapper" style="background-color:#b91c1c;">📦</div>
            <div class="metric-text">
                <h3>Productos con bajo stock</h3>
                <p>{{ $productosBajoStock }}</p>
            </div>
        </div>

        <div class="metric-card-new">
            <div class="icon-wrapper" style="background-color:#7c3aed;">👥</div>
            <div class="metric-text">
                <h3>Usuarios registrados</h3>
                <p>{{ $totalUsuarios }}</p>
            </div>
        </div>
    </div>

    {{-- Accesos rápidos mejorados --}}
    <div class="access-grid">
        @if($roles->contains('Administrador'))
        <a href="{{ route('usuarios.index') }}" class="access-card-new">
            <div class="icon-quickaccess">👤</div>
            <span>Gestión de Usuarios</span>
        </a>
        <a href="{{ route('clientes.index') }}" class="access-card-new">
            <div class="icon-quickaccess" style="background-color:#2563eb;">🏢</div>
            <span>Gestión de Clientes</span>
        </a>
        <a href="{{ route('productos.index') }}" class="access-card-new">
            <div class="icon-quickaccess" style="background-color:#dc2626;">📦</div>
            <span>Gestión de Productos</span>
        </a>
        <a href="{{ route('facturas.index') }}" class="access-card-new">
            <div class="icon-quickaccess" style="background-color:#059669;">🧾</div>
            <span>Gestión de Facturas</span>
        </a>
        <a href="{{ route('auditoria.index') }}" class="access-card-new">
            <div class="icon-quickaccess" style="background-color:#6b7280;">🔍</div>
            <span>Auditoría</span>
        </a>
        <a href="{{ route('usuarios.papelera') }}" class="access-card-new">
            <div class="icon-quickaccess" style="background-color:#b45309;">🗑️</div>
            <span>Papelera de Usuarios</span>
        </a>
        @endif

        @if($roles->contains('Secretario'))
        <a href="{{ route('clientes.index') }}" class="access-card-new">
            <div class="icon-quickaccess" style="background-color:#059669;">🏢</div>
            <span>Clientes</span>
        </a>
        @endif

        @if($roles->contains('Secretario') || $roles->contains('Administrador'))
        <a href="{{ route('clientes.papelera') }}" class="access-card-new">
            <div class="icon-quickaccess" style="background-color:#2563eb;">🗑️</div>
            <span>Papelera de Clientes</span>
        </a>
        @endif

        @if ($roles->contains('Bodega') || $roles->contains('Administrador'))
        <a href="{{ route('productos.papelera') }}" class="access-card-new">
            <div class="icon-quickaccess" style="background-color:#b45309;">🗑️</div>
            <span>Papelera de Productos</span>
        </a>
        @endif

        @if($roles->contains('Bodega'))
        <a href="{{ route('productos.index') }}" class="access-card-new">
            <div class="icon-quickaccess" style="background-color:#b45309;">📦</div>
            <span>Productos</span>
        </a>
        @endif

        @if($roles->contains('Ventas'))
        <a href="{{ route('facturas.index') }}" class="access-card-new">
            <div class="icon-quickaccess" style="background-color:#7c3aed;">💵</div>
            <span>Facturación</span>
        </a>
        @endif

        @if($roles->contains('Administrador') || $roles->contains('Ventas'))
        <div class="chart-container" style="grid-column: 1 / -1;">
            <h3>Ventas Mensuales (últimos 6 meses)</h3>
            <canvas id="graficoVentas" height="120"></canvas>
        </div>

        <!-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const ctx = document.getElementById('graficoVentas').getContext('2d');
            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {
                        !!json_encode($ventasMensuales - > pluck('mes')) !!
                    },
                    datasets: [{
                        label: 'Ventas en USD',
                        data: {
                            !!json_encode($ventasMensuales - > pluck('total')) !!
                        },
                        backgroundColor: '#4338ca',
                        borderRadius:5,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#374151'
                            },
                            grid: {
                                color: '#e5e7eb'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#4b5563'
                            },
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            labels: {
                                color: '#374151'
                            }
                        }
                    }
                }
            });
        </script> -->
        @endif
    </div>
</div>
@endsection