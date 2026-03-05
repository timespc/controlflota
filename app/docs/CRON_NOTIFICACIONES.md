# Recordatorio: Cron de notificaciones

Este archivo sirve como recordatorio de que hay que **configurar el cron** (y opcionalmente el token) para que las notificaciones funcionen correctamente.

---

## Qué hace el endpoint

La ruta **`GET /notificaciones/procesar-recordatorios`** ejecuta dos procesos:

1. **Recordatorios**: reenvía por email las notificaciones que un admin no leyó, según el `recordatorio_minutos` de cada uno (cada X minutos).
2. **Calibraciones por vencer**: crea **una notificación diaria** con la lista de calibraciones que vencen en los próximos X días (según la config de cada admin) y la envía por email.

---

## Qué hay que implementar

### 1. Cron en el servidor

Hay que programar una tarea que llame a ese endpoint de forma periódica.

- **Recordatorios**: conviene ejecutar cada **5–15 minutos** (según el valor más bajo de “recordatorio cada X minutos” que tengan los admins).
- **Vencimientos**: se genera **como máximo una notificación por día**; basta con ejecutar **una vez al día** (ej. 8:00).

**Ejemplo (una vez por día a las 8:00 y cada 15 min para recordatorios):**

```bash
# Una vez al día a las 8:00 (vencimientos + recordatorios)
0 8 * * * curl -s "https://tudominio.com/notificaciones/procesar-recordatorios"

# Cada 15 minutos (recordatorios)
*/15 * * * * curl -s "https://tudominio.com/notificaciones/procesar-recordatorios"
```

O con `wget`:

```bash
0 8 * * * wget -q -O - "https://tudominio.com/notificaciones/procesar-recordatorios"
*/15 * * * * wget -q -O - "https://tudominio.com/notificaciones/procesar-recordatorios"
```

Ajustar la URL a la base de tu proyecto (ej. `http://localhost/montajes-campana/public/notificaciones/procesar-recordatorios` en desarrollo).

### 2. Token opcional (recomendado en producción)

Para que no cualquiera pueda disparar el proceso:

1. En **`.env`** definir:
   ```env
   NOTIF_RECORDATORIO_TOKEN=un_token_largo_y_secreto
   ```
2. Llamar al endpoint con ese token:
   ```
   GET /notificaciones/procesar-recordatorios?token=un_token_largo_y_secreto
   ```
3. En el cron usar la URL con `?token=...`.

Si no se define `NOTIF_RECORDATORIO_TOKEN`, el endpoint se puede llamar sin token (útil en desarrollo).

---

## Resumen

| Pendiente                         | Descripción |
|----------------------------------|-------------|
| Configurar cron                  | Llamar a `notificaciones/procesar-recordatorios` cada 15 min y/o 1 vez al día. |
| (Opcional) `NOTIF_RECORDATORIO_TOKEN` en `.env` | Proteger el endpoint en producción. |

Cuando esto esté implementado, se puede borrar o archivar este recordatorio.
