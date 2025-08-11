<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Factura #{{ $factura->id }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 20px;
            color: #1e293b;
        }

        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgb(0 0 0 / 0.1);
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #cbd5e1;
            padding-bottom: 15px;
            margin-bottom: 20px;
            align-items: center;
        }

        .logo {
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
        }

        .invoice-info {
            font-size: 14px;
            color: #64748b;
            text-align: right;
        }

        .section {
            margin-bottom: 30px;
        }

        .section h3 {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 6px;
            margin-bottom: 12px;
            color: #334155;
        }

        .cliente-info p {
            margin: 4px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        th {
            background-color: #f1f5f9;
            color: #0f172a;
        }

        .total-summary p {
            font-size: 16px;
            font-weight: 600;
            margin: 6px 0;
            text-align: right;
        }

        .total-summary p.total {
            font-size: 20px;
            color: #1e40af;
            font-weight: 700;
            margin-top: 15px;
        }

        .estado {
            font-weight: 700;
            color: #16a34a;
        }

        .estado.anulada {
            color: #dc2626;
        }

        /* Pagos section */
        .pagos-list {
            margin-top: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            max-height: 180px;
            overflow-y: auto;
        }

        .pagos-list table {
            font-size: 13px;
        }

        .pagos-list th, .pagos-list td {
            padding: 8px 6px;
        }

        .estado-pago {
            font-weight: 600;
            padding: 2px 6px;
            border-radius: 4px;
            color: white;
            display: inline-block;
            font-size: 12px;
        }

        .estado-pago.pendiente {
            background-color: #facc15; /* amarillo */
            color: #92400e;
        }

        .estado-pago.aprobado {
            background-color: #16a34a; /* verde */
        }

        .estado-pago.rechazado {
            background-color: #dc2626; /* rojo */
        }

        /* Formulario pago */
        form.pago-form {
            background: #f1f5f9;
            padding: 20px;
            border-radius: 8px;
            max-width: 500px;
            margin-top: 25px;
        }

        form.pago-form label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #334155;
        }

        form.pago-form input[type="text"],
        form.pago-form input[type="number"],
        form.pago-form select,
        form.pago-form textarea {
            width: 100%;
            padding: 8px 10px;
            margin-bottom: 15px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            font-size: 14px;
            resize: vertical;
        }

        form.pago-form textarea {
            min-height: 60px;
        }

        form.pago-form button {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 12px 18px;
            border-radius: 6px;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form.pago-form button:hover {
            background-color: #1e40af;
        }

        /* Mensajes flash */
        .alert-success {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #4ade80;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">GioFacturas Pro</div>
            <div class="invoice-info">
                Factura #{{ $factura->id }}<br />
                Fecha: {{ $factura->created_at->format('d/m/Y') }}
            </div>
        </div>

        <div class="section cliente-info">
            <h3>Datos del Cliente</h3>
            <p><strong>Nombre:</strong> {{ $factura->cliente->nombre }}</p>
            <p><strong>Email:</strong> {{ $factura->cliente->email }}</p>
        </div>

        <div class="section">
            <h3>Detalles de la Factura</h3>
            <p><strong>Total:</strong> ${{ number_format($factura->total, 2) }}</p>
            <p>
                <strong>Estado:</strong>
                @if($factura->anulada)
                    <span class="estado anulada">Anulada</span>
                @elseif($factura->pagada)
                    <span class="estado">Pagada</span>
                @else
                    <span class="estado" style="color:#ca8a04">Pendiente</span>
                @endif
            </p>
        </div>

        <div class="section">
            <h3>Productos</h3>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($factura->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto->nombre }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td>${{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total-summary">
                <p>Subtotal: ${{ number_format($factura->subtotal, 2) }}</p>
                <p>Descuento: -${{ number_format($factura->descuento, 2) }}</p>
                <p>IVA (12%): +${{ number_format($factura->iva, 2) }}</p>
                <p class="total">Total: ${{ number_format($factura->total, 2) }}</p>
            </div>
        </div>

        <div class="section">
            <h3>Pagos realizados</h3>
            @if($factura->pagos->count() > 0)
            <div class="pagos-list">
                <table>
                    <thead>
                        <tr>
                            <th>Tipo de Pago</th>
                            <th>Monto</th>
                            <th>N° Transacción</th>
                            <th>Estado</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($factura->pagos as $pago)
                        <tr>
                            <td>{{ ucfirst($pago->tipo_pago) }}</td>
                            <td>${{ number_format($pago->monto, 2) }}</td>
                            <td>{{ $pago->numero_transaccion ?? '-' }}</td>
                            <td>
                                <span class="estado-pago {{ strtolower($pago->estado) }}">
                                    {{ ucfirst($pago->estado) }}
                                </span>
                            </td>
                            <td>{{ $pago->observacion ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p>No hay pagos registrados aún.</p>
            @endif
        </div>

        @if(empty($isPdf) || !$isPdf)
    <div class="section">
        <h3>Registrar nuevo pago</h3>

        @if(session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
        @endif

        <form class="pago-form" action="{{ route('pagos.store', ['factura_id' => $factura->id]) }}" method="POST">
            @csrf

            <!-- tus campos aquí -->
            
            <button type="submit">Registrar pago</button>
        </form>
    </div>
@endif
    </div>
</body>

</html>
