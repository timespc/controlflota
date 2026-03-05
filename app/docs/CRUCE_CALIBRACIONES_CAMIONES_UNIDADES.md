# Cruce calibraciones + camiones para completar campos de Unidades

Varios campos de Unidades (Fecha Alta, Modo Carga, Tara Total, Peso Máximo, taras/PBT, cisternas, etc.) se pueden completar desde la BD **camiones** (tabla equipos) porque:

- **Calibraciones** (BD vieja): la tabla `unidades` solo tiene Patente (semi), PatenteTractor, IdTta, Tara (tara del semi), cubiertas, cotas, etc. **No tiene** FechaAlta, ModoCarga, TaraTotal, PesoMax, taras por eje, cisternas ni Nación a nivel de unidad.
- Esos campos están en la BD **camiones** (tabla equipos): FecAlta, ModoCarga, TaraTotal, PesoMax, TaraTractor, PBTTractor, TaraSemi, PatSemi2, TaraSemi2, PBTSemiTras, C1–C10, CapTotal, etc.

Este documento detalla el cruce y la **implementación** disponible.

---

## 1. Qué campos faltan y de dónde salen

| Campo en Unidades / Reporte | En calibraciones | En camiones (equipos) | Nota |
|-----------------------------|------------------|------------------------|------|
| **Fecha Alta**              | No existe        | Sí (FecAlta)           | Solo desde camiones. |
| **Modo Carga**              | No existe        | Sí (ModoCarga)         | Solo desde camiones. |
| **Nación**                  | No existe a nivel unidad | Sí en equipos; además en nuestro sistema está en **transportistas** | Se puede llenar **sin camiones** desde `transportistas.nacion`. |
| **Tara Total / Peso Máximo** | No existe     | Sí (TaraTotal, PesoMax) | Solo desde camiones. |
| **Taras y PBT (tractor, semi, semi trasero)** | No existe | Sí (TaraTractor, PBTTractor, TaraSemi, TaraSemi2, PBTSemiTras) | Solo desde camiones. |
| **Cisternas C1–C10, Capacidad total** | No existe | Sí (C1–C10, CapTotal) | Solo desde camiones. |

**Resumen:**  
- **Nación:** se completa desde `transportistas.nacion` en el reporte.  
- **Demás campos:** el comando `equipos:completar-desde-camiones` empareja **por patente tractor + patente semi** (sin exigir id_tta) y copia desde equipos a unidades.

---

## 2. Criterio de cruce (calibraciones ↔ camiones)

Para que una fila de **nuestra** `unidades` corresponda a una fila de **equipos** en camiones, hace falta un criterio de emparejamiento.

- **Nuestras unidades** (origen: calibraciones) tienen: `id_tta`, `patente_tractor`, `patente` (semi delantera), y si es bitren `patente_semi_trasero`.
- **Equipos en camiones** suelen tener: IdTta (transportista), PatTractor (tractor), PatSemi (semi delantera), y si es bitren PatSemi2 (semi trasera).

**Clave de cruce (por defecto):**

- Emparejar solo por **patente tractor + patente semi (delantera)**:
  - `unidades.patente_tractor` ≈ `equipos.PatTractor`
  - `unidades.patente` ≈ `equipos.PatSemi`
- Así se obtiene buena data aunque los **IdTta** no coincidan entre sistemas. Opción `--con-tta` para exigir también `id_tta` = `IdTta`.

**Normalización:** patentes sin espacios y en mayúsculas al comparar.

**Bitrenes:**  
En nuestro sistema un bitren es **una** fila en `unidades` con `patente` (semi delantera) y `patente_semi_trasero`. En camiones puede ser una fila de equipos con PatSemi y PatSemi2. El cruce sigue siendo por **semi delantera**:  
`equipos.PatSemi` = `unidades.patente`, y además `equipos.PatTractor` = `unidades.patente_tractor`, `equipos.IdTta` = `unidades.id_tta`. No hace falta usar PatSemi2 para el match; con eso ya queda un único equipo por unidad.

---

## 3. Origen de los datos de camiones

Para poder cruzar hace falta **acceso a la información de equipos del sistema camiones**:

- **Opción A – Base de datos camiones accesible:**  
  Otra conexión MySQL (u otro motor) que apunte a la BD de camiones, con una tabla tipo `equipos` (o el nombre real) que tenga al menos: IdTta, PatTractor, PatSemi (y si aplica PatSemi2), FecAlta, ModoCarga, TaraTotal, PesoMax, Transporte, Nación (o nombre de país).

- **Opción B – Exportación (CSV / SQL):**  
  Que te exporten desde camiones la tabla de equipos (mismas columnas útiles). Luego se puede importar a una tabla temporal en nuestra BD o leer el CSV en un script y hacer el cruce por IdTta + patentes normalizadas.

En ambos casos hay que conocer el **nombre exacto de tablas y columnas** en camiones (IdTta, PatTractor, PatSemi, FecAlta, ModoCarga, TaraTotal, PesoMax, Transporte, Nación o equivalentes).

---

## 4. Formas de implementar el cruce (cuando se decida)

### 4.1 Completar solo Nación (sin camiones)

- En el **reporte de flota** (y donde se listen unidades): hacer JOIN de `unidades` con `transportistas` y usar `transportistas.nacion` (o el nombre del país desde `paises` si se guarda por `pais_id`).
- Así la columna **Nación** se llena con el país del transportista, sin tocar la base camiones.

### 4.2 Cruce con camiones para el resto de campos

**Opción 1 – Script único (recomendable para un solo llenado):**

1. Conectar a la BD camiones (o leer CSV/export de equipos).
2. Cargar todos los equipos en memoria (o en tabla temporal) con patentes normalizadas.
3. Recorrer nuestras `unidades`; para cada una, buscar un equipo donde:
   - `id_tta` = IdTta del equipo,
   - patente normalizada del tractor coincida,
   - patente normalizada de la semi delantera (`unidades.patente`) coincida con PatSemi del equipo.
4. Si hay match: hacer `UPDATE unidades SET modo_carga = ..., fecha_alta = ..., tara_total = ..., peso_maximo = ..., transporte = ... WHERE id_unidad = ?`.
5. Dejar sin tocar las unidades sin match (esos campos siguen NULL/vacíos).

**Opción 2 – Conexión “camiones” en el proyecto:**

- En `app/Config/Database.php` definir un grupo `camiones` que apunte a la BD de camiones.
- Crear un comando (ej. `php spark equipos:completar-desde-camiones`) o un paso extra en la migración que:
  - Lea de la tabla de equipos en la conexión `camiones`,
  - Para cada fila de nuestra `unidades`, busque la fila de equipos con el criterio anterior (id_tta + patentes normalizadas),
  - Actualice en nuestra BD los campos transporte, fecha_alta, modo_carga, tara_total, peso_maximo.

**Consideraciones:**

- **Normalización de patentes:** aplicar la misma función en ambos lados (quitar espacios, pasar a mayúsculas, etc.) para evitar falsos “no match” por formato.
- **Unidades sin equipo en camiones:** no actualizar; dejar NULL/vacío. No inventar datos.
- **Varios equipos para la misma clave:** definir criterio (ej. el más reciente por fecha, o el primero) y usar solo uno para no duplicar datos.

---

## 5. Implementación

### Comando `equipos:completar-desde-camiones`

- **Uso:** `php spark equipos:completar-desde-camiones` (o `--dry-run` para simular).
- **Requisitos:** Conexión `camiones` en `app/Config/Database.php` apuntando a la BD de camiones. Tabla de equipos con columnas equivalentes a: id_tta, tractor_patente, semi_delan_patente, fecha_alta, modo_carga, tara_total, peso_maximo, transporte, nacion.
- **Cruce:** Por `id_tta` + patente normalizada del tractor + patente normalizada de la semi delantera. Se actualizan en `unidades`: transporte, fecha_alta, modo_carga, tara_total, peso_maximo.
- **Nación:** No se escribe en `unidades`. El reporte de flota muestra la nación del **transportista** (JOIN con transportistas). Antes de confiar en esa info, el comando **compara** la nación del equipo en camiones con la nación del transportista en nuestro sistema; si difieren, lo escribe en consola (amarillo) para revisión. Se mantiene siempre la nación del transportista en el reporte.

### Configuración opcional

- **`app/Config/Camiones.php`:** Si la tabla de equipos en camiones usa otros nombres de columna (ej. IdTta, PatTractor, PatSemi), definilos en `columnMap`, ej. `['id_tta' => 'IdTta', 'tractor_patente' => 'PatTractor', 'semi_delan_patente' => 'PatSemi', ...]`. También podés cambiar `tableEquipos` si la tabla tiene otro nombre.

### Reporte de flota

- **Nación:** Se obtiene del transportista (unidades → transportistas.nacion).
- **Transporte, Fecha alta, Modo carga, Tara total, Peso máx.:** Se leen de la tabla `unidades` (completados por el comando desde camiones).

### Columna `transporte` en unidades

- Se añadió con la migración `AddTransporteToUnidades`. El comando la llena desde camiones.

---

## 6. Resumen

- Las columnas **Transporte**, **Fecha Alta**, **Modo Carga**, **Tara Total** y **Peso Máximo** se completan con el comando que cruza con la BD **camiones** (tabla equipos).
- **Nación** se muestra desde **transportistas** en el reporte; el comando compara nación camiones vs transportista y avisa si difieren.
- **Cruce:** por `id_tta` + patentes normalizadas (tractor + semi delantera).
- **Uso:** configurar conexión `camiones`, opcionalmente `Config\Camiones` para nombres de columnas, y ejecutar `php spark equipos:completar-desde-camiones`.
