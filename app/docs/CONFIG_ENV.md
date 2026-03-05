# Configuración y entornos (.env)

Documento sobre el uso de `.env` y la configuración por entorno en el proyecto.

---

## Estado actual

- **Una sola `.env`** en la raíz del proyecto. Contiene: `CI_ENVIRONMENT`, app, base de datos (default, old, camiones), Auth (Google, ADMIN_EMAIL), opcionales (NOTIF_RECORDATORIO_TOKEN).
- **Las credenciales de BD** se leen desde `.env` en `app/Config/Database.php` (con fallback a valores por defecto). Ver `.env.example` para los nombres de variables.
- **No hay** `.env.development` ni `.env.production` versionados; es una decisión deliberada para no subir secretos al repositorio.

---

## Uso recomendado por entorno

### Desarrollo local

- Copiar `.env.example` a `.env` y completar valores (BD, Google si aplica, ADMIN_EMAIL).
- Dejar `CI_ENVIRONMENT=development`.
- El archivo `.env` no debe versionarse (debe estar en `.gitignore`).

### Staging / Producción

- **No** versionar `.env` ni archivos con secretos.
- En el servidor:
  - Crear `.env` a mano o desde un ejemplo interno, **o**
  - Usar variables de entorno del sistema y que la aplicación las lea (CodeIgniter 4 usa `env()` y ya está preparado para ello).
- En `.env` del servidor: `CI_ENVIRONMENT=production` (o `staging` si aplica).
- Ajustar `app.baseURL` a la URL pública del sitio.

### Archivos por entorno (opcional)

Si se quiere separar por entorno en máquinas de desarrollo:

- **.env.development** – valores para desarrollo local (no versionar si tiene secretos).
- **.env.production** – no versionar; solo como plantilla o recordatorio en documentación.

La aplicación por defecto solo carga `.env`; para usar `.env.development` / `.env.production` haría falta lógica adicional (por ejemplo, en `index.php` o en un bootstrap) que cargue uno u otro según `CI_ENVIRONMENT`. No es obligatorio; con una sola `.env` y `CI_ENVIRONMENT` suele bastar.

---

## Variables documentadas

Todas las variables usadas por la app están (o deberían estar) listadas en **`.env.example`** con comentarios. Al desplegar en un nuevo entorno, copiar `.env.example` a `.env` y rellenar los valores.

---

## Resumen

- **Un solo `.env`** es suficiente para la mayoría de los casos; las credenciales y la app ya leen desde `.env`.
- **Opcional:** usar archivos distintos por entorno (`.env.development`, etc.) con lógica de carga propia; no es requisito del proyecto.
- **Importante:** no subir `.env` con secretos al repositorio; sí mantener `.env.example` actualizado y documentado.
