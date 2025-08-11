# API de Facturas - Documentación

Esta API permite gestionar facturas y clientes del sistema de facturación segura.

## Autenticación

Todas las rutas requieren autenticación mediante tokens de Laravel Sanctum. Incluye el token en el header `Authorization`:

```
Authorization: Bearer {token}
```

## Endpoints Disponibles

### Base URL
```
https://tu-dominio.com/api
```

---

## 📋 CLIENTES

### 1. Listar Clientes
**GET** `/api/clientes`

Obtiene una lista paginada de todos los clientes.

**Parámetros de consulta:**
- `page` (int, opcional): Número de página (default: 1)
- `per_page` (int, opcional): Elementos por página, máximo 50 (default: 10)
- `search` (string, opcional): Buscar por nombre o email

**Ejemplo de respuesta:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "nombre": "Juan Pérez",
        "email": "juan@email.com",
        "telefono": "123456789",
        "direccion": "Calle 123",
        "created_at": "2025-07-24T10:00:00.000000Z"
      }
    ],
    "per_page": 10,
    "total": 1
  },
  "message": "Clientes obtenidos exitosamente"
}
```

### 2. Obtener Cliente Específico
**GET** `/api/clientes/{id}`

Obtiene los detalles de un cliente específico.

**Parámetros de consulta:**
- `include_facturas` (boolean, opcional): Incluir facturas del cliente

**Ejemplo de respuesta:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "nombre": "Juan Pérez",
    "email": "juan@email.com",
    "telefono": "123456789",
    "direccion": "Calle 123",
    "created_at": "2025-07-24T10:00:00.000000Z",
    "facturas_count": 5,
    "total_facturado": 750.00
  },
  "message": "Cliente obtenido exitosamente"
}
```

---

## 🧾 FACTURAS

### 3. Listar Todas las Facturas
**GET** `/api/facturas`

Obtiene una lista paginada de todas las facturas.

**Parámetros de consulta:**
- `page` (int, opcional): Número de página (default: 1)
- `per_page` (int, opcional): Elementos por página, máximo 50 (default: 10)
- `cliente_id` (int, opcional): Filtrar por ID de cliente
- `fecha_desde` (string, opcional): Filtrar desde fecha (Y-m-d)
- `fecha_hasta` (string, opcional): Filtrar hasta fecha (Y-m-d)

**Ejemplo de respuesta:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "cliente_id": 1,
        "user_id": 1,
        "total": 150.00,
        "anulada": false,
        "created_at": "2025-07-24T10:00:00.000000Z",
        "cliente": {
          "id": 1,
          "nombre": "Juan Pérez",
          "email": "juan@email.com"
        },
        "detalles": [
          {
            "id": 1,
            "producto_id": 1,
            "cantidad": 2,
            "precio_unitario": 75.00,
            "subtotal": 150.00,
            "producto": {
              "id": 1,
              "nombre": "Producto Ejemplo"
            }
          }
        ]
      }
    ]
  },
  "message": "Facturas obtenidas exitosamente"
}
```

### 4. Facturas de un Cliente
**GET** `/api/clientes/{cliente_id}/facturas`

Obtiene todas las facturas de un cliente específico.

**Parámetros de consulta:**
- `page` (int, opcional): Número de página
- `per_page` (int, opcional): Elementos por página

**Ejemplo de respuesta:**
```json
{
  "success": true,
  "data": {
    "cliente": {
      "id": 1,
      "nombre": "Juan Pérez",
      "email": "juan@email.com",
      "telefono": "123456789"
    },
    "facturas": {
      "current_page": 1,
      "data": [
        {
          "id": 1,
          "total": 150.00,
          "anulada": false,
          "created_at": "2025-07-24T10:00:00.000000Z",
          "detalles": []
        }
      ]
    }
  },
  "message": "Facturas del cliente obtenidas exitosamente"
}
```

### 5. Obtener Factura Específica
**GET** `/api/facturas/{id}`

Obtiene los detalles completos de una factura.

**Ejemplo de respuesta:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "cliente_id": 1,
    "user_id": 1,
    "total": 150.00,
    "anulada": false,
    "created_at": "2025-07-24T10:00:00.000000Z",
    "cliente": {
      "id": 1,
      "nombre": "Juan Pérez",
      "email": "juan@email.com",
      "telefono": "123456789",
      "direccion": "Calle 123"
    },
    "detalles": [
      {
        "id": 1,
        "producto_id": 1,
        "cantidad": 2,
        "precio_unitario": 75.00,
        "subtotal": 150.00,
        "producto": {
          "id": 1,
          "nombre": "Producto Ejemplo",
          "descripcion": "Descripción del producto"
        }
      }
    ],
    "usuario": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@email.com"
    }
  },
  "message": "Factura obtenida exitosamente"
}
```

### 6. Crear Nueva Factura
**POST** `/api/facturas`

Crea una nueva factura.

**Cuerpo de la petición:**
```json
{
  "cliente_id": 1,
  "productos": [
    {
      "producto_id": 1,
      "cantidad": 2
    },
    {
      "producto_id": 2,
      "cantidad": 1
    }
  ]
}
```

**Ejemplo de respuesta exitosa:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "cliente_id": 1,
    "user_id": 1,
    "total": 150.00,
    "anulada": false,
    "created_at": "2025-07-24T10:00:00.000000Z",
    "cliente": {
      "id": 1,
      "nombre": "Juan Pérez"
    },
    "detalles": []
  },
  "message": "Factura creada exitosamente"
}
```

---

## 🔧 Ejemplos de Uso

### Usando cURL

#### Obtener token (desde la interfaz web):
1. Ve a la página principal del sistema
2. Crea un token de acceso
3. Copia el token generado

#### Listar facturas de un cliente:
```bash
curl -X GET "https://tu-dominio.com/api/clientes/1/facturas" \
  -H "Authorization: Bearer tu_token_aqui" \
  -H "Accept: application/json"
```

#### Crear una factura:
```bash
curl -X POST "https://tu-dominio.com/api/facturas" \
  -H "Authorization: Bearer tu_token_aqui" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "cliente_id": 1,
    "productos": [
      {
        "producto_id": 1,
        "cantidad": 2
      }
    ]
  }'
```

### Usando JavaScript/Fetch

```javascript
// Configuración base
const API_BASE = 'https://tu-dominio.com/api';
const TOKEN = 'tu_token_aqui';

const headers = {
  'Authorization': `Bearer ${TOKEN}`,
  'Content-Type': 'application/json',
  'Accept': 'application/json'
};

// Obtener facturas de un cliente
async function obtenerFacturasCliente(clienteId) {
  const response = await fetch(`${API_BASE}/clientes/${clienteId}/facturas`, {
    method: 'GET',
    headers: headers
  });
  
  const data = await response.json();
  return data;
}

// Crear nueva factura
async function crearFactura(facturaData) {
  const response = await fetch(`${API_BASE}/facturas`, {
    method: 'POST',
    headers: headers,
    body: JSON.stringify(facturaData)
  });
  
  const data = await response.json();
  return data;
}
```

---

## ⚠️ Códigos de Error

- **200**: Éxito
- **201**: Recurso creado exitosamente
- **400**: Error en los datos enviados
- **401**: No autenticado (token inválido o faltante)
- **404**: Recurso no encontrado
- **422**: Error de validación
- **500**: Error interno del servidor

---

## 🔒 Seguridad

- Todos los endpoints requieren autenticación con token válido
- Los tokens se generan desde la interfaz web del sistema
- Máximo 50 elementos por página en las consultas paginadas
- Se registra auditoría de todas las operaciones realizadas
