<?php

require 'vendor/autoload.php';

$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFICACIÓN DE TABLAS RELACIONADAS CON PERMISOS ===" . PHP_EOL;

try {
    // Consultar directamente las tablas en PostgreSQL
    $tables = \Illuminate\Support\Facades\DB::select("
        SELECT table_name 
        FROM information_schema.tables 
        WHERE table_schema = 'public' 
        AND (table_name LIKE '%role%' OR table_name LIKE '%permission%')
        ORDER BY table_name
    ");
    
    echo "Tablas existentes que contienen 'role' o 'permission':" . PHP_EOL;
    
    foreach($tables as $table) {
        echo "- {$table->table_name}" . PHP_EOL;
    }
    
    echo PHP_EOL . "Verificando si existen registros en la tabla roles:" . PHP_EOL;
    
    $rolesTableExists = \Illuminate\Support\Facades\DB::select("
        SELECT EXISTS (
            SELECT 1 
            FROM information_schema.tables 
            WHERE table_schema = 'public' 
            AND table_name = 'roles'
        ) as exists
    ");
    
    if ($rolesTableExists[0]->exists) {
        $count = \Illuminate\Support\Facades\DB::table('roles')->count();
        echo "La tabla 'roles' existe y tiene {$count} registros." . PHP_EOL;
        
        // Mostrar los roles existentes
        $roles = \Illuminate\Support\Facades\DB::table('roles')->get();
        echo "Roles existentes:" . PHP_EOL;
        foreach($roles as $role) {
            // Mostrar todas las propiedades del rol
            $properties = get_object_vars($role);
            echo "  - Rol: " . json_encode($properties) . PHP_EOL;
        }
    } else {
        echo "La tabla 'roles' NO existe." . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
