# Análisis de responsive del proyecto

Evaluación del comportamiento en **desktop**, **tablet** y **celular** según el código actual (vistas, layout, CSS y JS).

---

## Resumen

| Dispositivo | Nivel | Comentario breve |
|-------------|--------|-------------------|
| **Desktop** | ✅ Bueno | Layout pensado para escritorio; sidebar fijo/mini, tablas y formularios cómodos. |
| **Tablet** | ✅ Aceptable | Breakpoints y `table-responsive` presentes; algunos modales y tablas densas pueden apretarse. |
| **Celular** | ⚠️ Mejorable | Viewport y menú hamburguesa correctos; tablas DataTable y formularios complejos siguen siendo difíciles en pantallas muy chicas. |

En conjunto: **responsive suficiente para uso interno/operativo**; no está pensado como experiencia “mobile-first” pero se puede usar en tablet y, con limitaciones, en celular.

---

## 1. Lo que está bien resuelto

### Viewport y meta
- **Layout principal** (`app/Views/layout/app.php`):  
  `<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">`  
  Correcto para que no haga zoom raro en móviles.
- **Login** (`app/Views/auth/login.php`):  
  `viewport` + `max-width: 420px` + padding; se ve bien en móvil.

### Layout y grid
- **Contenedor**: `container-fluid` + padding responsive (`px-xl-4 px-sm-2 px-0`, `py-lg-2 py-1`, `mt-0 mt-lg-3`).  
  El contenido se adapta al ancho de pantalla.
- **Vistas**: Uso consistente de **Bootstrap** (`row`, `col-12`, `col-md-*`, `col-lg-*`) en listados, filtros y formularios (banderas, equipos, calibración, reportes, etc.).  
  En tablet/móvil las columnas pasan a apilarse.

### Header
- **Desktop (xl)**: Botón “mini sidebar” visible; búsqueda y nombre/rol visibles.
- **Tablet/móvil (< xl)**: Solo **menú hamburguesa** (`d-block d-xl-none`); nombre de usuario se oculta en pantallas pequeñas (`d-none d-md-inline`), rol en `d-none d-lg-inline`.  
  Reduce bien el contenido en pantallas chicas.

### Sidebar
- **Desktop**: Sidebar fijo; opción “mini” con `.sidebar-mini-btn`.
- **Tablet/celular**: `theme.js` hace `.sidebar.toggleClass("open")` con el botón `.menu-toggle`.  
  El template Luno usa breakpoints tipo Bootstrap (576, 768, 992, 1200, 1400 px) para mostrar/ocultar y animar.  
  Comportamiento típico: menú que se abre/cierra con el hamburguesa.

### Tablas
- **DataTables**: Opción `responsive: true` en Banderas (y patrón repetible).
- **Contenedor**: Uso de `.table-responsive` con `overflow-x: auto` y `-webkit-overflow-scrolling: touch` en varias vistas (banderas, equipos, calibración, reportes, etc.).  
  En móvil/tablet las tablas hacen scroll horizontal en lugar de romper el layout.

### Formularios y botones (vistas revisadas)
- **Banderas, Equipos, Calibración**:  
  - `@media (max-width: 768px)`: botones más grandes (`min-height: 44px`, `font-size: 16px`), inputs igual para mejor toque.  
  - Clase `.btn-tablet` con `touch-action: manipulation` para evitar doble tap.  
- **Modales**: En móvil `max-width: 95%`, en tablet `max-width: 90%` para que no se salgan de la pantalla.

### Login
- Vista independiente con Bootstrap 5, viewport correcto y layout centrado con `max-width: 420px`; se ve bien en todos los tamaños.

---

## 2. Puntos débiles / mejorables

### Tablas muy anchas (DataTables con muchas columnas)
- En **Equipos, Calibración, Reportes** hay muchas columnas; aunque exista `table-responsive`, en **celular** el scroll horizontal es largo y la lectura incómoda.
- **Recomendación**: En pantallas muy chicas, valorar:
  - Ocultar columnas poco críticas (DataTables responsive columns / `columnDefs` por breakpoint), o
  - Vista “resumida” o cards por fila en lugar de tabla (cambio más grande).

### Modales con mucho contenido
- Modales de **Equipos** y **Calibración** tienen muchos campos; en **móvil** 95% de ancho puede seguir siendo justo y requerir mucho scroll vertical.
- **Recomendación**: En `max-width: 768px` (o menor), considerar modales a pantalla completa o secciones colapsables para no abrumar.

### Sidebar en móvil
- Depende del CSS del template (Luno) para overlay y cierre al tocar fuera; si no está definido, conviene comprobar que al abrir el menú:
  - El resto de la pantalla esté cubierto o deshabilitado, y
  - Un click fuera cierre el sidebar.

### Formularios muy largos (Calibración, Equipos)
- Formularios con muchas filas (por ejemplo calibración con detalle) en **celular** son usables pero pesados; no hay diferencias específicas de layout solo para móvil (por ejemplo menos columnas en un mismo paso).
- **Recomendación**: Mantener el patrón actual y, si se prioriza móvil, ir a pasos o acordeones por sección.

### Reportes y exportaciones
- Vistas de reportes usan `row`/`col-*` y algunas tablas con `table-responsive`; en **tablet** suele estar bien.  
- En **celular** las tablas de reporte tienen el mismo problema que el resto: mucho scroll horizontal si hay muchas columnas.

---

## 3. Breakpoints y tecnologías

- **CSS**: Template Luno + Bootstrap (breakpoints típicos: 576, 768, 992, 1200, 1400 px).
- **Vistas propias**: `max-width: 768px` (móvil) y `769px–1024px` (tablet) en banderas, equipos, calibración.
- **Header**: `d-xl-*` (≥1200 px), `d-md-*` (≥768 px), `d-lg-*` (≥992 px).

No hay contradicciones evidentes; el criterio es coherente entre layout global y vistas.

---

## 4. Conclusión y prioridades

- **Desktop**: Muy bien cubierto.
- **Tablet**: Bien cubierto para uso operativo; tablas y modales pueden afinarse si el uso en tablet crece.
- **Celular**: Usable para consultas rápidas y login; tablas y formularios complejos son el cuello de botella.

**Prioridades si se quiere mejorar:**
1. Revisar en dispositivo real: sidebar abierto/cerrado y overlay en móvil.
2. Reducir columnas visibles o cambiar a vista resumida/cards en DataTables en pantallas &lt; 768 px (o 576 px).
3. En modales muy cargados (Equipos/Calibración), probar ancho 100% o pantalla completa en móvil y, si hace falta, dividir en pasos o acordeones.

---

## 5. Mejoras aplicadas (implementación en repo)

Se implementaron las prioridades anteriores:

### CSS global (`public/assets/css/custom.css`)
- **Botones e inputs**: Clase `.btn-tablet` y tamaños mínimos para toque (44px, 16px en &lt; 768px); `touch-action: manipulation`.
- **Tablas**: `.table-responsive` con `overflow-x: auto` y `-webkit-overflow-scrolling: touch`; fuente 14px en tablet.
- **Modales**: En &lt; 576px modal a **pantalla completa** (100vw, 100vh, scroll en body); en 577–768px max-width 95%; en 769–1024px max-width 90%.
- **Sidebar**: Clase `.sidebar-backdrop` (overlay oscuro) que se muestra al abrir el menú en móvil y se oculta en &gt; 1200px.

### Layout (`app/Views/layout/`)
- **app.php**: Se añadió `<div class="sidebar-backdrop" id="sidebar-backdrop">` después del sidebar.
- **scripts.php**: Al hacer clic en `.menu-toggle` se sincroniza el backdrop con el estado del sidebar; al hacer clic en el backdrop se cierra el sidebar; en `resize` a &gt; 1200px se cierran sidebar y backdrop.

### Vistas
- **Banderas, Equipos, Calibración**: Se eliminó CSS responsive duplicado; se deja solo lo específico (validación, anchos de columnas). Lo común queda en `custom.css`.
- **Calibración**: Se añadieron `columnDefs` con `responsivePriority` (1–5) en la DataTable para que en pantallas chicas se oculten columnas en orden de prioridad.

### Resultado
- **Celular**: Modales fullscreen, tablas con scroll táctil, botones/inputs más grandes, sidebar con overlay y cierre al tocar fuera.
- **Tablet**: Modales al 90–95%, mismo patrón de tablas y controles.
- **Desktop**: Sin cambios; el backdrop no se muestra en &gt; 1200px.
