# Frontend: librerías globales, DataTables y accesibilidad

Documento que describe el estado actual del frontend (puntos flojos opcionales) y las mejoras aplicadas o recomendadas.

---

## 1. Librerías cargadas globalmente

Todas las vistas que extienden `layout/app` cargan los mismos CSS y JS en `layout/style.php` y `layout/scripts.php`. No hay carga bajo demanda por módulo.

### CSS (layout/style.php)

- **dataTables, select2, sweetalert2, jquery.dataTables.yadcf:** Sí se usan (DataTables, Select2, SweetAlert2, yadcf).
- **fullcalendar, jsgrid, summernote, dropify, skedtape, cropper, fancybox, daterangepicker, bootstrapdatepicker, flatpickr, nouislider, rangeslider, jkanban, x-editable, swiper, tuicalendar, tabledragger, smart_wizard_all:** Plugines del tema; muchos no se usan en las vistas actuales.

### JS (layout/scripts.php)

- **DataTables (bundle, yadcf, fixedColumns):** banderas, calibradores, cubiertas, marcas, nación, equipos, transportistas, calibracion, reglas, marcas_sensor, dashboard.
- **apexcharts:** Dashboard.
- **jquery.smartWizard:** Calibración (wizard).
- **waitMe, jquery-ui, toastr, sweetalert2, select2, jquery.validate:** Uso en varias vistas.
- **jquery.barrating, jqueryknob:** Posible uso puntual.

### Recomendación (opcional a futuro)

- Corto plazo: dejar como está.
- Medio/largo plazo: valorar layout "ligero" sin DataTables para vistas que no usan tablas, o cargar DataTables/ApexCharts solo donde se necesiten.

---

## 2. DataTables: estandarización aplicada

En **layout/scripts.php** se definen **valores por defecto** de DataTables tras cargar dataTables.bundle.js:

- `language: { url: '.../assets/js/datatable/esp.json' }`
- `pageLength: 10`
- `lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]]`
- `processing: true`
- `responsive: true`

Las vistas que inicializan DataTable heredan estos valores si no los sobrescriben.

---

## 3. Accesibilidad: guía breve

- Formularios: inputs con label o aria-label; mensajes de error asociados al campo.
- Botones: texto visible o aria-label en iconos (ej. "Editar", "Eliminar").
- Modales: role="dialog", aria-modal="true", aria-labelledby al título.
- Contraste y navegación por teclado: mantener foco y contraste adecuados.

---

## Resumen

- Librerías globales: documentado; carga bajo demanda opcional a futuro.
- DataTables: defaults unificados en layout/scripts.php.
- Accesibilidad: guía breve para nuevas vistas.
