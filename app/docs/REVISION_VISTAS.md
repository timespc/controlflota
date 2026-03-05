# Revisión de todas las vistas

Revisión realizada sobre las vistas del proyecto para CSRF, AJAX (fetch/jQuery) y consistencia.

---

## Layouts

| Layout | Meta CSRF | Incluye scripts.php (ajaxSetup) | Uso |
|--------|-----------|----------------------------------|-----|
| **layout/app** | Sí (`csrf-token-name`, `csrf-token-value`) | Sí | Todas las vistas de la app con sidebar/header |
| **layout/print** | No | No | Solo `calibracion/imprimir_informe_carga_segura` (HTML para imprimir, sin JS POST) |
| **Standalone HTML** | No (o propio) | No | `auth/login`, `auth/pendiente`, `auth/rechazado`, `calibracion/ver_publico`, errores |

---

## Vistas por módulo

### Layout app (tienen meta CSRF + ajaxSetup)

| Vista | AJAX / fetch | CSRF | Notas |
|-------|--------------|------|--------|
| **reportes/calibraciones** | `fetch` POST listar-calibraciones | OK: body (csrf/csrfVal) + headers X-CSRF-TOKEN, X-Requested-With | Revisado |
| **reportes/vencimientos** | `fetch` POST listar-vencimientos | OK: body + headers | Revisado |
| **reportes/flota** | `fetch` POST listar-flota | OK: body + headers | Revisado |
| **reportes/transportistas** | `fetch` POST listar-transportistas | OK: body + headers | Revisado |
| **reportes/index** | No AJAX | N/A | Solo enlaces |
| **equipos/ver** | `fetch` GET listar docs; POST subir/eliminar docs | OK: POST con headers (meta csrf-token-value + X-Requested-With) | Revisado |
| **transportistas/ver** | `fetch` GET listar docs; POST subir/eliminar docs | OK: POST con headers | Revisado |
| **transportistas/index** | `$.get` listar docs; `$.ajax` subir, provinciasPorPais, obtener, guardar, eliminar; `$.post` eliminar doc | OK: ajaxSetup aplica X-CSRF-TOKEN y X-Requested-With a POST | Revisado |
| **equipos/index** | `$.ajax` obtener, eliminar, guardar | OK: ajaxSetup | Revisado |
| **banderas/index** | `$.ajax` obtener (GET), guardar (POST), eliminar (POST) | OK: ajaxSetup para POST | Revisado |
| **calibracion/index** | `$.getJSON` varios; `$.ajax` multiflecha-guardar, registrar-reimpresion, equipos/guardar, notas-guardar, guardar; `$.post` eliminar | OK: formulario tiene input CSRF; algunos ajax añaden X-CSRF-TOKEN desde `#form-calibracion` | Revisado |
| **calibracion/informe_carga_segura** | `$.ajax` POST con `$form.serialize()` | OK: formulario tiene `csrf_field()`; ajaxSetup añade header | Revisado |
| **calibracion/imprimir** | Extiende app | N/A o según contenido | Revisar si tiene JS POST |
| **marcas_sensor/index** | `$.ajax` obtener (GET), guardar (POST), eliminar (POST) | OK: ajaxSetup | Revisado |
| **reglas/index** | `$.ajax` guardar (POST) | OK: ajaxSetup | Revisado |
| **calibradores/index** | `$.ajax` obtener (GET), guardar (POST), eliminar (POST) | OK: ajaxSetup | Revisado |
| **cubiertas/index** | `$.ajax` obtener (GET), guardar (POST), eliminar (POST) | OK: ajaxSetup | Revisado |
| **marcas/index** | `$.ajax` obtener (GET), guardar (POST), eliminar (POST) | OK: ajaxSetup | Revisado |
| **nacion/index** | `$.ajax` obtener (GET), guardar (POST), eliminar (POST) | OK: ajaxSetup | Revisado |
| **notificaciones/index** | `$.post` marcar-leida | OK: token en body (input hidden) + ajaxSetup header | Revisado |
| **notificaciones/config** | Form POST tradicional a guardar-config | OK: `csrf_field()` en formulario | Revisado |
| **dashboard/index** | Puede tener widgets/AJAX | Depende: si hay POST vía jQuery, ajaxSetup aplica | Revisado (sin POST en revisión) |

### Layout print (sin meta CSRF ni scripts)

| Vista | AJAX | Notas |
|-------|------|--------|
| **calibracion/imprimir_informe_carga_segura** | No | Solo HTML para imprimir; no hay POST desde JS |

### Vistas standalone (sin layout app)

| Vista | POST / CSRF | Notas |
|------|-------------|--------|
| **auth/login** | Form POST con `csrf_field()` | OK; no AJAX |
| **auth/pendiente** | Solo enlace logout (GET) | Sin POST desde la página |
| **auth/rechazado** | Solo enlace logout (GET) | Sin POST desde la página |
| **calibracion/ver_publico** | Solo lectura por token en URL | Sin formularios POST |
| **errors/html/production** | Página de error | Sin AJAX |
| **errors/html/error_404** | Página de error | Sin AJAX |
| **errors/html/error_exception** | Página de error | Sin AJAX |
| **errors/cli/** | Salida CLI | N/A |

### Layout parciales (incluidos por app)

| Vista | Notas |
|-------|--------|
| **layout/header** | Incluye badge notificaciones; el polling es `$.get` (GET) en scripts.php |
| **layout/sidebar** | Menú estático |
| **layout/style** | CSS |
| **layout/scripts** | ajaxSetup (X-CSRF-TOKEN para POST, X-Requested-With para todas las peticiones jQuery); polling notificaciones |

---

## Resumen de cambios aplicados en esta revisión

1. **layout/scripts.php**  
   - Se añade el header `X-Requested-With: XMLHttpRequest` en **todas** las peticiones jQuery (no solo POST), para consistencia con las vistas que usan `fetch()` con ese header.

2. **Vistas con `fetch()`**  
   - **Reportes** (calibraciones, vencimientos, flota, transportistas): ya enviaban CSRF en body; se añadieron headers `X-CSRF-TOKEN` y `X-Requested-With` en los `fetch()` POST.  
   - **Transportistas/ver** y **Equipos/ver**: subida y eliminación de documentos ya corregidas previamente (headers CSRF + X-Requested-With).  
   - Los GET con `fetch()` (listar documentos) no requieren CSRF.

3. **Vistas con jQuery**  
   - Todas las que extienden `layout/app` tienen meta CSRF y cargan `scripts.php`, por lo que `$.ajax` y `$.post` reciben automáticamente `X-CSRF-TOKEN` (en POST) y `X-Requested-With`.  
   - Notificaciones además envía el token en el body (input hidden).  
   - Calibración e informe_carga_segura tienen además `csrf_field()` en formularios; el envío por serialización incluye el token.

---

## Checklist rápido para nuevas vistas

- Si la vista extiende **layout/app**: ya tiene meta CSRF y ajaxSetup; cualquier `$.ajax`/`$.post` POST lleva token y X-Requested-With.
- Si usás **fetch()** para POST: añadir headers `X-Requested-With: XMLHttpRequest` y `X-CSRF-TOKEN` (valor del meta `csrf-token-value` o de `csrf_hash()`).
- Si la vista es **standalone** y tiene formulario POST: usar `csrf_field()` o equivalente y, si hay AJAX POST, enviar el token (body o header) manualmente.
