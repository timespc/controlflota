# Unidades vs Equipos – Una sola tabla

## Situación

- **BD vieja (calibraciones):** Solo tiene tabla `unidades`. Una fila por unidad (patente del semi + patente del tractor). No tiene segunda patente de semi → problema para bitrenes (2 semis).
- **Otro sistema (pantalla frm_equipos):** Tiene tabla `equipos` con tractor + semi delantera + semi trasera (PatSemi, PatSemi2), capacidades C1–C10, modo carga, etc. Ese diseño ya resuelve la 2ª patente.
- **Nuestro sistema actual:** Tiene dos tablas: `unidades` (patente, patente_tractor, …) y `equipos` (bitren, tractor, semi_delan, semi_trasero, C1–C10, …). Unidades tiene FK opcional a equipos.

## Comparación (viejo)

| Origen   | Tabla     | Identificador | Tractor | Semi 1 | Semi 2 | Cubiertas/cotas | Capacidades cisterna |
|----------|-----------|---------------|---------|--------|--------|------------------|------------------------|
| Viejo    | unidades  | Patente (semi)| PatenteTractor | Patente (PK) | —      | Sí               | No                     |
| Viejo    | equipos   | IdEquipo      | PatTractor     | PatSemi      | PatSemi2 | No               | C1–C10, CapTotal        |

En equipos además: BiTren, IdTta, FecAlta, ModoCarga, TaraTotal, PesoMax, taras/PBT por tractor y semis.

## Decisión

**Una sola tabla en nuestro sistema: `unidades`.**  
Se incorporan en `unidades` los campos que hoy están en `equipos` (bitren, 2ª patente de semi, modo_carga, capacidades, etc.) y se deja de usar la tabla y el módulo `equipos`.

## Cambios en `unidades`

1. **Bitren y segunda patente de semi**
   - `bitren` ENUM('SI','NO') DEFAULT 'NO'
   - `patente_semi_trasero` VARCHAR(20) NULL (2ª patente cuando es bitren)

2. **Campos que vienen de equipos** (todos opcionales)
   - `modo_carga` VARCHAR(50)
   - `fecha_alta` DATE
   - `tara_total` DECIMAL(10,3)
   - `peso_maximo` DECIMAL(10,3)
   - `tractor_tara`, `tractor_pbt`
   - `semi_delan_tara`, `semi_delan_pbt` (nuestra patente = semi delantera)
   - `semi_trasero_tara`, `semi_trasero_pbt`, `semi_trasero_anio_modelo`
   - `cisterna_1_capacidad` … `cisterna_10_capacidad`, `capacidad_total`
   - `comentarios` (o reutilizar `observaciones`)

3. **Quitar**
   - Columna `id_equipo` y FK a `equipos` en `unidades`.
   - Tabla `equipos`, controlador Equipos, vistas y reportes que dependan de equipos.

## Resultado

- Una fila de `unidades` = una “unidad” con todo: patente (semi 1), patente_tractor, y si es bitren también patente_semi_trasero, más modo_carga, capacidades, taras, etc.
- La pantalla de unidades debe permitir indicar bitren SI/NO y, si es SI, cargar la segunda patente de semi y el resto de datos que hoy tiene el formulario de equipos.

## Bitrenes en el sistema viejo (2 registros = 1 equipo)

En el viejo, cuando tenían tractor + semi delantera + semi trasera no había campo para la 2ª patente, así que creaban **2 registros** en `unidades`: mismo `IdTta` y mismo `PatenteTractor`, con dos `Patente` distintas (semi1 y semi2).

**Detección:** agrupar por `(IdTta, PatenteTractor)` y contar `Patente` distintas. Si hay 2 o más → candidato a bitren.

**Script:** `php app/scripts/buscar_bitrenes_unidades_viejo.php` (lee el dump y lista los grupos). En el dump hay **261** grupos con 2+ patentes para el mismo tractor.

**Migración:** para cada grupo bitren se crea **una sola** fila en `unidades` con `bitren = SI`, `patente` = una de las patentes (ej. la primera), `patente_semi_trasero` = la otra. Las calibraciones que referencian cualquiera de las dos patentes deben apuntar al mismo `id_unidad`.
