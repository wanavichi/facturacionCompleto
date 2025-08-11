<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Verificar estructura de la tabla
    echo "=== ESTRUCTURA DE LA TABLA personal_access_tokens ===\n";
    $columns = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'personal_access_tokens' ORDER BY ordinal_position");
    foreach ($columns as $column) {
        echo "- {$column->column_name} ({$column->data_type})\n";
    }
    
    echo "\n=== ÚLTIMOS 3 TOKENS EN LA BASE DE DATOS ===\n";
    $tokens = DB::table('personal_access_tokens')
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get(['id', 'name', 'token', 'plain_text_token', 'created_at']);
    
    foreach ($tokens as $token) {
        echo "ID: {$token->id}\n";
        echo "Nombre: {$token->name}\n";
        echo "Token Hash: " . substr($token->token, 0, 20) . "...\n";
        echo "Token Original: " . ($token->plain_text_token ? substr($token->plain_text_token, 0, 20) . "..." : "NULL") . "\n";
        echo "Creado: {$token->created_at}\n";
        echo "---\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
