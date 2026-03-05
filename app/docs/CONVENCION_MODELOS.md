# Convención de modelos (BaseModel vs métodos propios)

Breve descripción de cómo se usan los modelos en el proyecto y el rol de BaseModel.

---

## BaseModel

- **Ubicación:** `app/Models/BaseModel.php`
- **Heredado por:** Todos los modelos de la app (BanderasModel, MarcasModel, CubiertasModel, NacionModel, CalibradoresModel, EquiposModel, TransportistasModel, CalibracionModel, etc.).

Define:

- Propiedades comunes: `$table`, `$primaryKey`, `$returnType`, `$allowedFields`, etc.
- Métodos genéricos:
  - `get($id)` – buscar por id
  - `store(array $data)` – insertar o actualizar según exista `id`
  - `destroy($id)` – borrar
  - `getAll($paginate, $order_field, $order_by)` – listar
  - `countAll()` – contar

---

## Convención del proyecto

En la práctica, los **controladores** no suelen usar `get()` / `store()` / `destroy()` / `getAll()` del BaseModel. En su lugar:

- Cada modelo define métodos **propios** con nombres y lógica específicos de la entidad, por ejemplo:
  - `listarTodos()`, `obtenerPorId($id)`, `guardar($data)`, `eliminar($id)`, `total()`, etc.
- Esos métodos pueden usar distinta clave primaria (`id_bandera`, `id_tta`, etc.), validación, relaciones o reglas de negocio.

Por tanto:

- **BaseModel** se usa como **clase base** para no repetir configuración (tabla, primaryKey, returnType, timestamps, etc.).
- Los **métodos genéricos** (get, store, destroy, getAll) están disponibles pero **no son el estándar** del proyecto; el estándar son los métodos propios de cada modelo.
- No es obligatorio migrar todo a get/store/destroy; se puede seguir usando listarTodos/obtenerPorId/guardar/eliminar y, si en algún módulo conviene, usar get/store/destroy sin romper la convención.

---

## Resumen

| Aspecto | Uso en el proyecto |
|--------|---------------------|
| Herencia | Todos los modelos extienden BaseModel. |
| Configuración (tabla, primaryKey, etc.) | Heredada de BaseModel; cada modelo la sobrescribe si hace falta. |
| Listar / obtener / guardar / eliminar | Métodos propios por modelo (listarTodos, obtenerPorId, guardar, eliminar, etc.). |
| get / store / destroy / getAll | Opcionales; disponibles para uso puntual o futuras refactorizaciones. |

Este documento deja explícita la convención para mantener coherencia y para que nuevos desarrollos sigan el mismo criterio.
