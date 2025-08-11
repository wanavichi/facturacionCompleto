<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PRUEBA DE API CON NUEVO TOKEN ===" . PHP_EOL;

// Obtener el último token creado
$ultimoToken = App\Models\ClienteAccessToken::latest()->first();

if ($ultimoToken) {
    echo "Usando token: {$ultimoToken->plain_text_token}" . PHP_EOL;
    echo "Cliente: {$ultimoToken->cliente->nombre}" . PHP_EOL;
    
    // Hacer petición a la API
    $url = 'http://127.0.0.1:8000/api/facturas';
    $headers = [
        'Authorization: Bearer ' . $ultimoToken->plain_text_token,
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
    echo "Respuesta: " . $response . PHP_EOL;
} else {
    echo "No hay tokens disponibles para probar." . PHP_EOL;
}
