@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto sm:px-6 lg:px-8 mt-10">
    <h2 class="text-3xl font-semibold text-gray-800 mb-8">Nueva Factura</h2>

    <div class="flex justify-start mb-6">
        <a href="{{ route('facturas.index') }}" 
           class="inline-flex items-center gap-2 bg-gray-600 hover:bg-gray-700 text-white font-medium py-2 px-5 rounded transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Regresar
        </a>
    </div>

    @if($errors->any())
    <div class="bg-red-100 text-red-800 p-4 rounded mb-6 flex items-center gap-3 shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
        </svg>
        <span>{{ $errors->first() ?? 'Revise los campos del formulario.' }}</span>
    </div>
    @endif

    <form method="POST" action="{{ route('facturas.store') }}" class="bg-white p-8 rounded-lg shadow-md border border-gray-200" id="factura-form">
        @csrf

        <!-- Selección de cliente -->
        <div class="mb-6">
            <label for="cliente_id" class="block mb-2 font-semibold text-gray-700">Cliente:</label>
            <select id="cliente_id" name="cliente_id" 
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
                <option value="" disabled selected>Seleccione un cliente</option>
                @foreach($clientes as $cliente)
                    <option value="{{ $cliente->id }}">{{ $cliente->nombre }} - {{ $cliente->email }}</option>
                @endforeach
            </select>
        </div>

        <!-- Productos -->
        <div>
            <h3 class="text-xl font-semibold mb-4 text-gray-800">Productos:</h3>
            <div class="overflow-x-auto border rounded-md border-gray-300">
                <table class="min-w-full text-sm table-auto">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left font-medium text-gray-600">Producto</th>
                            <th class="px-4 py-3 text-center font-medium text-gray-600">Cantidad</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600">Precio</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-600">Subtotal</th>
                            <th class="px-4 py-3 font-medium text-gray-600 text-center">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="productos-container" class="divide-y divide-gray-200">
                        <!-- Filas dinámicas -->
                    </tbody>
                </table>
            </div>

            <button type="button" onclick="agregarProducto()" 
                class="mt-4 inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Agregar Producto
            </button>
        </div>

        <!-- Total -->
        <div class="mt-6 text-right text-2xl font-bold text-gray-900">
            Total: $<span id="total">0.00</span>
        </div>
        <input type="hidden" name="total" id="total_input">

        <!-- Botón guardar -->
        <div class="mt-8 text-right">
            <button type="submit" 
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg transition">
                Guardar Factura
            </button>
        </div>
    </form>
</div>

<script>
    const productosData = @json($productos);

    function agregarProducto() {
        const container = document.getElementById('productos-container');
        const index = container.children.length;

        const row = document.createElement('tr');
        row.classList.add('bg-white', 'hover:bg-gray-50', 'transition');

        row.innerHTML = `
            <td class="px-4 py-3">
                <select name="productos[${index}][producto_id]" 
                        class="w-full border border-gray-300 rounded-md px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                        onchange="actualizarPrecio(this, ${index})" required>
                    <option value="" disabled selected>Seleccione</option>
                    ${productosData.map(p => `<option value="${p.id}" data-precio="${p.precio}">${p.nombre}</option>`).join('')}
                </select>
            </td>
            <td class="px-4 py-3 text-center">
                <input type="number" name="productos[${index}][cantidad]" min="1" value="1" 
                       class="w-20 border border-gray-300 rounded-md px-2 py-1 text-center focus:outline-none focus:ring-2 focus:ring-blue-500" 
                       onchange="recalcularSubtotal(${index})" required>
            </td>
            <td class="px-4 py-3 text-right">
                <input type="text" id="precio-${index}" 
                       class="w-24 border border-gray-300 rounded-md px-2 py-1 bg-gray-100 text-right" disabled>
            </td>
            <td class="px-4 py-3 text-right">
                <input type="text" name="productos[${index}][subtotal]" id="subtotal-${index}" 
                       class="w-28 border border-gray-300 rounded-md px-2 py-1 bg-gray-100 text-right" readonly>
            </td>
            <td class="px-4 py-3 text-center">
                <button type="button" onclick="eliminarFila(this)" 
                        class="text-red-600 hover:text-red-800 font-semibold transition">Eliminar</button>
            </td>
        `;
        container.appendChild(row);
    }

    function actualizarPrecio(select, index) {
        const precio = select.options[select.selectedIndex]?.getAttribute('data-precio') || 0;
        document.getElementById(`precio-${index}`).value = parseFloat(precio).toFixed(2);
        recalcularSubtotal(index);
    }

    function recalcularSubtotal(index) {
        const cantidad = parseFloat(document.querySelector(`[name="productos[${index}][cantidad]"]`)?.value) || 0;
        const precio = parseFloat(document.getElementById(`precio-${index}`)?.value) || 0;
        const subtotal = cantidad * precio;
        document.getElementById(`subtotal-${index}`).value = subtotal.toFixed(2);
        recalcularTotal();
    }

    function recalcularTotal() {
        let total = 0;
        const subtotales = document.querySelectorAll('[id^="subtotal-"]');
        subtotales.forEach(input => total += parseFloat(input.value) || 0);
        document.getElementById('total').textContent = total.toFixed(2);
        document.getElementById('total_input').value = total.toFixed(2);
    }

    function eliminarFila(button) {
        button.closest('tr').remove();
        recalcularTotal();
    }
</script>
@endsection
