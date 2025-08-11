<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRUEBA CON MÚLTIPLES CLIENTES ===" . PHP_EOL;

// Crear token para otro cliente (William Leon, ID: 18)
$cliente = App\Models\Cliente::find(18);

if ($cliente) {
    echo "Creando token para cliente: {$cliente->nombre}" . PHP_EOL;
    
    $tokenData = App\Models\ClienteAccessToken::createToken($cliente, 'Token_William_' . time());
    $tokenWilliam = $tokenData['plainTextToken'];
    
    echo "Token creado: {$tokenWilliam}" . PHP_EOL;
    
    // Probar API con token de William
    echo "\n--- Probando API con token de William ---" . PHP_EOL;
    $url = 'http://127.0.0.1:8000/api/facturas';
    $headers = [
        'Authorization: Bearer ' . $tokenWilliam,
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Código HTTP: {$httpCode}" . PHP_EOL;
    
    $responseData = json_decode($response, true);
    if ($responseData && isset($responseData['data']['total'])) {
        echo "Total de facturas para {$cliente->nombre}: {$responseData['data']['total']}" . PHP_EOL;
    } else {
        echo "Respuesta: {$response}" . PHP_EOL;
    }
    
} else {
    echo "Cliente con ID 18 no encontrado." . PHP_EOL;
}

// También probar con token de Abigail para comparar
echo "\n--- Probando API con token de Abigail (para comparar) ---" . PHP_EOL;
$tokenAbigail = App\Models\ClienteAccessToken::where('cliente_id', 1)->latest()->first();

if ($tokenAbigail) {
    $headers = [
        'Authorization: Bearer ' . $tokenAbigail->plain_text_token,
        'Content-Type: application/json',
        'Accept: application/json'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Código HTTP: {$httpCode}" . PHP_EOL;
    
    $responseData = json_decode($response, true);
    if ($responseData && isset($responseData['data']['total'])) {
        echo "Total de facturas para Abigail: {$responseData['data']['total']}" . PHP_EOL;
    }
}
