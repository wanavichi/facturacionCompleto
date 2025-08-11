@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto sm:px-6 lg:px-8 mt-8">
    <h2 class="text-3xl font-semibold text-gray-800 mb-6">Pagos Pendientes</h2>

    {{-- Mensajes --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-6 shadow-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 text-red-800 p-4 rounded mb-6 shadow-sm flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @forelse($pagos as $pago)
        <div class="bg-white shadow-md rounded-lg p-6 mb-6 border border-gray-200">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <p class="mb-1"><strong class="text-gray-700">Factura:</strong> #{{ $pago->factura_id }}</p>
                    <p class="mb-1"><strong class="text-gray-700">Cliente:</strong> {{ $pago->pagador->name ?? 'N/A' }}</p>
                    <p class="mb-1"><strong class="text-gray-700">Monto:</strong> ${{ number_format($pago->monto, 2) }}</p>
                </div>
                <div>
                    <p class="mb-1"><strong class="text-gray-700">Tipo de Pago:</strong> {{ ucfirst($pago->tipo_pago) }}</p>
                    <p class="mb-1"><strong class="text-gray-700">Transacción:</strong> {{ $pago->numero_transaccion }}</p>
                    <p class="mb-1"><strong class="text-gray-700">Observación:</strong> {{ $pago->observacion ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="mt-4 flex gap-3">
                <form action="{{ route('pagos.approve', $pago->id) }}" method="POST">
                    @csrf
                    <button class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        Aprobar
                    </button>
                </form>

                <form action="{{ route('pagos.reject', $pago->id) }}" method="POST">
                    @csrf
                    <button class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Rechazar
                    </button>
                </form>
            </div>
        </div>
    @empty
        <div class="bg-yellow-100 text-yellow-800 p-4 rounded shadow-sm">
            No hay pagos pendientes en este momento.
        </div>
    @endforelse

    {{-- Paginación --}}
    <div class="mt-6">
        {{ $pagos->links() }}
    </div>
</div>
@endsection
