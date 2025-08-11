<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Iniciar Sesión</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-indigo-50 to-white text-gray-800 h-screen flex items-center justify-center">

    <div class="w-full max-w-md p-8 bg-white rounded-2xl shadow-lg border border-gray-200">
        <!-- Logo y encabezado -->
        <div class="text-center mb-8">
            <!-- Icono ubicación (pin) -->
            <svg xmlns="http://www.w3.org/2000/svg" 
                class="mx-auto h-14 w-14 text-indigo-600 mb-4" 
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" >
                <path stroke-linecap="round" stroke-linejoin="round" 
                    d="M12 11c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2z" />
                <path stroke-linecap="round" stroke-linejoin="round" 
                    d="M12 21s8-4.5 8-10a8 8 0 10-16 0c0 5.5 8 10 8 10z" />
            </svg>
            <h2 class="text-3xl font-extrabold text-indigo-700">Bienvenido a GeoFacturas</h2>
            <p class="text-gray-500 mt-2">Por favor, inicia sesión en tu cuenta</p>
        </div>
 
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
        <!-- Formulario -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-6">
                <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Correo Electrónico</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autofocus
                    placeholder="tucorreo@ejemplo.com"
                    class="w-full px-5 py-3 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                />
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Contraseña</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    placeholder="********"
                    class="w-full px-5 py-3 rounded-lg border border-gray-300 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
                />
            </div>

            <!-- Remember + Forgot -->
            <div class="flex items-center justify-between mb-8 text-sm">
                <label class="flex items-center space-x-2 text-gray-700 select-none cursor-pointer">
                    <input
                        type="checkbox"
                        name="remember"
                        class="form-checkbox h-5 w-5 text-indigo-600 rounded transition duration-150 ease-in-out"
                    />
                    <span>Recordarme</span>
                </label>
                <a href="#" class="text-indigo-600 hover:text-indigo-800 font-medium">¿Olvidaste tu contraseña?</a>
            </div>

            <!-- Botón -->
            <button
                type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg shadow-md transition-colors"
            >
                Iniciar sesión
            </button>
        </form>
    </div>

</body>
</html>
