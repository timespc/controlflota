# Notas de Instalación

## Assets del Proyecto Base

Para que el proyecto funcione correctamente, necesitas copiar los siguientes archivos y carpetas del proyecto base (Itdelfoscampus) a este proyecto:

### Desde `../Itdelfoscampus/public/assets/` a `public/assets/`:

1. **CSS:**
   - `cssbundle/` (todos los archivos CSS)
   - `css/` (custom.css, luno-style.css, waitMe.min.css)
   - `fonts/` (todas las fuentes)

2. **JavaScript:**
   - `js/` (plugins.js, theme.js, waitMe.min.js)
   - `js/bundle/` (todos los bundles)
   - `jquery-ui.min.js`

3. **Otros:**
   - `jquery-ui.min.css`
   - `jquery-ui.theme.min.css`

### Comandos sugeridos (desde PowerShell):

```powershell
# Copiar CSS bundles
Copy-Item -Path "..\Itdelfoscampus\public\assets\cssbundle\*" -Destination "public\assets\cssbundle\" -Recurse -Force

# Copiar CSS
Copy-Item -Path "..\Itdelfoscampus\public\assets\css\*" -Destination "public\assets\css\" -Recurse -Force

# Copiar JS
Copy-Item -Path "..\Itdelfoscampus\public\assets\js\*" -Destination "public\assets\js\" -Recurse -Force

# Copiar fuentes
Copy-Item -Path "..\Itdelfoscampus\public\assets\fonts\*" -Destination "public\assets\fonts\" -Recurse -Force

# Copiar jQuery UI
Copy-Item -Path "..\Itdelfoscampus\public\assets\jquery-ui.min.js" -Destination "public\assets\" -Force
Copy-Item -Path "..\Itdelfoscampus\public\assets\jquery-ui.min.css" -Destination "public\assets\" -Force
Copy-Item -Path "..\Itdelfoscampus\public\assets\jquery-ui.theme.min.css" -Destination "public\assets\" -Force
```

## Configuración de Base de Datos

Configurar la conexión a la base de datos en `app/Config/Database.php` cuando sea necesario.

## Instalación de Dependencias

Ejecutar:
```bash
composer install
```

## Estructura de Archivos Necesarios

Asegúrate de que existan los siguientes directorios:
- `public/assets/cssbundle/`
- `public/assets/css/`
- `public/assets/js/`
- `public/assets/js/bundle/`
- `public/assets/fonts/`
- `writable/logs/`
- `writable/session/`

## Notificaciones a admins

Las notificaciones se envían por **email** (según la configuración de cada admin) y se muestran en el sistema (campana) y como **push en el navegador** si el usuario lo permite. No está implementado el envío por WhatsApp.

### Calibraciones por vencer

Cada admin puede activar el tipo "Calibraciones por vencer" y configurar **cuántos días antes** del vencimiento quiere recibir el aviso (0 = no aviso). El proceso diario crea una notificación con la lista de calibraciones que vencen en ese plazo (o ya vencidas) y la envía por email a los admins que tienen el tipo activo y cuyo `días_aviso_vencimiento` cubre al menos una calibración de la lista.

### Cron: recordatorios y vencimientos

Para que se envíen **recordatorios** (reaviso por email si no leés una notificación) y la **notificación diaria de calibraciones por vencer**, hay que llamar periódicamente a:

```
GET /notificaciones/procesar-recordatorios
```

Opcional: en `.env` podés definir `NOTIF_RECORDATORIO_TOKEN=un_token_secreto` y llamar con `?token=un_token_secreto` para evitar que cualquiera dispare el proceso.

- **Recordatorios**: ejecutar cada X minutos (ej. cada 5 o 15), según el valor más bajo de `recordatorio_minutos` que tengan los admins.
- **Vencimientos**: se crea como máximo **una notificación por día** con la lista de calibraciones por vencer; conviene ejecutar el endpoint **una vez por día** (ej. a las 8:00).

## Migración 2 bases a 1 (go-live)

Para consolidar las bases **camiones** y **calibraciones** en **montajes_campana** (p. ej. al sacar el sistema online con datos del cliente), usar el comando unificado:

```bash
php spark migrate:full-2-to-1 [opciones]
```

### Requisitos

- En `app/Config/Database.php` deben existir las conexiones: **default** (montajes_campana), **old** (calibraciones), **camiones**.
- Las tablas de parámetros (banderas, calibradores, cubiertas, marcas, naciones, etc.) ya deben estar cargadas en montajes_campana (Fase 1 del plan).
- Las bases origen (camiones y calibraciones) conviene que sean **copias** del cliente, no producción activa.

### Opciones

| Opción | Descripción |
|--------|-------------|
| `--dry-run` | Solo ejecuta pre-vuelo y muestra qué se haría; no escribe en la BD. |
| `--truncate` | Antes de migrar, vacía las tablas: calibracion_multiflecha, calibracion_detalle, calibraciones, equipos, transportistas (y si existen calibracion_reimpresiones, calibracion_notas). Reinicia auto-increment. |
| `--backup-dir=ruta` | Si se usa con `--truncate`, intenta hacer backup (mysqldump) de montajes_campana en la carpeta indicada. Se recomienda hacer backup manual antes de truncar. |

### Orden de ejecución (dentro del comando)

1. Pre-vuelo: verifica conexiones y existencia de tablas necesarias.
2. (Opcional) Backup y truncate.
3. Dentro de una transacción: transportistas desde calibraciones → sync transportistas desde camiones → equipos desde camiones → equipos faltantes desde unidades → calibraciones, detalle y multiflecha → choferes.
4. Verificación post-migración (conteos y calibraciones sin id_equipo).
5. Si algo falla, se hace rollback y se reporta el error.

### Comandos relacionados

- `php spark equipos:importar-desde-camiones`: importa solo equipos desde camiones (inserta faltantes; usar `--update` para actualizar existentes). Requiere que los transportistas ya existan.
- `php spark migrate:olddb --only=transportistas`: migra solo transportistas desde calibraciones.
- `php spark transportistas:sync-tipo-codigo-axion-desde-camiones`: sincroniza tipo y codigo_axion desde camiones y crea transportistas faltantes por nombre.


