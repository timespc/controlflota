# Sistema de Gestión de Montajes y Calibraciones (Control Flota)

Sistema web para la gestión de flota, transportistas, equipos, choferes y calibraciones de cisternas. Desarrollado con **CodeIgniter 4**, con autenticación por usuario/contraseña y **Google Sign-In** (Ion Auth). Incluye reportes, impresión de tarjetas e informes de carga segura, y herramientas de migración desde bases legacy.

---

## Qué incluye el sistema

### Gestión de flota
- **Transportistas**: alta, edición y listado con dirección, localidad, provincia, nación, contacto. Vista detalle con equipos asociados y documentos.
- **Equipos**: tractor + semi(s), patentes, cisternas (capacidad, tipo, cotas), checklist por tipo de trabajo (asfalto, alquitrán, etc.). Detalle por equipo con calibraciones y documentos.
- **Choferes**: por transportista, con documento, nombre, nacionalidad y comentarios.

### Calibración
- **Calibración**: carga y edición de calibraciones por equipo/patente, con calibrador, fechas, vencimiento, multiflecha, notas internas. Impresión de tarjeta de calibración e **informe de carga segura** (PDF). Reimpresiones registradas. Vista pública por token para compartir certificado sin login.

### Parámetros (catálogos)
- **Calibradores**, **Banderas**, **Cubiertas**, **Marcas**, **Marcas sensor**, **Nación** (países y provincias), **Reglas**, **Inspectores**, **Items censo**, **Tipos de cargamentos**.

### Reportes
- **Calibraciones**: listado filtrable y exportación CSV.
- **Vencimientos**: calibraciones próximas a vencer, exportación CSV.
- **Flota**: equipos con datos de transportista y calibración, exportación CSV.
- **Transportistas**: listado y exportación CSV.

### Administración (solo rol admin)
- **Usuarios**: alta por email (entrada con Google), nombre, apellido, usuario calculado (primera letra nombre + apellido), grupo (calibrador/admin), activar/desactivar, restablecer contraseña, eliminar.
- **Notificaciones**: centro de notificaciones (altas, desactivaciones, eliminaciones de usuarios; recordatorios de vencimientos). Configuración de recordatorios y notificaciones push.

### Documentos
- Subida y listado de documentos por transportista y por equipo. Visualización y eliminación.

### Autenticación y seguridad
- Login tradicional (usuario/contraseña) y **Google Sign-In** para usuarios ya dados de alta.
- Roles: **Administrador** y **Calibrador**. Cambio de contraseña obligatorio en primer acceso (opcional).
- Rutas protegidas por filtros; acceso a usuarios y notificaciones solo para admins.

---

## Tecnologías

- **Backend**: PHP 8.1+, CodeIgniter 4.5
- **Autenticación**: Ion Auth (CodeIgniter), Google Sign-In (API Client)
- **Frontend**: Bootstrap 5, jQuery, DataTables, SweetAlert2
- **PDF**: Dompdf (tarjetas, informes de carga segura)
- **Exportación**: PhpSpreadsheet (CSV)
- **Base de datos**: MySQL / MariaDB (migraciones en `app/Database/Migrations/`)

---

## Requisitos

- PHP 8.1 o superior (extensiones: gd, intl, mbstring, fileinfo recomendado)
- Composer
- MySQL o MariaDB
- Servidor web (Apache/Nginx) o entorno integrado (WAMP, XAMPP, etc.)
- Para Google Sign-In: proyecto en Google Cloud Console y credenciales OAuth 2.0

---

## Instalación

1. **Clonar e instalar dependencias**
   ```bash
   composer install
   ```

2. **Configuración**
   - Copiar `.env.example` a `.env` y ajustar:
     - `database.default.*` (host, usuario, contraseña, base de datos)
     - `app.baseURL`
     - Credenciales de Google si se usa Sign-In
   - Crear la base de datos y ejecutar migraciones:
     ```bash
     php spark migrate
     ```
   - Opcional: cargar seeds (usuarios iniciales, grupos, etc.) si existen.

3. **Permisos**
   - Directorios `writable/cache`, `writable/logs`, `writable/session`, `writable/uploads` y `writable/debugbar` deben ser escribibles por el servidor web.

4. **Documentación adicional**
   - Ver `NOTAS_INSTALACION.md` si existe en el proyecto para pasos específicos del entorno o de la migración desde bases legacy.

---

## Estructura del proyecto

```
├── app/
│   ├── Config/          # Rutas, Base de datos, Ion Auth, etc.
│   ├── Controllers/     # Dashboard, Transportistas, Equipos, Choferes, Calibración, Reportes, Usuarios, Notificaciones, Auth, etc.
│   ├── Database/
│   │   └── Migrations/  # Esquema de tablas (users, transportistas, equipos, calibraciones, etc.)
│   ├── Filters/        # CheckLogin (rol admin/calibrador)
│   ├── Libraries/      # NotificacionEnvio, etc.
│   ├── Models/
│   └── Views/
│       ├── layout/      # App, sidebar, header, scripts
│       ├── auth/        # Login, cambiar contraseña, pendiente, rechazado
│       ├── dashboard/
│       ├── transportistas/  # index, ver
│       ├── equipos/     # index, ver
│       ├── choferes/
│       ├── calibracion/ # index, imprimir, ver_publico, informe_carga_segura
│       ├── reportes/    # index, calibraciones, vencimientos, flota, transportistas
│       ├── parametros/  # placeholder (redirección)
│       ├── calibradores/, banderas, cubiertas, marcas, marcas_sensor, nacion, reglas, inspectores, items_censo, tipos_cargamentos
│       ├── usuarios/    # index (solo admin)
│       └── notificaciones/
├── app/Commands/       # Comandos CLI (migración 2 bases → 1, importación desde camiones, etc.)
├── public/             # index.php, assets, img
├── writable/           # cache, logs, session, uploads
├── tests/
├── .env.example
├── composer.json
└── spark               # CLI de CodeIgniter
```

---

## Comandos CLI (opcional)

Útiles para mantenimiento o migración desde sistemas legacy (bases *camiones* y *calibraciones*):

- **Migración unificada (go-live)**  
  `php spark migrate:full-2-to-1`  
  Con opciones `--truncate`, `--backup-dir`, `--dry-run`. Une transportistas, equipos, calibraciones, choferes e ítems censo en la base actual.

- **Solo calibraciones desde base vieja**  
  `php spark migrate:old-db --only=calibraciones`

- **Importar equipos desde camiones**  
  `php spark equipos:importar-desde-camiones`

- **Sincronizar choferes / ítems censo desde camiones**  
  `php spark choferes:desde-camiones`  
  `php spark items-censo:importar-desde-camiones`

Detalle de opciones y orden de ejecución en `NOTAS_INSTALACION.md` o en la ayuda de cada comando (`php spark help <nombre>`).

---

## Licencia

MIT (o la que indique el proyecto/organización).
