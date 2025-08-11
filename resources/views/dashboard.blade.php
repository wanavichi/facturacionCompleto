<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
        }

        h2 {
            color: #1f2937;
        }

        .dark h2 {
            color: #e5e7eb;
        }

        .dashboard-container {
            background-color: #ffffff;
            padding: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
        }

        .dark .dashboard-container {
            background-color: #1f2937;
            color: #f3f4f6;
        }

        .dashboard-message {
            font-size: 1.1rem;
            color: #374151;
        }

        .dark .dashboard-message {
            color: #d1d5db;
        }
    </style>

    <div class="container-fluid py-12">
        <form action="">
            {{-- listar usuarios --}}
            <label for="usuario">Seleccione un usuario</label>
            <select name="usuario" class="form-select" id="usuario" aria-label="Default select example">
                <option selected>Seleccione un usuario</option>
                @foreach ($usuarios as $usuario)
                    <option value="{{ $usuario->id }}">{{ $usuario->email }}</option>
                @endforeach
            </select>
        </form>
    </div>
</x-app-layout>