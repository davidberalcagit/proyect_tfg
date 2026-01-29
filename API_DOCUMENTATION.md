# Documentación Funcional de la API - Proyecto Coches

Esta API permite la gestión de una plataforma de compra-venta y alquiler de vehículos. Está construida sobre Laravel y utiliza autenticación mediante Laravel Sanctum.

## Autenticación

La mayoría de los endpoints de escritura requieren autenticación. Se debe enviar el token en la cabecera `Authorization`.

*   **Header:** `Authorization: Bearer <token>`

### Login
*   **Endpoint:** `POST /api/login`
*   **Descripción:** Inicia sesión y devuelve un token de acceso.
*   **Parámetros:**
    *   `email` (string, required)
    *   `password` (string, required)
*   **Respuesta Exitosa (200):**
    ```json
    {
        "token": "1|laravel_sanctum_token...",
        "user": { ... }
    }
    ```

### Logout
*   **Endpoint:** `POST /api/logout`
*   **Auth:** Requerida
*   **Descripción:** Cierra sesión y revoca el token actual.
*   **Respuesta Exitosa (200):** `{"message": "Logged out"}`

---

## Coches (Cars)

Gestión del inventario de vehículos.

### Listar Coches Disponibles
*   **Endpoint:** `GET /api/cars`
*   **Auth:** No requerida
*   **Descripción:** Obtiene una lista paginada de coches en estado "En Venta" (1).
*   **Respuesta Exitosa (200):**
    ```json
    {
        "data": [
            {
                "id": 1,
                "title": "Toyota Corolla 2020",
                "precio": 15000,
                "marca": { "nombre": "Toyota" },
                "modelo": { "nombre": "Corolla" },
                ...
            }
        ],
        "links": { ... },
        "meta": { ... }
    }
    ```

### Ver Detalle de Coche
*   **Endpoint:** `GET /api/cars/{id}`
*   **Auth:** No requerida
*   **Descripción:** Obtiene los detalles completos de un coche específico.
*   **Respuesta Exitosa (200):** Objeto coche completo con relaciones.
*   **Error (404):** Si el coche no existe.

### Crear Coche
*   **Endpoint:** `POST /api/cars`
*   **Auth:** Requerida (Rol: Vendedor)
*   **Descripción:** Publica un nuevo coche. El estado inicial será "Pendiente" (4).
*   **Parámetros:**
    *   `id_marca` (int, required)
    *   `id_modelo` (int, required)
    *   `precio` (numeric, required)
    *   `anyo_matri` (int, required)
    *   `km` (int, required)
    *   `matricula` (string, required)
    *   `id_combustible` (int, required)
    *   `id_marcha` (int, required)
    *   `id_color` (int, nullable)
    *   `id_listing_type` (int, required)
    *   `descripcion` (string, required)
    *   `image` (file, optional)
*   **Respuesta Exitosa (201):**
    ```json
    {
        "message": "Coche creado correctamente...",
        "data": { ... }
    }
    ```

### Actualizar Coche
*   **Endpoint:** `PUT /api/cars/{id}`
*   **Auth:** Requerida (Dueño del coche)
*   **Descripción:** Modifica los datos de un coche. Solo permitido si el coche está en estado "Pendiente" (4).
*   **Parámetros:** Mismos que en Crear.
*   **Respuesta Exitosa (200):** Objeto coche actualizado.
*   **Error (403):** Si no es el dueño o el coche ya está aprobado.

### Eliminar Coche
*   **Endpoint:** `DELETE /api/cars/{id}`
*   **Auth:** Requerida (Dueño del coche)
*   **Descripción:** Elimina un coche del sistema.
*   **Respuesta Exitosa (204):** Sin contenido.

### Mis Coches
*   **Endpoint:** `GET /api/my-cars`
*   **Auth:** Requerida
*   **Descripción:** Lista los coches publicados por el usuario autenticado.

---

## Ofertas (Offers)

Gestión de negociaciones entre compradores y vendedores.

### Listar Mis Ofertas
*   **Endpoint:** `GET /api/offers`
*   **Auth:** Requerida
*   **Descripción:** Muestra las ofertas realizadas por el usuario (como comprador) y las recibidas (como vendedor).
*   **Respuesta Exitosa (200):** Lista paginada de ofertas.

### Crear Oferta
*   **Endpoint:** `POST /api/offers`
*   **Auth:** Requerida (Comprador)
*   **Descripción:** Envía una oferta por un coche.
*   **Parámetros:**
    *   `id_vehiculo` (int, required)
    *   `precio_oferta` (numeric, required)
    *   `mensaje` (string, optional)
*   **Respuesta Exitosa (201):** Objeto oferta creada.
*   **Error (409):** Si ya existe una oferta pendiente para ese coche.
*   **Error (400):** Si intentas ofertar por tu propio coche.

### Ver Oferta
*   **Endpoint:** `GET /api/offers/{id}`
*   **Auth:** Requerida (Participante)
*   **Descripción:** Muestra los detalles de una oferta. Solo visible para el comprador o el vendedor.

### Actualizar Oferta
*   **Endpoint:** `PUT /api/offers/{id}`
*   **Auth:** Requerida
*   **Descripción:**
    *   **Comprador:** Puede actualizar el `precio_oferta` si está pendiente.
    *   **Vendedor:** Puede actualizar el `estado` a 'aceptada' o 'rechazada'.
*   **Parámetros:**
    *   `precio_oferta` (numeric, solo comprador)
    *   `estado` (string: 'aceptada', 'rechazada', solo vendedor)

### Eliminar Oferta
*   **Endpoint:** `DELETE /api/offers/{id}`
*   **Auth:** Requerida (Comprador)
*   **Descripción:** Cancela (elimina) una oferta pendiente. Solo el creador puede hacerlo.

---

## Ventas (Sales)

Registro de transacciones completadas.

### Listar Mis Ventas
*   **Endpoint:** `GET /api/sales`
*   **Auth:** Requerida
*   **Descripción:** Historial de ventas donde el usuario es vendedor o comprador.

### Registrar Venta
*   **Endpoint:** `POST /api/sales`
*   **Auth:** Requerida (Vendedor)
*   **Descripción:** Registra una venta manualmente (generalmente tras aceptar una oferta).
*   **Parámetros:**
    *   `id_vehiculo` (int, required)
    *   `id_comprador` (int, required)
    *   `precio` (numeric, required)
    *   `fecha` (date, required)
    *   `metodo_pago` (string, required)
    *   `estado` (int, required)
*   **Respuesta Exitosa (201):** Objeto venta creada.

---

## Tablas Auxiliares (Solo Lectura Pública)

Endpoints para obtener datos necesarios para los formularios.

### Marcas
*   **Endpoint:** `GET /api/brands`
*   **Respuesta:** Lista de todas las marcas.

### Modelos
*   **Endpoint:** `GET /api/brands/{id}/models`
*   **Respuesta:** Lista de modelos asociados a una marca específica.

### Clientes (Perfil)
*   **Endpoint:** `GET /api/customers/{id}`
*   **Auth:** Requerida
*   **Descripción:** Obtiene información pública de un perfil de vendedor/cliente.
