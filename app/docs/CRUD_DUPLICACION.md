# Reducción de duplicación CRUD

Resumen de qué controladores usan `CrudBaseController` y cuáles mantienen lógica propia, y por qué.

---

## Controladores que usan CrudBaseController

Estos módulos extienden **CrudBaseController** y definen solo propiedades de configuración (`crudModelClass`, `crudPrimaryKey`, `crudFieldName`, etc.) y, si aplica, `index()` y/o `guardar()` o hooks (`prepararDataForGuardar`, `getCrudValidationRules`). **listar**, **obtener**, **eliminar** y **total** vienen de la base.

| Controlador | PK | Notas |
|-------------|-----|--------|
| Banderas | id_bandera | Solo index() propio. |
| Marcas | id_marca | Solo index() propio. |
| Cubiertas | id_cubierta | Solo index() propio. |
| Nacion | id_nacion | Solo index() propio. |
| Calibradores | id_calibrador | Solo index() propio; opciones() para select. |
| MarcasSensor | id_marca_sensor | Solo index() propio. |
| Reglas | id_regla | index(), getCrudValidationRules(), prepararDataForGuardar() (habilitada, id_usuario_creacion). |
| Equipos | id_equipo | index() con transportistas/paises; guardar() propio (muchos campos, cisternas). |
| **Transportistas** | **id_tta** | index() con paises; ver(); guardar() propio (país/provincia); provinciasPorPais(); listar/obtener/eliminar/total desde base. |

---

## Controladores que no usan CrudBaseController

| Controlador | Motivo |
|-------------|--------|
| **Calibracion** | Flujo propio: listar, obtener, guardar, eliminar, imprimir, informe carga segura, multiflecha, notas, ultimaPorPatente, ver público por token. No es un CRUD simple. |
| Documentos, Auth, Dashboard, Reportes, Notificaciones | No son CRUD de entidades maestras; lógica específica por módulo. |

---

## Formato de respuesta JSON (estándar)

Todos los endpoints CRUD (base y propios) usan el helper **json_response()**:

- **listar:** `{ success, data }`
- **obtener:** `{ success, data? }` o `{ success, message }` si no encontrado
- **guardar:** `{ success, message, id? }` o `{ success, message?, errors? }` si validación falla
- **eliminar:** `{ success, message }`
- **total:** `{ success, total }` o `{ success, total, message }` en error

Definición: `app/Helpers/json_response_helper.php`. Uso en controladores: `$this->response->setJSON(json_response(true, ['data' => $lista]));`

---

## Resumen

- **CrudBaseController** centraliza listar, obtener, eliminar y total para entidades con un solo campo principal o con guardar() sobrescrito (Equipos, Transportistas).
- **Transportistas** y **Equipos** extienden la base y solo implementan index(), ver() (Transportistas) y guardar() con lógica propia.
- **Calibracion** sigue en BaseController por listar/obtener/guardar con filtros, relaciones o flujos que no encajan en la base sin muchos overrides.
