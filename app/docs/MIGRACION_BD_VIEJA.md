# Migración de la base de datos del sistema viejo

Este documento describe cómo ejecutar la migración de datos desde la base de datos del sistema legacy (`calibraciones`) a la base del sistema actual (`montajes_campana`).

## Cómo migrar (resumen)

1. **Crear el esquema del sistema nuevo** (si aún no está):
   ```bash
   php spark migrate
   ```

2. **Tener la BD vieja accesible**  
   - Si tenés el servidor viejo con MySQL: la BD `calibraciones` debe existir y ser accesible.  
   - Si solo tenés el dump (`montajes-campana-db-sistema-viejo.sql`): importalo en MySQL (creá una BD, ej. `calibraciones`, e importá el .sql).

3. **Configurar la conexión `old`** en `app/Config/Database.php` (grupo `$old`): host, usuario, contraseña y nombre de la BD vieja (`calibraciones` por defecto).

4. **Ejecutar la migración de datos**:
   ```bash
   # Primero probar sin escribir (solo conteos)
   php spark migrate:olddb --dry-run

   # Migración real
   php spark migrate:olddb
   ```

5. **(Opcional)** Generar tokens públicos para calibraciones migradas:
   ```bash
   php spark calibracion:generar-tokens
   ```

## Requisitos

1. **Migraciones del sistema actual ejecutadas**  
   La base de datos destino debe tener todas las tablas creadas por las migraciones de CodeIgniter 4:
   ```bash
   php spark migrate
   ```

2. **Base de datos vieja accesible**  
   La BD del sistema viejo (por defecto `calibraciones`) debe existir y ser accesible con las mismas credenciales que la BD actual (host, usuario, contraseña). Si solo tenés el dump, importalo en MySQL antes. La conexión se configura en `app/Config/Database.php` bajo el grupo `old`.

3. **Conexión `old` en Database.php**  
   En `app/Config/Database.php` debe existir el array `$old` con la configuración de la BD vieja (mismo host/user/pass que `default`, `database` = `calibraciones` por defecto). Ajustar si tu BD vieja tiene otro nombre o credenciales.

## Uso

Desde la raíz del proyecto:

```bash
# Migración normal (inserta en la BD nueva sin vaciarla)
php spark migrate:olddb

# Solo simulación: muestra cuántos registros se migrarían por tabla, sin escribir
php spark migrate:olddb --dry-run

# Vaciar tablas destino antes de insertar (útil para re-ejecutar la migración desde cero)
php spark migrate:olddb --truncate
```

## Orden de migración

El comando ejecuta los pasos en este orden (respetando dependencias por claves foráneas):

1. **banderas** – Catálogo de banderas (IdBandera → id_bandera, Bandera → bandera).
2. **calibradores** – Personas que calibran.
3. **cubiertas** – Catálogo de medidas de cubiertas.
4. **marcas** – Marcas de unidades.
5. **naciones** – Tabla `nacion` del viejo → tabla `naciones` del actual.
6. **transportistas** – Se mapea IdNacion a pais_id (por nombre en `paises`) y Pcia a provincia_id (por nombre en `pais_provincias`, con normalización para provincias argentinas).
7. **unidades** – Patente sigue siendo dato; se genera id_unidad nuevo. Tipo: 1→ACOPLADO, 2→CHASIS, 3→SEMI. Cubiertas por eje se copian por ID.
8. **calibraciones** – Cabecera: CodCalib → id_calibracion, Patente → patente, id_unidad se resuelve por patente. Multiflecha 0/1 → NO/SI.
9. **calibracion_detalle** – Solo desde `cisternas` (una fila por cisterna; mflec=NO).
10. **calibracion_multiflecha** – Desde `cisternas_multi` (compartimientos multiflecha por cisterna): id_calibracion, numero_linea (= Cisterna), numero_multiflecha (= NroMultiflecha), capacidad, enrase, precintos, etc.

## Opciones

| Opción      | Descripción |
|------------|-------------|
| `--dry-run` | No escribe en la BD nueva; solo muestra conteos por paso. |
| `--truncate` | Antes de insertar en cada grupo, vacía las tablas destino afectadas (en orden seguro para FKs). Útil para repetir la migración sobre una BD limpia. |

## Notas

- **Provincias:** Los transportistas del viejo usan `Pcia` (texto, ej. "BUENOS AIRES"). Se mapea a `provincia_id` buscando en `pais_provincias` por nombre normalizado (ej. "RIO NEGRO" → "Río Negro"). "C.A.B.A." se asimila a Buenos Aires. Si no hay coincidencia, `provincia_id` queda NULL y se conserva el texto en `provincia`.
- **Países:** IdNacion del viejo se mapea a `pais_id` del nuevo por nombre de país (nacion.Nacion vs paises.nombre), con normalización de mayúsculas y nombres (ej. PERU → Perú).
- **Unidades:** En el sistema nuevo hay una sola tabla `unidades` con bitren y patente_semi_trasero (2ª patente de semi cuando es bitren). No se migra la tabla vieja `equipos`; toda la info queda en unidades. La BD vieja usa Patente como PK; la nueva usa id_unidad (autoincrement). Se mantiene patente como dato y se construye un mapa patente → id_unidad para rellenar calibraciones.id_unidad.
- **Detalle de calibración:** `cisternas` → `calibracion_detalle` (una fila por cisterna). `cisternas_multi` → `calibracion_multiflecha` (compartimientos multiflecha por cisterna).

## Tokens públicos (QR / URL pública) después de migrar

Las calibraciones migradas no tienen `token_publico` (la URL pública es `calibracion/ver/{token}` y se usa para el QR). Para asignar un token a todas las que no lo tengan:

```bash
php spark calibracion:generar-tokens
```

Opcional: `--dry-run` para ver cuántas se actualizarían sin modificar la BD.

## Re-ejecución

La migración usa **INSERT IGNORE** para las tablas que insertan con PK explícita (banderas, calibradores, cubiertas, marcas, naciones, transportistas, calibraciones, calibracion_detalle). Si una fila con la misma clave primaria ya existe, se ignora y no se produce error. Así podés volver a ejecutar `php spark migrate:olddb` sin fallar por duplicados (solo se insertarán los registros que aún no existan). Para reemplazar todo desde cero, usá `--truncate`.

## Errores frecuentes

- **Error conectando a las bases de datos:** Revisar que la BD `calibraciones` exista y que el grupo `old` en `Database.php` tenga host/user/password correctos.
- **Duplicate entry:** Con el cambio a INSERT IGNORE ya no debería aparecer; si la BD nueva ya tiene datos, las filas existentes se ignoran. Para rehacer la migración por completo, usá `--truncate`.
