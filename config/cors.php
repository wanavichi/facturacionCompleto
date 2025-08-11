<?php

return [

    // Rutas a las que se aplicará CORS (API y cookie de Sanctum para SPA si aplica)
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
    ],

    // Métodos permitidos (restringir si se desea más estricto)
    'allowed_methods' => [
        'GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'
    ],

    // Orígenes permitidos. Defínelos estrictamente por variable de entorno
    // Ejemplo en .env: CORS_ALLOWED_ORIGINS=http://localhost:3000,https://frontend.mi-app.com
    'allowed_origins' => array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', 'http://localhost,http://localhost:3000,http://127.0.0.1:8000')))),

    'allowed_origins_patterns' => [],

    // Headers permitidos
    'allowed_headers' => ['*'],

    // Headers expuestos al cliente (si necesitas exponer alguno en particular)
    'exposed_headers' => [],

    // Cache de preflight
    'max_age' => 0,

    // No enviar cookies por defecto en CORS para API basada en Bearer
    'supports_credentials' => false,
];
