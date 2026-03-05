# Análisis: criterio “mismo día” para detectar bitrenes en la migración

Documento de respaldo del análisis realizado para validar que el criterio **mismo día** (fecha de `UltActualiz`) filtra correctamente los bitrenes al migrar desde la BD vieja, evitando falsos positivos.

---

## Contexto

En la BD vieja (`calibraciones`, tabla `unidades`) no existía campo para una segunda patente de semi. Cuando había un **bitren** (tractor + semi delantera + semi trasera), se creaban **2 registros** en `unidades`:

- Mismo `IdTta` y mismo `PatenteTractor`
- Dos `Patente` distintas (semi1 y semi2)

Para migrar al sistema nuevo (una sola fila por unidad con `bitren = SI` y `patente_semi_trasero`) hay que **detectar esos pares** y fusionarlos en una sola fila.

---

## Criterio inicial (sin fecha)

Agrupar por `(IdTta, PatenteTractor)` y considerar bitren si hay **2 o más** `Patente` distintas.

- **Resultado en el dump:** **261** grupos con 2+ patentes para el mismo tractor.

Problema: un mismo tractor puede haber tenido **varios semis a lo largo del tiempo** (cambio de semi, no bitren). Esos casos aparecen como “2+ patentes” pero no son un bitren real.

---

## Criterio refinado: mismo día

Solo considerar bitren cuando los 2 (o más) registros con distintas patentes están cargados **el mismo día**, usando la fecha de `UltActualiz` (solo la fecha, no la hora).

- **Clave de agrupación:** `(IdTta, PatenteTractor, fecha(UltActualiz))`
- Si un grupo tiene 2+ `Patente` distintas **y** misma fecha → **bitren** → se migra como una sola unidad con `bitren = SI` y `patente_semi_trasero`.

**Resultado en el dump:**

| Criterio | Cantidad |
|----------|----------|
| Candidatos a bitren **sin** filtrar por día (IdTta + PatenteTractor) | **261** |
| Candidatos a bitren **con** mismo día (IdTta + PatenteTractor + fecha) | **29** |
| **Falsos positivos filtrados** (mismo tractor, 2+ semis pero en días distintos) | **232** |

Es decir: **232** casos son tractores que en algún momento tuvieron 2+ semis distintos pero **registrados en fechas distintas** (cambio de semi en el tiempo), no un bitren con dos semis al mismo tiempo. El criterio “mismo día” los excluye correctamente.

---

## Comprobación: misma semana

Para estar seguros, se revisó cuántos de esos **232 falsos positivos** tenían las fechas de carga **en la misma semana** (por si algún bitren se hubiera cargado en días distintos de la misma semana).

- **Resultado:** solo **1** caso tiene las dos cargas en la misma semana.

---

## Detalle del único caso “misma semana”

Ese caso **no** es un bitren real, sino un **duplicado por diferencia de espaciado** en la patente:

| Campo | Valor |
|-------|--------|
| **IdTta** | 935 |
| **PatenteTractor** | 6243 IRC |

| Carga | UltActualiz | Patente(s) semi |
|-------|-------------|-----------------|
| 1 | 2024-04-22 | 6243 IRC |
| 2 | 2024-04-24 | 6243  IRC *(doble espacio)* |

Son **dos registros del mismo semi** (6243 IRC), uno con "6243 IRC" y otro con "6243  IRC". No es un bitren; es dato duplicado/inconsistente en el viejo. El criterio “mismo día” hace bien en no unificarlo como bitren.

---

## Conclusión

- El criterio **mismo día** (`IdTta` + `PatenteTractor` + `fecha(UltActualiz)`) está filtrando bien:
  - **29** bitrenes detectados y migrados como una sola unidad.
  - **232** falsos positivos excluidos (mismo tractor, distintos semis en distintos días).
  - El único caso “misma semana” es un duplicado por typo/espaciado, no un bitren.

Si en el futuro se quisiera unificar también duplicados por patente normalizada (como 6243 IRC / 6243  IRC), podría hacerse en un paso aparte de limpieza o migración, no como bitren.

---

## Referencias

- **Script de análisis:** `app/scripts/buscar_bitrenes_unidades_viejo.php`  
  - Uso: `php app/scripts/buscar_bitrenes_unidades_viejo.php`  
  - Lee el dump `montajes-campana-db-sistema-viejo.sql`, agrupa por (IdTta, PatenteTractor) y por (IdTta, PatenteTractor, fecha), y muestra conteos + listado de bitrenes y del caso “misma semana”.
- **Migración:** `app/Commands/MigrateOldDb.php` → método `migrateUnidades()` aplica la clave por día para detectar bitrenes y fusionar en una sola fila.
- **Documentación relacionada:** `app/docs/UNIDADES_VS_EQUIPOS.md` (decisión una sola tabla, bitrenes en el viejo).
