@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-8">
    <h2 class="text-3xl font-semibold text-gray-800 mb-6">Listado de Facturas</h2>

    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('dashboard') }}"
            class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-5 rounded transition">
            {{-- Icono regresar --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Regresar
        </a>

        <a href="{{ route('facturas.create') }}"
            class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded transition">
            Nueva Factura
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-6 shadow-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 text-red-800 p-4 rounded mb-6 shadow-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ $errors->first('msg') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="w-full table-auto text-sm text-gray-700">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left font-medium">ID</th>
                    <th class="px-6 py-3 text-left font-medium">Cliente</th>
                    <th class="px-6 py-3 text-left font-medium">Total</th>
                    <th class="px-6 py-3 text-left font-medium">Estado</th>
                    <th class="px-6 py-3 text-left font-medium">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($facturas as $factura)
                <tr class="border-b last:border-none {{ $factura->anulada ? 'bg-gray-50 text-gray-400' : '' }}">
                    <td class="px-6 py-4">{{ $factura->id }}</td>
                    <td class="px-6 py-4">{{ $factura->cliente->nombre }}</td>
                    <td class="px-6 py-4">${{ number_format($factura->total, 2) }}</td>
                    <td class="px-6 py-4">
                        @if($factura->anulada)
                            <span class="text-red-600 font-semibold">Anulada</span>
                        @elseif($factura->pagada)
                            <span class="text-green-600 font-semibold">Pagada</span>
                        @elseif($factura->estado === 'pendiente')
                            <span class="text-yellow-600 font-semibold">Pendiente</span>
                        @else
                            <span class="text-gray-600 font-semibold">{{ ucfirst($factura->estado) }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 space-x-4 whitespace-nowrap">
                        {{-- Botón ver factura --}}
                        <button onclick="openModal('verFactura-{{ $factura->id }}')" class="text-blue-600 hover:text-blue-800 font-semibold transition">Ver</button>

                        {{-- Botón pagos --}}
                        @if(!$factura->anulada)
                            <button onclick="openModal('pagosFactura-{{ $factura->id }}')" class="text-indigo-600 hover:text-indigo-800 font-semibold transition">
                                Pagos
                            </button>
                        @endif

                        {{-- Botón anular --}}
                        @if(!$factura->anulada && ($factura->user_id == $usuarioId || $roles->contains('Administrador')))
                            <button onclick="openModal('anularFactura-{{ $factura->id }}')" class="text-red-600 hover:text-red-800 font-semibold transition">Anular</button>
                        @endif

                        {{-- Botón PDF --}}
                        <a href="{{ route('facturas.pdf', $factura) }}" class="text-indigo-600 hover:text-indigo-800 font-semibold transition">PDF</a>

                        {{-- Botón notificar --}}
                        @if($factura->cliente->email_verified_at)
                            <button onclick="openModal('notificarFactura-{{ $factura->id }}')" class="text-yellow-600 hover:text-yellow-800 font-semibold transition">Notificar</button>
                        @else
                            <span class="text-gray-400 italic">Correo no verificado</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modales --}}
    @foreach($facturas as $factura)
        {{-- Modal Ver Factura --}}
        <div id="verFactura-{{ $factura->id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-3xl max-h-[80vh] overflow-y-auto">
                <h3 class="text-xl font-bold mb-4 text-gray-800">Factura #{{ $factura->id }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm text-gray-700 mb-4">
                    <div>
                        <p class="mb-1"><strong>Cliente:</strong> {{ $factura->cliente->nombre }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $factura->cliente->email }}</p>
                        <p class="mb-1"><strong>Fecha:</strong> {{ $factura->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="mb-1"><strong>Subtotal:</strong> ${{ number_format($factura->subtotal, 2) }}</p>
                        <p class="mb-1"><strong>Descuento:</strong> -${{ number_format($factura->descuento, 2) }}</p>
                        <p class="mb-1"><strong>IVA (12%):</strong> ${{ number_format($factura->iva, 2) }}</p>
                        <p class="mb-1"><strong>Total:</strong> ${{ number_format($factura->total, 2) }}</p>
                        <p class="mb-1"><strong>Estado:</strong>
                            @if($factura->anulada)
                                <span class="text-red-600 font-semibold">Anulada</span>
                            @elseif($factura->pagada)
                                <span class="text-green-600 font-semibold">Pagada</span>
                            @else
                                <span class="text-yellow-600 font-semibold">Pendiente</span>
                            @endif
                        </p>
                    </div>
                </div>

                <h4 class="text-lg font-semibold text-gray-800 mt-4 mb-2">Detalles</h4>
                @if($factura->detalles && $factura->detalles->count())
                    <table class="w-full text-sm text-gray-700 mb-4">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-3 py-2 text-left">Producto</th>
                                <th class="px-3 py-2 text-right">Cantidad</th>
                                <th class="px-3 py-2 text-right">Precio</th>
                                <th class="px-3 py-2 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($factura->detalles as $d)
                                <tr class="border-b last:border-none">
                                    <td class="px-3 py-2">{{ $d->producto->nombre ?? 'N/A' }}</td>
                                    <td class="px-3 py-2 text-right">{{ $d->cantidad }}</td>
                                    <td class="px-3 py-2 text-right">${{ number_format($d->precio_unitario, 2) }}</td>
                                    <td class="px-3 py-2 text-right">${{ number_format($d->subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500">No hay detalles registrados.</p>
                @endif

                <div class="mt-4 text-right">
                    <button onclick="closeModal('verFactura-{{ $factura->id }}')" class="px-5 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded transition">Cerrar</button>
                </div>
            </div>
        </div>

        {{-- Modal Anular Factura --}}
        <div id="anularFactura-{{ $factura->id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
                <h3 class="text-xl font-bold mb-4 text-gray-800">Confirmar anulación</h3>
                <p class="text-gray-700 mb-6">¿Seguro que deseas anular la factura #{{ $factura->id }}? Esta acción no se puede deshacer.</p>
                <div class="flex justify-end gap-3">
                    <button onclick="closeModal('anularFactura-{{ $factura->id }}')" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded">Cancelar</button>
                    <form action="{{ route('facturas.anular', $factura) }}" method="POST">
                        @csrf
                        <button class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded">Anular</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Notificar Factura --}}
        <div id="notificarFactura-{{ $factura->id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
                <h3 class="text-xl font-bold mb-4 text-gray-800">Enviar notificación</h3>
                @if($factura->cliente->email_verified_at)
                    <p class="text-gray-700 mb-6">Se enviará la factura #{{ $factura->id }} al correo del cliente: <strong>{{ $factura->cliente->email }}</strong>.</p>
                    <div class="flex justify-end gap-3">
                        <button onclick="closeModal('notificarFactura-{{ $factura->id }}')" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded">Cancelar</button>
                        <form action="{{ route('facturas.notificar', $factura) }}" method="POST">
                            @csrf
                            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded">Enviar</button>
                        </form>
                    </div>
                @else
                    <p class="text-gray-600">El cliente no tiene su correo verificado, no es posible enviar notificación.</p>
                    <div class="mt-6 text-right">
                        <button onclick="closeModal('notificarFactura-{{ $factura->id }}')" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded">Cerrar</button>
                    </div>
                @endif
            </div>
        </div>

        {{-- Modal de pagos --}}
        <div id="pagosFactura-{{ $factura->id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-2xl max-h-[80vh] overflow-y-auto">
                <h3 class="text-xl font-bold mb-4 text-gray-800">Pagos de la Factura #{{ $factura->id }}</h3>
                @if($factura->pagos->count())
                    <table class="w-full text-sm text-gray-700 mb-4">
                        <thead class="bg-gray-100 border-b">
                            <tr>
                                <th class="px-3 py-2">Tipo</th>
                                <th class="px-3 py-2">Monto</th>
                                <th class="px-3 py-2">Estado</th>
                                <th class="px-3 py-2">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($factura->pagos as $pago)
                                <tr class="border-b last:border-none">
                                    <td class="px-3 py-2">{{ ucfirst($pago->tipo_pago) }}</td>
                                    <td class="px-3 py-2">${{ number_format($pago->monto, 2) }}</td>
                                    <td class="px-3 py-2">
                                        @if($pago->estado === 'aprobado')
                                            <span class="text-green-600 font-semibold">Aprobado</span>
                                        @elseif($pago->estado === 'rechazado')
                                            <span class="text-red-600 font-semibold">Rechazado</span>
                                        @else
                                            <span class="text-yellow-600 font-semibold">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        @if($roles->contains('Pagos'))
                                            @if($pago->estado === 'pendiente')
                                                <form action="{{ route('pagos.approve', $pago->id) }}" method="POST" class="inline">@csrf
                                                    <button class="text-green-600 hover:text-green-800">Aprobar</button>
                                                </form>
                                                <form action="{{ route('pagos.reject', $pago->id) }}" method="POST" class="inline">@csrf
                                                    <button class="text-red-600 hover:text-red-800">Rechazar</button>
                                                </form>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500">No hay pagos registrados para esta factura.</p>
                @endif
                <div class="mt-4 text-right">
                    <button onclick="closeModal('pagosFactura-{{ $factura->id }}')" class="px-5 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded transition">Cerrar</button>
                </div>
            </div>
        </div>
    @endforeach
</div>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
@endsection
