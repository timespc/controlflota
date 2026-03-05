# Guía unificada: datos y scripts de migración / cruce con otras bases

Este documento reúne **todos** los scripts y pasos para obtener datos desde bases externas (sistema viejo, BD camiones) y comandos auxiliares. Los archivos específicos (ej. `MIGRACION_BD_VIEJA.md`) siguen existiendo para detalle por tema.

---

## Índice rápido

| Origen / tema | Comando principal | Conexión | Config |
|---------------|-------------------|----------|--------|
| BD vieja (calibraciones) | `migrate:olddb` | `old` | — |
| BD camiones → unidades (equipos por patente) | `equipos:completar-desde-camiones` | `camiones` | `Camiones.php` |
| BD camiones → transportistas (CUIT, parecido nombres) | `transportistas:cuit-desde-camiones` | `camiones` | `Camiones.php` |
| Tokens públicos (post-migración) | `calibracion:generar-tokens` | — | — |
| Marcar recalibradas | `calibracion:marcar-recalibradas` | — | — |

---

## 1. Base de datos del sistema viejo (`calibraciones`)

### Qué hace
Migra datos desde la BD legacy **calibraciones** a la BD actual **montajes_campana**. Incluye: banderas, calibradores, cubiertas, marcas, naciones, transportistas, unidades, calibraciones, detalle y multiflecha.

### Requisitos
- Migraciones del sistema actual ejecutadas: `php spark migrate`
- BD vieja accesible (servidor MySQL con BD `calibraciones` o dump importado).
- En **`app/Config/Database.php`** debe existir el grupo **`$old`** con host, usuario, contraseña y `database` = `calibraciones` (o el nombre de tu BD vieja).

### Dump disponible
En la raíz del proyecto: **`montajes-campana-db-sistema-viejo.sql`**. Si no tenés el servidor viejo, creá una BD (ej. `calibraciones`) e importá ese archivo.

### Comandos

```bash
# Simulación (solo conteos, no escribe)
php spark migrate:olddb --dry-run

# Migración real
php spark migrate:olddb

# Vaciar tablas destino y volver a insertar (re-ejecutar desde cero)
php spark migrate:olddb --truncate
```

### Orden de migración (respetando FKs)
1. banderas → 2. calibradores → 3. cubiertas → 4. marcas → 5. naciones → 6. transportistas → 7. unidades → 8. calibraciones → 9. calibracion_detalle → 10. calibracion_multiflecha.

### Opciones
| Opción | Descripción |
|--------|-------------|
| `--dry-run` | No escribe; solo muestra conteos por paso. |
| `--truncate` | Vacía tablas destino antes de insertar (orden seguro para FKs). |

### Después de migrar
- **Tokens públicos (QR / URL):** las calibraciones migradas no tienen `token_publico`. Para generarlos:  
  `php spark calibracion:generar-tokens` (opcional: `--dry-run`).
- Detalle de provincias, países, unidades, bitrenes, etc.: ver **`app/docs/MIGRACION_BD_VIEJA.md`**.

---

## 2. Base de datos camiones (equipos y ttas)

Se usa para **completar unidades** con datos de la tabla equipos (por patente) y para **sincronizar CUIT/email de transportistas** desde la tabla ttas (por nombre, con opción de parecido).

### Requisitos
- En **`app/Config/Database.php`** debe existir el grupo **`$camiones`** (host, usuario, contraseña, `database` = nombre de la BD camiones).
- Configuración de tablas y columnas en **`app/Config/Camiones.php`** (ver más abajo).

---

### 2.1 Llevar data de equipos → unidades (unificar por patente)

**Comando:** `equipos:completar-desde-camiones`  
**Archivo:** `app/Commands/CompletarEquiposDesdeCamiones.php`

- **Qué hace:** Lee la tabla **equipos** de la BD camiones y **completa** las unidades de montajes-campana con: fecha_alta, modo_carga, tara_total, peso_maximo, taras/PBT tractor y semis, cisternas C1–C10, capacidad_total. Opcionalmente compara nación (equipo vs transportista).
- **Emparejamiento:** Por **patente tractor + patente semi** (sin exigir `id_tta`). Con `--con-tta` también exige que coincida `id_tta`.

**Uso:**
```bash
php spark equipos:completar-desde-camiones
php spark equipos:completar-desde-camiones --dry-run
php spark equipos:completar-desde-camiones --verbose
php spark equipos:completar-desde-camiones --con-tta
```

**Opciones:**
| Opción | Descripción |
|--------|-------------|
| `--dry-run` | Solo mostrar qué se haría, sin actualizar unidades. |
| `--verbose` | Mostrar muestras de claves (equipos vs unidades) para diagnosticar. |
| `--con-tta` | Exigir también `id_tta` para el match (por defecto solo patente tractor + semi). |

**Config en `app/Config/Camiones.php`:**
- `tableEquipos`: nombre de la tabla de equipos (default `equipos`).
- `columnMap`: mapeo nombre interno → nombre de columna en la BD camiones (ej. `id_tta` → `IdTta`, `tractor_patente` → `PatTractor`, etc.).
- `excludeColumns`: columnas que no existen en camiones y no se incluyen en el SELECT (ej. `nacion`).

---

### 2.2 Transportistas: CUIT y parecido por nombre

**Comando:** `transportistas:cuit-desde-camiones`  
**Archivo:** `app/Commands/CuitTransportistasDesdeCamiones.php`

- **Qué hace:** Sincroniza **CUIT** (y opcionalmente **email**) de los transportistas de montajes-campana con la tabla **ttas** de la BD camiones.
- **Match:** Primero por **nombre normalizado** (trim, mayúsculas, espacios colapsados). Si no hay match exacto, puede usar **parecido** con `similar_text` (porcentaje de similitud).

**Uso:**
```bash
php spark transportistas:cuit-desde-camiones
php spark transportistas:cuit-desde-camiones --dry-run
php spark transportistas:cuit-desde-camiones --verbose
# Listar transportistas sin match que se parecen a algún tta (con %)
php spark transportistas:cuit-desde-camiones --similares --umbral=50 --max=100
# Usar mejor parecido ≥ 90% para actualizar CUIT/email cuando no hay match exacto
php spark transportistas:cuit-desde-camiones --usar-similares --umbral=90
```

**Opciones:**
| Opción | Descripción |
|--------|-------------|
| `--dry-run` | Solo mostrar qué se actualizaría, sin modificar la BD. |
| `--verbose` | Listar cada transportista y si hubo match o no. |
| `--similares` | Listar transportistas sin match que se parecen a algún tta (umbral y cantidad con `--umbral` y `--max`). |
| `--usar-similares` | Si no hay match exacto, usar el mejor parecido con similitud ≥ 90% para actualizar CUIT y email. |
| `--umbral=N` | Porcentaje mínimo de similitud para `--similares` y `--usar-similares` (default 50). |
| `--umbral-max=N` | Con `--similares`: solo listar si similitud ≤ N (ej. rango 30–60%). |
| `--max=N` | Máximo de filas a listar con `--similares` (default 50). |

**Config en `app/Config/Camiones.php`:**
- `tableTtas`: tabla de transportistas en camiones (default `ttas`).
- `columnTtasNombre`: columna con nombre/razón social.
- `columnTtasCuit`: columna con CUIT.
- `columnTtasEmail`: columna con email (vacío = no sincronizar email).

---

## 3. Comandos auxiliares (sin otra BD)

### 3.1 Generar tokens públicos para calibraciones

**Comando:** `calibracion:generar-tokens`  
**Archivo:** `app/Commands/CalibracionGenerarTokens.php`

Asigna `token_publico` a todas las calibraciones que no lo tengan (para URL pública y QR). Útil después de migrar desde la BD vieja.

```bash
php spark calibracion:generar-tokens
php spark calibracion:generar-tokens --dry-run
```

---

### 3.2 Marcar recalibradas

**Comando:** `calibracion:marcar-recalibradas`  
**Archivo:** `app/Commands/CalibracionMarcarRecalibradas.php`

Marca como recalibradas las calibraciones vencidas que ya tienen una calibración posterior vigente para la misma patente (`id_calibracion_reemplazo`). Útil después de añadir el campo o migrar datos.

```bash
php spark calibracion:marcar-recalibradas
php spark calibracion:marcar-recalibradas --dry-run
```

---

## 4. Resumen de conexiones en `app/Config/Database.php`

| Grupo | Uso | Database por defecto |
|-------|-----|------------------------|
| `default` | Sistema actual (montajes_campana) | — |
| `old` | Migración desde sistema viejo | `calibraciones` |
| `camiones` | Completar unidades y CUIT transportistas | `camiones` |

Ajustar en cada grupo: `hostname`, `username`, `password`, `database` según el entorno.

---

## 5. Documentos adicionales (detalle por tema)

- **`app/docs/MIGRACION_BD_VIEJA.md`** – Detalle completo de la migración desde la BD vieja (orden, notas de provincias/países/unidades, re-ejecución, errores frecuentes).

---

## 6. Checklist rápido (no olvidarse de nada)

- [ ] **Migración BD vieja:** `php spark migrate` → configurar `$old` → `php spark migrate:olddb` (opcional `--dry-run` primero).
- [ ] **Dump BD vieja:** Si no hay servidor viejo, importar `montajes-campana-db-sistema-viejo.sql` en una BD y apuntar `$old` a ella.
- [ ] **Tokens después de migrar:** `php spark calibracion:generar-tokens`.
- [ ] **Recalibradas (opcional):** `php spark calibracion:marcar-recalibradas`.
- [ ] **BD camiones:** Configurar `$camiones` y `app/Config/Camiones.php` (tablas/columnas).
- [ ] **Unidades desde equipos (por patente):** `php spark equipos:completar-desde-camiones` (opcional `--dry-run` / `--verbose`).
- [ ] **CUIT transportistas desde camiones:** `php spark transportistas:cuit-desde-camiones`; si hay muchos sin match, usar `--similares` para revisar y luego `--usar-similares` si corresponde.
