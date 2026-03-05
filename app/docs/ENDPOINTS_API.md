# Endpoints (API / frontend)

Documentación de los endpoints usados por el frontend (AJAX/fetch). Formato de respuesta estándar: `{ success: bool, message?: string, data?: mixed, errors?: array, id?: mixed, total?: int }`. Las peticiones POST que modifican datos requieren CSRF (header `X-CSRF-TOKEN` o campo en body). Salvo indicación, requieren sesión (filtro checkLogin).

---

## Auth

| Método | URL | Body | Respuesta | Uso |
|--------|-----|------|-----------|-----|
| GET | auth/login | — | HTML | Pantalla login |
| POST | auth/login | identity, password, remember?, csrf | Redirect / HTML | Login formulario |
| POST | auth/google-sign-in | (Google OAuth) | Redirect | Login con Google |
| GET | auth/logout | — | Redirect | Cerrar sesión |
| GET | auth/pendiente | — | HTML | Usuario pendiente de aprobación |
| GET | auth/rechazado | — | HTML | Usuario rechazado |

---

## Notificaciones (admin)

| Método | URL | Body | Respuesta | Uso |
|--------|-----|------|-----------|-----|
| GET | notificaciones | — | HTML | Listado notificaciones |
| GET | notificaciones/config | — | HTML | Configuración |
| POST | notificaciones/guardar-config | (form + csrf) | Redirect | Guardar config |
| GET | notificaciones/contar-no-leidas | — | JSON | Badge |
| GET | notificaciones/estado-push | — | JSON { count, ultima? } | Polling badge + toast |
| POST | notificaciones/marcar-leida | id_notificacion, csrf | — | Marcar leída |
| POST | notificaciones/aprobar-usuario/(:num) | — | — | Aprobar usuario |
| POST | notificaciones/rechazar-usuario/(:num) | — | — | Rechazar usuario |

---

## Transportistas

| Método | URL | Body | Respuesta | Uso |
|--------|-----|------|-----------|-----|
| GET | transportistas | — | HTML | Índice |
| GET | transportistas/ver/(:num) | — | HTML | Vista detalle + docs |
| POST | transportistas/listar | (csrf) | { success, data } | DataTable |
| GET | transportistas/obtener/(:num) | — | { success, data } | Modal editar |
| POST | transportistas/guardar | id_tta?, transportista, cuit, direccion, ... | { success, message, id } | Crear/actualizar |
| POST | transportistas/eliminar/(:num) | — | { success, message } | Eliminar |
| GET | transportistas/total | — | { success, total } | — |
| GET | transportistas/provinciasPorPais/(:num) | — | { success, data } | Select provincias |

---

## Documentos (transportista o equipo)

| Método | URL | Body | Respuesta | Uso |
|--------|-----|------|-----------|-----|
| GET | documentos/listar/transportista/(:num) | — | { success, data } | Pestaña docs (transportista) |
| GET | documentos/listar/equipo/(:num) | — | { success, data } | Pestaña docs (equipo) |
| POST | documentos/subir/transportista/(:num) | archivo (multipart), csrf | { success, message } | Subir doc transportista |
| POST | documentos/subir/equipo/(:num) | archivo (multipart), csrf | { success, message } | Subir doc equipo |
| GET | documentos/ver/(:num) | — | Archivo (stream) | Ver/descargar |
| POST | documentos/eliminar/(:num) | csrf | { success, message } | Eliminar doc |

---

## Equipos

| Método | URL | Body | Respuesta | Uso |
|--------|-----|------|-----------|-----|
| GET | equipos | — | HTML | Índice |
| GET | equipos/ver/(:num) | — | HTML | Ver equipo |
| POST | equipos/listar | (csrf) | { success, data } | DataTable |
| GET | equipos/obtener/(:num) | — | { success, data } | Modal editar (id_equipo) |
| POST | equipos/guardar | id_equipo?, id_tta, bitren, tractor_*, semi_*, cisterna_*, ... | { success, message, id } | Crear/actualizar |
| POST | equipos/eliminar/(:num) | — | { success, message } | Eliminar |
| GET | equipos/total | — | { success, total } | — |
| GET | equipos/patentes | — | { success, data } | Listado patentes |
| GET | equipos/info-patente/(:segment) | — | { success, data } | Info por patente |

---

## Banderas, Marcas, Cubiertas, Nación, Calibradores, MarcasSensor, Reglas (CRUD simple)

Patrón común (todos POST listar; GET obtener/total; POST guardar/eliminar):

| Método | URL (ej. banderas) | Body | Respuesta |
|--------|---------------------|------|-----------|
| GET | banderas | — | HTML |
| POST | banderas/listar | (csrf) | { success, data } |
| GET | banderas/obtener/(:num) | — | { success, data } |
| POST | banderas/guardar | id_bandera?, bandera | { success, message, id } |
| POST | banderas/eliminar/(:num) | — | { success, message } |
| GET | banderas/total | — | { success, total } |

Mismo esquema para: **marcas**, **cubiertas**, **nacion**, **calibradores**, **marcas-sensor**, **reglas**.  
Además: **GET calibradores/opciones** → `{ success, data }` (lista para select).

---

## Calibración

| Método | URL | Body | Respuesta | Uso |
|--------|-----|------|-----------|-----|
| GET | calibracion | — | HTML | Índice |
| POST | calibracion/listar | (csrf) | { success, data } | DataTable |
| GET | calibracion/obtener/(:num) | — | { success, data } | Modal / edición |
| GET | calibracion/ultima-por-patente | patente (query) | { success, data } | Última calibración por patente |
| POST | calibracion/guardar | (form/JSON + csrf) | { success, message, id? } | Guardar calibración |
| POST | calibracion/eliminar/(:num) | — | { success, message } | Eliminar |
| GET | calibracion/imprimir/(:num) | — | HTML/PDF | Imprimir (admin) |
| GET | calibracion/informe-carga-segura/(:num) | — | HTML | Form informe |
| POST | calibracion/guardar-informe-carga-segura/(:num) | (form + csrf) | { success, message?, redirect? } | Guardar informe |
| GET | calibracion/ver/(:segment) | — | HTML | Vista pública por token |
| GET | calibracion/multiflecha/(:num)/(:num) o (:num)/(:num)/(:num) | — | { success, data } | Datos multiflecha |
| POST | calibracion/multiflecha-guardar | (csrf + datos) | { success, message } | Guardar multiflecha |
| GET | calibracion/notas/(:num) | — | { success, notas? } | Obtener notas |
| POST | calibracion/notas-guardar | (csrf + notas) | { success } | Guardar notas |
| POST | calibracion/registrar-reimpresion | (csrf) | — | Admin |

---

## Reportes

| Método | URL | Body | Respuesta | Uso |
|--------|-----|------|-----------|-----|
| GET | reportes | — | HTML | Índice reportes |
| GET | reportes/calibraciones | — | HTML | Filtros + tabla |
| POST | reportes/listar-calibraciones | FormData (fechas, patente, precinto, id_calibrador) + csrf | { success, data } | Tabla calibraciones |
| GET | reportes/exportar-calibraciones-csv | (query desde filtros) | CSV | Descarga |
| GET | reportes/vencimientos | — | HTML | Filtros + tabla |
| POST | reportes/listar-vencimientos | dias + csrf | { success, data } | Tabla vencimientos |
| GET | reportes/exportar-vencimientos-csv | ?dias= | CSV | Descarga |
| GET | reportes/flota | — | HTML | Filtros + tabla |
| POST | reportes/listar-flota | FormData (id_tta, bitren, ...) + csrf | { success, data } | Tabla flota |
| GET | reportes/exportar-flota-csv | (query) | CSV | Descarga |
| GET | reportes/transportistas | — | HTML | Tabla transportistas |
| POST | reportes/listar-transportistas | csrf | { success, data } | Tabla |
| GET | reportes/exportar-transportistas-csv | — | CSV | Descarga |

---

## Convenciones

- **CSRF:** En POST, enviar header `X-CSRF-TOKEN` (valor del meta `csrf-token-value`) o incluir el token en el body (nombre en meta `csrf-token-name`). Para AJAX con jQuery se configura en `layout/scripts.php` (ajaxSetup).
- **JSON:** Todas las respuestas JSON de la app usan el helper `json_response()`: siempre incluyen `success`; el resto según el endpoint (message, data, errors, id, total).
- **Rutas:** Definidas en `app/Config/Routes.php`. Filtros: checkLogin (global salvo auth y calibracion/ver); admin para notificaciones e imprimir.
