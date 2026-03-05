# Puntos flojos opcionales — hecho

Resumen del estado de los puntos flojos opcionales (frontend, config, BaseModel) y enlaces a la documentación.

---

## Estado

| Punto | Estado | Documentación / Cambios |
|-------|--------|--------------------------|
| **Frontend** (librerías globales, DataTables, accesibilidad) | Hecho | Documentado en [PUNTOS_FLOJOS_FRONTEND.md](PUNTOS_FLOJOS_FRONTEND.md). Defaults de DataTables (idioma, pageLength, lengthMenu, processing, responsive) en `app/Views/layout/scripts.php`. |
| **Config** (.env por entorno) | Hecho | Documentado en [CONFIG_ENV.md](CONFIG_ENV.md). Comentarios en `.env.example` con referencia a este doc. |
| **BaseModel** poco usado | Hecho | Documentado en [CONVENCION_MODELOS.md](CONVENCION_MODELOS.md). DocBlock en `app/Models/BaseModel.php` con convención y enlace al doc. |

---

## Enlaces rápidos

- [PUNTOS_FLOJOS_FRONTEND.md](PUNTOS_FLOJOS_FRONTEND.md) — librerías globales, DataTables, accesibilidad
- [CONFIG_ENV.md](CONFIG_ENV.md) — configuración y .env por entorno
- [CONVENCION_MODELOS.md](CONVENCION_MODELOS.md) — BaseModel vs métodos propios
