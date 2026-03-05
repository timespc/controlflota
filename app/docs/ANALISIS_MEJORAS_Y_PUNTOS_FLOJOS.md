# Análisis del proyecto: mejoras y puntos flojos

Documento de análisis del sistema montajes-campana (CodeIgniter 4) para identificar qué está sólido, qué puntos quedaron flojos y qué se podría mejorar.

---

## Lo que está bien resuelto

- **Estructura clara:** Controladores por módulo, modelos con sufijo Model, vistas por recurso. Config separada (Database, Routes, Filters, Camiones, Auth).
- **Autenticación y roles:** Ion Auth integrado, filtro global CheckLogin, rutas de admin explícitas (Notificaciones, imprimir calibración, etc.). Login con Google configurado vía .env.
- **Migraciones y datos:** Scripts documentados para BD vieja, BD camiones (unidades por patente, CUIT transportistas con parecido). Documento unificado en `DATOS_Y_SCRIPTS_UNIFICADO.md`.
- **UI consistente:** SweetAlert2 en lugar de alert/confirm, mismo layout (sidebar, header), DataTables y convenciones de nombres en vistas.
- **Rutas y URLs:** Uso coherente de `site_url()` y `base_url()` en vistas.
- **Idioma:** Locale `es` por defecto; textos en español en la app.

---

## Puntos flojos y riesgos

### 1. Seguridad

| Punto | Detalle |
|-------|---------|
| **CSRF desactivado** | En `Config/Filters.php` los filtros `csrf`, `honeypot` e `invalidchars` están comentados. Cualquier POST desde el mismo origen puede ejecutarse sin token. |
| **Credenciales de BD en código** | En `app/Config/Database.php` las conexiones `default`, `old` y `camiones` tienen host, usuario, contraseña y database hardcodeados. No se usan variables de entorno (.env), lo que complica distintos entornos y no sigue buenas prácticas. |
| **Documentos sin control por recurso** | Las rutas de Documentos (listar, subir, ver, eliminar) solo exigen estar logueado (filtro global). No se comprueba que el usuario tenga permiso sobre ese transportista o esa unidad. Cualquier usuario autenticado podría intentar acceder a documentos de otros si conoce el ID. |
| **Ruta pública por token** | `calibracion/ver/(:segment)` es pública por diseño (para QR). Asegurar que el token sea impredecible y que no se filtren URLs en logs o referrers. |

### 2. Código muerto e inconsistencia de filtros

| Punto | Detalle |
|-------|---------|
| **AuthFilter no usado** | Existe `app/Filters/AuthFilter.php` (comprueba sesión y estado pendiente/rechazado) pero **no está registrado** en `Config/Filters.php`. Solo se usa CheckLogin. O se integra la lógica en CheckLogin y se elimina AuthFilter, o se registra y se unifica. |
| **Typo en ruta** | La ruta y el filtro usan `auth/google-sing-in`; lo correcto sería `google-sign-in`. |

### 3. Validación

| Punto | Detalle |
|-------|---------|
| **Reglas repetidas** | Banderas, Marcas, Cubiertas, Nacion, Calibradores, etc. repiten el mismo patrón: `required|min_length[1]|max_length[255]` (o similar) en cada controlador. No hay reglas centralizadas en `Config/Validation.php` ni reutilización por entidad. |
| **Dos estilos** | Equipos usa validación en el modelo (`$this->equiposModel->validate($data)`); el resto usa `$this->validate($rules)` en el controlador. Mezcla de enfoques. |

### 4. Respuestas JSON

| Punto | Detalle |
|-------|---------|
| **Formato no unificado** | Unos endpoints devuelven `{ success, message, data }` y otros solo `{ data }` o `{ data, error }`. Los listados para DataTables suelen ser `{ data: [...] }`; guardar/eliminar/obtener usan `success`/`message`. El frontend tiene que contemplar ambos formatos. |
| **Manejo de errores en listar** | En varios listar(), en el catch se devuelve `['data' => [], 'error' => $e->getMessage()]` pero DataTables puede no mostrar ese error al usuario de forma clara. |

### 5. Duplicación de código

| Punto | Detalle |
|-------|---------|
| **CRUD muy similar** | Banderas, Marcas, Cubiertas, Nacion, Calibradores (y en parte Reglas, MarcasSensor) comparten la misma estructura: listar (POST), obtener/(:num), guardar (POST), eliminar/(:num), total. Modelos con listarTodos, obtenerPorId, guardar*. Candidato a un controlador base o trait para reducir duplicación. |
| **BaseModel poco usado** | BaseModel define get(), store(), destroy(), getAll(), pero la mayoría de modelos implementan sus propios listarTodos/obtenerPorId/guardar*. O se usa BaseModel de forma consistente o se documenta que no es el estándar del proyecto. |

### 6. Configuración y entornos

| Punto | Detalle |
|-------|---------|
| **Una sola .env** | No hay `.env.development` / `.env.production`; un solo `.env`. Para staging/producción suele ser mejor no versionar .env y usar variables de entorno del servidor. |
| **DB no lee .env** | Aunque Auth sí usa env() para Google y ADMIN_EMAIL, Database no. En muchos proyectos las credenciales de BD se leen de .env para poder cambiar por entorno sin tocar código. |

### 7. Testing y mantenibilidad

| Punto | Detalle |
|-------|---------|
| **Sin tests de aplicación** | No hay carpeta `tests/` ni `phpunit.xml` en la raíz con tests propios. Cualquier refactor (filtros, validación, respuestas JSON) no tiene red de seguridad. |
| **Documentación de API** | No hay documento que describa los endpoints (listar, guardar, eliminar, etc.) ni el formato de request/response. Útil para frontend y para futuras integraciones. |

### 8. Frontend

| Punto | Detalle |
|-------|---------|
| **Muchas dependencias** | Layout carga jQuery, DataTables, ApexCharts, SweetAlert2, toastr, select2, jquery.validate, etc. en todas las páginas. Valorar si algunas solo se usan en módulos concretos y cargarlas bajo demanda. |
| **Accesibilidad** | Algunos elementos tienen `aria-` y `role`; no hay una política clara (contraste, foco, labels en todos los inputs, mensajes de error asociados). |
| **Estilos DataTables** | Configuración de DataTables (idioma, pageLength, etc.) repetida en varias vistas; podría centralizarse en un JS común. |

---

## Mejoras recomendadas (priorizadas)

### Prioridad alta (seguridad y consistencia)

1. **Activar CSRF** (o al menos para rutas sensibles): descomentar el filtro en `Config/Filters.php` y asegurar que los formularios y peticiones AJAX envíen el token (por header o campo).
2. **Leer credenciales de BD desde .env**: en `Database.php`, usar `env('database.default.hostname')`, etc., con fallback a los valores actuales, y documentar las variables en un `.env.example`.
3. **Documentos: control por recurso**: en DocumentosController, al listar/subir/ver/eliminar, comprobar que el transportista o la unidad existan y que el usuario tenga permiso (por ejemplo mismo id_tta o rol). Evitar que un usuario acceda a documentos de otros por ID.

### Prioridad media (limpieza y mantenibilidad)

4. **Unificar formato de respuestas JSON**: definir un estándar (ej. siempre `{ success, message?, data?, errors? }`) y un helper o trait que lo aplique en controladores. Ir migrando listar/guardar/eliminar a ese formato para que el frontend sea uniforme.
5. **Eliminar o integrar AuthFilter**: si la lógica de “pendiente/rechazado” debe aplicarse, integrarla en CheckLogin y eliminar AuthFilter; si no, borrar el archivo para no tener código muerto.
6. **Corregir typo**: renombrar `google-sing-in` a `google-sign-in` en Routes y en la exclusión del filtro CheckLogin (y en el frontend si se usa la URL).
7. **Centralizar reglas de validación**: para entidades simples (bandera, marca, cubierta, nación, calibrador), definir reglas en `Config/Validation.php` o en cada modelo y reutilizarlas desde el controlador para no repetir required|min_length|max_length en cada uno.

### Prioridad baja (opcional)

8. **Reducir duplicación CRUD**: extraer un trait o controlador base con listar/obtener/guardar/eliminar/total y que los módulos pequeños (Banderas, Marcas, etc.) lo usen con configuración (nombre de modelo, nombres de campos).
9. **Tests básicos**: añadir PHPUnit y unos pocos tests (login, una ruta protegida, un guardar de un módulo CRUD) para poder refactorizar con más seguridad.
10. **Documentación de endpoints**: un README o doc en `app/docs` con tabla de rutas (método, URL, filtro, body, respuesta) para los principales endpoints usados por el frontend.

---

## Resumen en una frase por área

- **Seguridad:** Activar CSRF, sacar credenciales a .env, y restringir acceso a documentos por recurso.
- **Código:** Quitar o usar AuthFilter, unificar JSON y validación, corregir typo `google-sing-in`.
- **Configuración:** Leer BD (y si aplica otras claves) desde .env y documentar en .env.example.
- **Mantenibilidad:** Reducir duplicación CRUD, añadir tests mínimos y documentar endpoints.

Si querés, en un siguiente paso se puede bajar esto a tareas concretas por archivo (por ejemplo: “en Database.php cambiar … por env(…)”) o priorizar solo 2–3 ítems para implementar primero.
