<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICACIÓN DEL SISTEMA DE TOKENS ===" . PHP_EOL;

// Verificar clientes
$clientes = App\Models\Cliente::all();
echo "Clientes disponibles: " . $clientes->count() . PHP_EOL;
foreach($clientes as $cliente) {
    echo "- ID: {$cliente->id}, Nombre: {$cliente->nombre}, Email: {$cliente->email}" . PHP_EOL;
}

echo PHP_EOL;

// Verificar tokens en nueva tabla
$tokens = App\Models\ClienteAccessToken::all();
echo "Tokens en nueva tabla de clientes: " . $tokens->count() . PHP_EOL;
foreach($tokens as $token) {
    echo "- Token: {$token->name}, Cliente ID: {$token->cliente_id}, Creado: {$token->created_at}" . PHP_EOL;
}

echo PHP_EOL;

// Crear un token de prueba si hay clientes
if ($clientes->count() > 0) {
    $cliente = $clientes->first();
    echo "Creando token de prueba para cliente: {$cliente->nombre}" . PHP_EOL;
    
    $tokenData = App\Models\ClienteAccessToken::createToken($cliente, 'Token_Prueba_' . time());
    
    echo "Token creado exitosamente:" . PHP_EOL;
    echo "- Nombre: {$tokenData['token']->name}" . PHP_EOL;
    echo "- Token: {$tokenData['plainTextToken']}" . PHP_EOL;
    echo "- Cliente: {$cliente->nombre}" . PHP_EOL;
} else {
    echo "No hay clientes disponibles para crear tokens." . PHP_EOL;
}
