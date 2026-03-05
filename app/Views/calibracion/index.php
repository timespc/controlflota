<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
Calibración
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
  /* Responsive global: ver public/assets/css/custom.css (btn-tablet, table-responsive, modales) */
</style>
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<?php if (session('error')): ?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <?= esc(session('error')) ?>
      <a href="<?= site_url('marcas-sensor') ?>" class="alert-link">Ir a Marcas sensor</a>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  </div>
</div>
<?php endif; ?>
<?php if (session('success')): ?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <?= esc(session('success')) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
  </div>
</div>
<?php endif; ?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <div class="card border border-secondary">
      <div class="card-header text-white bg-secondary pt-3 pb-2 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Calibraciones</h5>
        <button type="button" class="btn btn-primary btn-tablet" id="btn-nueva-calibracion" data-bs-toggle="modal" data-bs-target="#modal-calibracion">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-circle me-1" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>Nueva calibración
        </button>
      </div>
      <div class="card-body">
        <div class="row g-3 mb-3">
          <div class="col-12 col-md-4 col-lg-3">
            <label class="form-label">Nº de calibración</label>
            <input type="text" id="buscar_numero" class="form-control" placeholder="Ej: 12345">
          </div>
          <div class="col-12 col-md-4 col-lg-3">
            <label class="form-label">Patente</label>
            <input type="text" id="buscar_patente" class="form-control" placeholder="Ej: AA123BB">
          </div>
          <div class="col-12 col-md-4 col-lg-3 d-flex align-items-end">
            <button type="button" class="btn btn-outline-primary btn-tablet" id="btn-buscar-calibraciones">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-search me-1" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>Buscar
            </button>
          </div>
        </div>
        <div class="table-responsive">
          <table id="calibraciones-datatable" class="table table-striped table-bordered table-hover" style="width:100%">
            <thead>
              <tr>
                <th>Nº</th>
                <th>Patente</th>
                <th>Fecha calib.</th>
                <th>Calibrador</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Crear / Editar calibración (por defecto tamaño xl; botón para expandir a fullscreen) -->
<div class="modal fade" id="modal-calibracion" tabindex="-1" aria-labelledby="titulo-modal-calibracion" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header text-white bg-secondary py-3">
        <h5 class="modal-title" id="titulo-modal-calibracion">Crear calibración</h5>
        <div class="d-flex align-items-center gap-2">
          <button type="button" class="btn btn-outline-light btn-sm" id="btn-modal-fullscreen-toggle" title="Expandir a pantalla completa">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-aspect-ratio" viewBox="0 0 16 16"><path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h13A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 12.5v-9zM1.5 3a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-13z"/><path d="M2 4.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1H3v2.5a.5.5 0 0 1-1 0v-3zm12 7a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1 0-1H13V8.5a.5.5 0 0 1 1 0v3z"/></svg>
          </button>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
      </div>
      <div class="modal-body overflow-auto py-4">
        <form id="form-calibracion">
          <?= csrf_field() ?>
          <input type="hidden" name="id_calibracion" id="id_calibracion" value="">
          <input type="hidden" name="multiflecha_por_cisterna" id="multiflecha_por_cisterna" value="">
          <input type="hidden" name="multiflecha_por_cisterna_semi2" id="multiflecha_por_cisterna_semi2" value="">
          <div class="row g-3 mb-4">
            <div class="col-6 col-md-4 col-lg-2">
              <label class="form-label">PATENTE</label>
              <div class="input-group">
                <select id="patente_select" class="form-select" title="Buscar o seleccionar patente">
                  <option value="">-- Buscar o seleccionar --</option>
                </select>
                <button type="button" class="btn btn-outline-secondary" id="btn-agregar-patente-modal" title="Agregar patente nueva">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2z"/></svg>
                </button>
              </div>
              <input type="hidden" name="patente" id="patente" value="">
              <small class="text-muted" id="patente-seleccionada-texto"></small>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
              <label class="form-label">FECHA CALIB</label>
              <input type="date" name="fecha_calib" id="fecha_calib" class="form-control">
            </div>
            <div class="col-6 col-md-4 col-lg-2">
              <label class="form-label">CALIBRADOR</label>
              <select name="id_calibrador" id="id_calibrador" class="form-select">
                <option value="">-- Seleccionar --</option>
              </select>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
              <label class="form-label">TEMP. AGUA</label>
              <input type="number" name="temp_agua" id="temp_agua" class="form-control" step="0.1">
            </div>
            <div class="col-6 col-md-4 col-lg-2">
              <label class="form-label">VALVULAS</label>
              <select name="valvulas" id="valvulas" class="form-select">
                <option value="ABIERTAS">Abiertas</option>
                <option value="CERRADAS">Cerradas</option>
              </select>
            </div>
          </div>
          <div class="row g-3 mb-4">
            <div class="col-12 col-md-6">
              <label class="form-label">OBSERVACIONES</label>
              <textarea name="observaciones" id="observaciones" class="form-control" rows="2"></textarea>
              <div class="form-check form-check-inline mt-1">
                <input class="form-check-input" type="checkbox" id="incluir_observaciones_impresion" value="1">
                <label class="form-check-label small" for="incluir_observaciones_impresion">Incluir observaciones en la impresión</label>
              </div>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
              <label class="form-label">TIPO UNIDAD</label>
              <select name="tipo_unidad" id="tipo_unidad" class="form-select">
                <option value="">-- Seleccionar --</option>
                <option value="CHASIS">CHASIS</option>
                <option value="ACOPLADO">ACOPLADO</option>
                <option value="SEMI">SEMI</option>
              </select>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
              <label class="form-label">VTO. CALIB</label>
              <input type="date" name="vto_calib" id="vto_calib" class="form-control">
            </div>
            <div class="col-6 col-md-3 col-lg-2">
              <label class="form-label">MULTI FLECHA</label>
              <select name="multi_flecha" id="multi_flecha" class="form-select">
                <option value="NO" selected>No</option>
                <option value="SI">Si</option>
              </select>
            </div>
            <div class="col-6 col-md-3 col-lg-2 d-none" id="wrap_multi_flecha_semi2">
              <label class="form-label">MULTI FLECHA 2do semi</label>
              <select name="multi_flecha_semi2" id="multi_flecha_semi2" class="form-select">
                <option value="NO" selected>No</option>
                <option value="SI">Si</option>
              </select>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
              <label class="form-label">Nº REGLA</label>
              <input type="text" name="n_regla" id="n_regla" class="form-control bg-light" readonly>
            </div>
            <div class="col-6 col-md-4 col-lg-2">
              <label class="form-label">Lts de capacidad nominal</label>
              <input type="text" name="litros_capacidad_nominal" id="litros_capacidad_nominal" class="form-control fw-bold" readonly placeholder="0.00">
              <small class="text-muted">Suma de capacidades de cada línea</small>
            </div>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
              <h6 class="mb-0">Detalle de calibración por línea</h6>
              <button type="button" class="btn btn-success btn-sm btn-agregar-linea" id="btn-agregar-linea">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle me-1" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>Agregar línea
              </button>
            </div>
            <div class="col-12 table-responsive">
              <table class="table table-bordered table-sm table-hover" id="tabla-detalle-calib">
                <thead class="table-secondary">
                  <tr>
                    <th rowspan="2" class="text-center align-middle" style="min-width: 70px;">Cis. Nº</th>
                    <th rowspan="2" class="text-center align-middle" style="min-width: 60px;">MFlec</th>
                    <th rowspan="2" class="text-center align-middle" style="min-width: 100px;">Capacidad (L)</th>
                    <th rowspan="2" class="text-center align-middle" style="min-width: 90px;">Enrase</th>
                    <th rowspan="2" class="text-center align-middle" style="min-width: 90px;">Referen</th>
                    <th rowspan="2" class="text-center align-middle" style="min-width: 90px;">Vacio (Calc)</th>
                    <th rowspan="2" class="text-center align-middle" style="min-width: 90px;">Vacio Lts (Calc)</th>
                    <th colspan="4" class="text-center align-middle bg-primary text-white py-2">PRECINTOS</th>
                    <th rowspan="2" class="text-center align-middle" style="min-width: 50px;"></th>
                  </tr>
                  <tr class="table-secondary">
                    <th class="text-center" style="min-width: 90px;">Campana</th>
                    <th class="text-center" style="min-width: 90px;">Soporte</th>
                    <th class="text-center" style="min-width: 90px;">Hombre</th>
                    <th class="text-center" style="min-width: 95px;">Últ. actual.</th>
                  </tr>
                </thead>
                <tbody id="tbody-detalle-calib">
                  <tr class="fila-linea" data-linea-index="1">
                    <td class="text-center align-middle fw-bold numero-cisterna">1</td>
                    <td class="text-center align-middle">
                      <button type="button" class="btn btn-outline-secondary btn-sm btn-mflec" title="Multiflecha">MF</button>
                    </td>
                    <td>
                      <input type="number" name="detalle_capacidad[]" class="form-control form-control-sm input-capacidad-linea" step="0.01" min="0" value="0">
                    </td>
                    <td><input type="number" name="detalle_enrase[]" class="form-control form-control-sm" step="0.01" value="0"></td>
                    <td><input type="number" name="detalle_referen[]" class="form-control form-control-sm" step="0.01" value="0"></td>
                    <td><input type="text" name="detalle_vacio[]" class="form-control form-control-sm" readonly placeholder="Calc"></td>
                    <td><input type="number" name="detalle_vacio_lts[]" class="form-control form-control-sm input-vacio-lts" step="0.01" min="0" placeholder="3% de capacidad"></td>
                    <td><input type="text" name="detalle_precinto_campana[]" class="form-control form-control-sm" placeholder=""></td>
                    <td><input type="text" name="detalle_precinto_soporte[]" class="form-control form-control-sm" placeholder=""></td>
                    <td><input type="text" name="detalle_precinto_hombre[]" class="form-control form-control-sm" placeholder=""></td>
                    <td><input type="text" name="detalle_precinto_ultima[]" class="form-control form-control-sm" readonly placeholder=""></td>
                    <td class="text-center align-middle">
                      <button type="button" class="btn btn-outline-danger btn-sm btn-quitar-linea" title="Quitar línea"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg></button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="row g-3 mb-4 d-none" id="bloque-semi2">
            <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
              <h6 class="mb-0">2do semi</h6>
              <button type="button" class="btn btn-success btn-sm btn-agregar-linea-semi2" id="btn-agregar-linea-semi2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-circle me-1" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>Agregar línea
              </button>
            </div>
            <div class="col-12 table-responsive">
              <table class="table table-bordered table-sm table-hover" id="tabla-detalle-calib-semi2">
                <thead class="table-secondary">
                  <tr>
                    <th rowspan="2" class="text-center align-middle" style="min-width: 70px;">Cis. Nº</th>
                    <th rowspan="2" class="text-center align-middle" style="min-width: 60px;">MFlec</th>
                    <th rowspan="2" class="text-center align-middle" style="min-width: 100px;">Capacidad (L)</th>
                    <th rowspan="2" class="text-center align-middle" style="min-width: 90px;">Enrase</th>
                    <th rowspan="2" class="text-center align-middle" style="min-width: 90px;">Referen</th>
                    <th rowspan="2" class="text-center align-middle" style="min-width: 90px;">Vacio (Calc)</th>
                    <th rowspan="2" class="text-center align-middle" style="min-width: 90px;">Vacio Lts (Calc)</th>
                    <th colspan="4" class="text-center align-middle bg-primary text-white py-2">PRECINTOS</th>
                    <th rowspan="2" class="text-center align-middle" style="min-width: 50px;"></th>
                  </tr>
                  <tr class="table-secondary">
                    <th class="text-center" style="min-width: 90px;">Campana</th>
                    <th class="text-center" style="min-width: 90px;">Soporte</th>
                    <th class="text-center" style="min-width: 90px;">Hombre</th>
                    <th class="text-center" style="min-width: 95px;">Últ. actual.</th>
                  </tr>
                </thead>
                <tbody id="tbody-detalle-calib-semi2">
                  <tr class="fila-linea-semi2" data-linea-index="1">
                    <td class="text-center align-middle fw-bold numero-cisterna-semi2">1</td>
                    <td class="text-center align-middle">
                      <button type="button" class="btn btn-outline-secondary btn-sm btn-mflec-semi2" title="Multiflecha 2do semi">MF</button>
                    </td>
                    <td><input type="number" name="detalle_semi2_capacidad[]" class="form-control form-control-sm input-capacidad-linea-semi2" step="0.01" min="0" value="0"></td>
                    <td><input type="number" name="detalle_semi2_enrase[]" class="form-control form-control-sm" step="0.01" value="0"></td>
                    <td><input type="number" name="detalle_semi2_referen[]" class="form-control form-control-sm" step="0.01" value="0"></td>
                    <td><input type="text" name="detalle_semi2_vacio[]" class="form-control form-control-sm" readonly placeholder="Calc"></td>
                    <td><input type="number" name="detalle_semi2_vacio_lts[]" class="form-control form-control-sm input-vacio-lts-semi2" step="0.01" min="0" placeholder="3%"></td>
                    <td><input type="text" name="detalle_semi2_precinto_campana[]" class="form-control form-control-sm" placeholder=""></td>
                    <td><input type="text" name="detalle_semi2_precinto_soporte[]" class="form-control form-control-sm" placeholder=""></td>
                    <td><input type="text" name="detalle_semi2_precinto_hombre[]" class="form-control form-control-sm" placeholder=""></td>
                    <td><input type="text" name="detalle_semi2_precinto_ultima[]" class="form-control form-control-sm" readonly placeholder=""></td>
                    <td class="text-center align-middle">
                      <button type="button" class="btn btn-outline-danger btn-sm btn-quitar-linea-semi2" title="Quitar línea"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg></button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <div class="row g-3">
            <div class="col-12 d-flex flex-wrap gap-2 align-items-center">
              <span id="contenedor-imprimir-calib" class="d-none">
                <?php if (function_exists('es_admin') && es_admin()): ?>
                <button type="button" class="btn btn-outline-primary btn-tablet" id="btn-imprimir-original-reimprimir" data-impresa="0" data-url-base="<?= site_url('calibracion/imprimir/') ?>">Imprimir Original</button>
                <button type="button" class="btn btn-outline-secondary btn-tablet" id="btn-imprimir-borrador" data-url-base="<?= site_url('calibracion/imprimir/') ?>">Borrador</button>
                <?php endif; ?>
                <a href="#" id="btn-informe-carga-segura" class="btn btn-outline-info btn-tablet" target="_blank" rel="noopener" title="Informe de Carga Segura (se abre en nueva pestaña)" data-puede-usar-informe="<?= !empty($puede_usar_informe_carga_segura) ? '1' : '0' ?>" data-url-marcas="<?= site_url('marcas-sensor') ?>">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16"><path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/><path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/></svg>
                  Informe Carga Segura
                </a>
              </span>
              <button type="submit" class="btn btn-primary btn-tablet">Guardar</button>
              <button type="button" class="btn btn-danger btn-tablet">Eliminar</button>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer border-top py-3">
        <div class="me-auto">
          <button type="button" class="btn btn-outline-secondary btn-tablet" id="btn-modal-notas" title="Notas del calibrador" disabled>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-journal-text me-1" viewBox="0 0 16 16"><path d="M5 10.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0-2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/><path d="M3 0h10a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2v-1h1v1a1 1 0 0 0 1 1h10a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v1H1V2a2 2 0 0 1 2-2z"/><path d="M1 5v-.5a.5.5 0 0 1 1 0V5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0V8h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0v.5h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1z"/></svg>
            Notas
          </button>
        </div>
        <button type="button" class="btn btn-secondary btn-tablet" data-bs-dismiss="modal">Volver a la lista</button>
        <button type="button" class="btn btn-primary btn-tablet" id="btn-modal-guardar">Guardar calibración</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Notas del calibrador -->
<div class="modal fade" id="modal-notas" tabindex="-1" aria-labelledby="titulo-modal-notas" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-secondary text-white py-3">
        <h5 class="modal-title" id="titulo-modal-notas">Notas del calibrador</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body py-4">
        <p class="small text-muted mb-2">Notas internas para esta calibración (solo visible para usuarios del sistema). <strong>Nunca se imprimen en la tarjeta de calibración.</strong> Se guarda quién cargó las notas y la fecha.</p>
        <textarea id="modal-notas-textarea" class="form-control font-monospace" rows="12" placeholder="Escriba aquí las notas..."></textarea>
        <div id="modal-notas-info" class="small text-muted mt-2"></div>
      </div>
      <div class="modal-footer py-3">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btn-guardar-notas">Guardar notas</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal agregar equipo (patente nueva) -->
<div class="modal fade" id="modal-agregar-unidad-calib" tabindex="-1" aria-labelledby="titulo-modal-agregar-unidad" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-secondary text-white py-2">
        <h6 class="modal-title" id="titulo-modal-agregar-unidad">Agregar patente (nuevo equipo)</h6>
        <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body py-3">
        <div class="mb-2">
          <label class="form-label">Patente <span class="text-danger">*</span></label>
          <input type="text" id="nueva_patente_input" class="form-control" placeholder="Ej: AA123BB" maxlength="20">
          <div class="invalid-feedback" id="nueva_patente_error"></div>
        </div>
        <div class="mb-2">
          <label class="form-label">Tipo unidad <span class="text-danger">*</span></label>
          <select id="nueva_tipo_unidad_select" class="form-select">
            <option value="">-- Seleccionar --</option>
            <option value="CHASIS">CHASIS</option>
            <option value="ACOPLADO">ACOPLADO</option>
            <option value="SEMI">SEMI</option>
          </select>
          <div class="invalid-feedback" id="nueva_tipo_unidad_error"></div>
        </div>
      </div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary btn-sm" id="btn-guardar-nueva-unidad">Agregar equipo</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Multiflecha Cisterna N° X (como imagen: header CISTERNA + CALIB, tabla, aviso) -->
<div class="modal fade" id="modal-multiflecha" tabindex="-1" aria-labelledby="titulo-modal-multiflecha" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header text-white py-2" style="background: #8b0000;">
        <div class="d-flex flex-column">
          <strong class="mb-1">MULTI FLECHA CISTERNA N° <span id="modal-multiflecha-cisterna-numero" class="bg-dark px-2 py-0 rounded">1</span></strong>
          <strong>MULTIFLECHA CALIB N° <span id="modal-multiflecha-calib-numero" class="bg-dark px-2 py-0 rounded">—</span></strong>
        </div>
        <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body py-3">
        <div class="table-responsive">
          <table class="table table-bordered table-sm table-hover" id="tabla-multiflecha">
            <thead class="table-secondary">
              <tr>
                <th class="text-center" style="width: 45px;">MF</th>
                <th>CAP.</th>
                <th>ENRASE</th>
                <th>REF</th>
                <th>Vacío</th>
                <th>Vac.Lts</th>
                <th colspan="3" class="text-center bg-primary text-white">N° PRECINTOS</th>
                <th class="text-center" style="width: 45px;">Sup</th>
                <th>ULT.ACT</th>
              </tr>
              <tr class="table-secondary">
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>CAMPANA</th>
                <th>SOPORTE</th>
                <th>P.HOMBRE</th>
                <th></th>
                <th></th>
              </tr>
            </thead>
            <tbody id="tbody-multiflecha"></tbody>
          </table>
        </div>
        <button type="button" class="btn btn-success btn-sm mt-2" id="btn-agregar-fila-multiflecha">
          <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-plus-circle me-1" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>Agregar línea
        </button>
        <p class="text-danger fw-bold small mt-3 mb-0" id="modal-multiflecha-aviso">ATENCIÓN!!! SI MODIFICA CISTERNA-<span id="modal-multiflecha-aviso-cisterna">1</span> CALIBRACIÓN ESTÁNDAR, HAGA LOS MISMOS CAMBIOS EN C<span id="modal-multiflecha-aviso-cisterna-2">1</span> MULTIFLECHA</p>
      </div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary btn-sm" id="btn-guardar-multiflecha">Guardar cambios</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Confirmar reimpresión (motivo + confirmar) -->
<div class="modal fade" id="modal-reimpresion" tabindex="-1" aria-labelledby="titulo-modal-reimpresion" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-secondary text-white py-2">
        <h6 class="modal-title" id="titulo-modal-reimpresion">Confirmar reimpresión</h6>
        <button type="button" class="btn-close btn-close-white btn-sm" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body py-3">
        <p class="small text-muted mb-2">Indique el motivo de la reimpresión (opcional pero recomendado).</p>
        <label class="form-label" for="reimpresion-mensaje">¿Por qué reimprime?</label>
        <textarea id="reimpresion-mensaje" class="form-control" rows="3" placeholder="Ej: documento extraviado, corrección de datos..."></textarea>
        <div class="invalid-feedback" id="reimpresion-mensaje-error"></div>
      </div>
      <div class="modal-footer py-2">
        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary btn-sm" id="btn-confirmar-reimpresion">Confirmar y abrir impresión</button>
      </div>
    </div>
  </div>
</div>

<script>
$(function() {
  var puedeUsarInformeCargaSegura = <?= !empty($puede_usar_informe_carga_segura) ? 'true' : 'false' ?>;
  var urlInformeCargaSeguraBase = '<?= site_url('calibracion/informe-carga-segura/') ?>';
  var contadorLinea = 1;

  function actualizarLitrosCapacidadNominal() {
    var total = 0;
    $('#tabla-detalle-calib .input-capacidad-linea').each(function() {
      total += parseFloat($(this).val()) || 0;
    });
    $('#litros_capacidad_nominal').val(total.toFixed(2));
  }

  function renumerarLineas() {
    $('#tbody-detalle-calib tr.fila-linea').each(function(idx) {
      var n = idx + 1;
      $(this).attr('data-linea-index', n);
      $(this).find('.numero-cisterna').text(n);
    });
    var total = $('#tbody-detalle-calib tr.fila-linea').length;
    $('#tbody-detalle-calib .btn-quitar-linea').prop('disabled', total <= 1);
  }

  function agregarLinea() {
    contadorLinea++;
    var tr = '<tr class="fila-linea" data-linea-index="' + contadorLinea + '">' +
      '<td class="text-center align-middle fw-bold numero-cisterna">' + contadorLinea + '</td>' +
      '<td class="text-center align-middle"><button type="button" class="btn btn-outline-secondary btn-sm btn-mflec" title="Multiflecha">MF</button></td>' +
      '<td><input type="number" name="detalle_capacidad[]" class="form-control form-control-sm input-capacidad-linea" step="0.01" min="0" value="0"></td>' +
      '<td><input type="number" name="detalle_enrase[]" class="form-control form-control-sm" step="0.01" value="0"></td>' +
      '<td><input type="number" name="detalle_referen[]" class="form-control form-control-sm" step="0.01" value="0"></td>' +
      '<td><input type="text" name="detalle_vacio[]" class="form-control form-control-sm" readonly placeholder="Calc"></td>' +
      '<td><input type="number" name="detalle_vacio_lts[]" class="form-control form-control-sm input-vacio-lts" step="0.01" min="0" placeholder="3% de capacidad"></td>' +
      '<td><input type="text" name="detalle_precinto_campana[]" class="form-control form-control-sm" placeholder=""></td>' +
      '<td><input type="text" name="detalle_precinto_soporte[]" class="form-control form-control-sm" placeholder=""></td>' +
      '<td><input type="text" name="detalle_precinto_hombre[]" class="form-control form-control-sm" placeholder=""></td>' +
      '<td><input type="text" name="detalle_precinto_ultima[]" class="form-control form-control-sm" readonly placeholder=""></td>' +
      '<td class="text-center align-middle">' +
        '<button type="button" class="btn btn-outline-danger btn-sm btn-quitar-linea" title="Quitar línea"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg></button>' +
      '</td></tr>';
    $('#tbody-detalle-calib').append(tr);
    renumerarLineas();
    actualizarLitrosCapacidadNominal();
  }

  $('#btn-agregar-linea').on('click', agregarLinea);

  // --- Multiflecha: abrir modal por cisterna (multi_flecha=SI). Si no hay id_calibracion, datos en multiflechaPendiente ---
  var multiflechaIdCalib = 0;
  var multiflechaNumeroLinea = 0;
  var multiflechaNumeroSemi = 1; // 1 = primer semi, 2 = segundo semi
  var multiflechaPendiente = {}; // { "1": [ lineas ], "2": [ ... ] } cuando la calibración aún no está guardada
  var multiflechaPendienteSemi2 = {}; // idem para 2do semi

  $(document).on('click', '#modal-calibracion .btn-mflec', function() {
    if ($('#multi_flecha').val() !== 'SI') return;
    multiflechaNumeroSemi = 1;
    var idCalib = $('#id_calibracion').val() ? parseInt($('#id_calibracion').val(), 10) : 0;
    var $row = $(this).closest('tr.fila-linea');
    var numLinea = parseInt($row.find('.numero-cisterna').first().text(), 10) || parseInt($row.attr('data-linea-index'), 10) || 1;
    multiflechaIdCalib = idCalib;
    multiflechaNumeroLinea = numLinea;
    $('#modal-multiflecha-cisterna-numero').text(numLinea);
    $('#modal-multiflecha-calib-numero').text(idCalib ? idCalib : '—');
    $('#modal-multiflecha-aviso-cisterna').text(numLinea);
    $('#modal-multiflecha-aviso-cisterna-2').text(numLinea);
    $('#tbody-multiflecha').empty();
    var urlMf = '<?= site_url('calibracion/multiflecha/') ?>' + idCalib + '/' + numLinea + (multiflechaNumeroSemi === 2 ? '/' + multiflechaNumeroSemi : '');
    if (idCalib) {
      $.getJSON(urlMf, function(res) {
        if (res.success && res.data && res.data.length) {
          $.each(res.data, function(i, lin) {
            agregarFilaMultiflecha(lin.numero_multiflecha, lin.capacidad, lin.enrase, lin.referen, lin.vacio_calc, lin.vacio_lts, lin.precinto_campana, lin.precinto_soporte, lin.precinto_hombre, lin.updated_at);
          });
        } else {
          agregarFilaMultiflecha(1, 0, 0, 0, null, null, '', '', '', null);
        }
        if ($('#tbody-multiflecha tr').length === 0) agregarFilaMultiflecha(1, 0, 0, 0, null, null, '', '', '', null);
        var mod = new bootstrap.Modal(document.getElementById('modal-multiflecha'));
        mod.show();
      }).fail(function() {
        agregarFilaMultiflecha(1, 0, 0, 0, null, null, '', '', '', null);
        var mod = new bootstrap.Modal(document.getElementById('modal-multiflecha'));
        mod.show();
      });
    } else {
      var pend = multiflechaNumeroSemi === 2 ? multiflechaPendienteSemi2[String(numLinea)] : multiflechaPendiente[String(numLinea)];
      if (pend && pend.length) {
        $.each(pend, function(i, lin) {
          agregarFilaMultiflecha(lin.numero_multiflecha, lin.capacidad, lin.enrase, lin.referen, lin.vacio_calc, lin.vacio_lts, lin.precinto_campana, lin.precinto_soporte, lin.precinto_hombre, null);
        });
      }
      if ($('#tbody-multiflecha tr').length === 0) agregarFilaMultiflecha(1, 0, 0, 0, null, null, '', '', '', null);
      var mod = new bootstrap.Modal(document.getElementById('modal-multiflecha'));
      mod.show();
    }
  });

  $(document).on('click', '#modal-calibracion .btn-mflec-semi2', function() {
    if ($('#multi_flecha_semi2').val() !== 'SI') return;
    multiflechaNumeroSemi = 2;
    var idCalib = $('#id_calibracion').val() ? parseInt($('#id_calibracion').val(), 10) : 0;
    var $row = $(this).closest('tr.fila-linea-semi2');
    var numLinea = parseInt($row.find('.numero-cisterna-semi2').first().text(), 10) || parseInt($row.attr('data-linea-index'), 10) || 1;
    multiflechaIdCalib = idCalib;
    multiflechaNumeroLinea = numLinea;
    $('#modal-multiflecha-cisterna-numero').text(numLinea);
    $('#modal-multiflecha-calib-numero').text(idCalib ? idCalib : '—');
    $('#modal-multiflecha-aviso-cisterna').text(numLinea);
    $('#modal-multiflecha-aviso-cisterna-2').text(numLinea);
    $('#tbody-multiflecha').empty();
    var urlMf = '<?= site_url('calibracion/multiflecha/') ?>' + idCalib + '/' + numLinea + '/' + multiflechaNumeroSemi;
    if (idCalib) {
      $.getJSON(urlMf, function(res) {
        if (res.success && res.data && res.data.length) {
          $.each(res.data, function(i, lin) {
            agregarFilaMultiflecha(lin.numero_multiflecha, lin.capacidad, lin.enrase, lin.referen, lin.vacio_calc, lin.vacio_lts, lin.precinto_campana, lin.precinto_soporte, lin.precinto_hombre, lin.updated_at);
          });
        } else {
          agregarFilaMultiflecha(1, 0, 0, 0, null, null, '', '', '', null);
        }
        if ($('#tbody-multiflecha tr').length === 0) agregarFilaMultiflecha(1, 0, 0, 0, null, null, '', '', '', null);
        var mod = new bootstrap.Modal(document.getElementById('modal-multiflecha'));
        mod.show();
      }).fail(function() {
        agregarFilaMultiflecha(1, 0, 0, 0, null, null, '', '', '', null);
        var mod = new bootstrap.Modal(document.getElementById('modal-multiflecha'));
        mod.show();
      });
    } else {
      var pend = multiflechaPendienteSemi2[String(numLinea)];
      if (pend && pend.length) {
        $.each(pend, function(i, lin) {
          agregarFilaMultiflecha(lin.numero_multiflecha, lin.capacidad, lin.enrase, lin.referen, lin.vacio_calc, lin.vacio_lts, lin.precinto_campana, lin.precinto_soporte, lin.precinto_hombre, null);
        });
      }
      if ($('#tbody-multiflecha tr').length === 0) agregarFilaMultiflecha(1, 0, 0, 0, null, null, '', '', '', null);
      var mod = new bootstrap.Modal(document.getElementById('modal-multiflecha'));
      mod.show();
    }
  });

  function formatoUltAct(ultAct) {
    if (!ultAct || ultAct === '') return '—';
    var m = String(ultAct).match(/^(\d{4})-(\d{2})-(\d{2})\s+(.+)$/);
    if (m) return m[3] + '/' + m[2] + '/' + m[1] + ' ' + m[4];
    return ultAct;
  }

  function agregarFilaMultiflecha(numMf, capacidad, enrase, referen, vacioCalc, vacioLts, pCampana, pSoporte, pHombre, ultAct) {
    var n = $('#tbody-multiflecha tr').length + 1;
    var numMfVal = numMf || n;
    var ultActStr = formatoUltAct(ultAct);
    var tr = '<tr class="fila-multiflecha">' +
      '<td class="text-center align-middle fw-bold">' + numMfVal + '</td>' +
      '<td><input type="number" class="form-control form-control-sm mf-capacidad" step="0.01" min="0" value="' + (capacidad || 0) + '"></td>' +
      '<td><input type="number" class="form-control form-control-sm mf-enrase" step="0.01" value="' + (enrase || 0) + '"></td>' +
      '<td><input type="number" class="form-control form-control-sm mf-referen" step="0.01" value="' + (referen || 0) + '"></td>' +
      '<td><input type="number" class="form-control form-control-sm mf-vacio-calc" step="0.01" value="' + (vacioCalc != null ? vacioCalc : '') + '"></td>' +
      '<td><input type="number" class="form-control form-control-sm mf-vacio-lts" step="0.01" min="0" value="' + (vacioLts != null ? vacioLts : '') + '"></td>' +
      '<td><input type="text" class="form-control form-control-sm mf-precinto-campana" value="' + (pCampana || '') + '"></td>' +
      '<td><input type="text" class="form-control form-control-sm mf-precinto-soporte" value="' + (pSoporte || '') + '"></td>' +
      '<td><input type="text" class="form-control form-control-sm mf-precinto-hombre" value="' + (pHombre || '') + '"></td>' +
      '<td class="text-center align-middle"><button type="button" class="btn btn-outline-danger btn-sm btn-quitar-fila-mf" title="Quitar"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg></button></td>' +
      '<td class="form-control form-control-sm form-control-plaintext small mf-ultact">' + ultActStr + '</td></tr>';
    $('#tbody-multiflecha').append(tr);
  }

  function actualizarHiddenMultiflecha() {
    $('#multiflecha_por_cisterna').val(JSON.stringify(multiflechaPendiente));
    $('#multiflecha_por_cisterna_semi2').val(JSON.stringify(multiflechaPendienteSemi2));
  }

  $('#btn-agregar-fila-multiflecha').on('click', function() {
    var n = $('#tbody-multiflecha tr').length + 1;
    agregarFilaMultiflecha(n, 0, 0, 0, null, null, '', '', '', null);
  });

  $(document).on('click', '#modal-multiflecha .btn-quitar-fila-mf', function() {
    $(this).closest('tr').remove();
  });

  $('#btn-guardar-multiflecha').on('click', function() {
    var lineas = [];
    $('#tbody-multiflecha tr.fila-multiflecha').each(function(i) {
      var $t = $(this);
      lineas.push({
        numero_multiflecha: i + 1,
        capacidad: parseFloat($t.find('.mf-capacidad').val()) || 0,
        enrase: parseFloat($t.find('.mf-enrase').val()) || 0,
        referen: parseFloat($t.find('.mf-referen').val()) || 0,
        vacio_calc: $t.find('.mf-vacio-calc').val() ? parseFloat($t.find('.mf-vacio-calc').val()) : null,
        vacio_lts: $t.find('.mf-vacio-lts').val() ? parseFloat($t.find('.mf-vacio-lts').val()) : null,
        precinto_campana: $t.find('.mf-precinto-campana').val() || null,
        precinto_soporte: $t.find('.mf-precinto-soporte').val() || null,
        precinto_hombre: $t.find('.mf-precinto-hombre').val() || null
      });
    });
    if (multiflechaIdCalib) {
      var payload = {
        csrf_test_name: $('input[name="csrf_test_name"]').val(),
        id_calibracion: multiflechaIdCalib,
        numero_linea: multiflechaNumeroLinea,
        numero_semi: multiflechaNumeroSemi,
        lineas: lineas
      };
      $.ajax({
        url: '<?= site_url('calibracion/multiflecha-guardar') ?>',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(payload),
        dataType: 'json',
        success: function(res) {
          if (res.success) {
            Swal.fire({ icon: 'success', title: 'Listo', text: res.message, timer: 1500, showConfirmButton: false });
            bootstrap.Modal.getInstance(document.getElementById('modal-multiflecha')).hide();
          } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message });
          }
        },
        error: function(xhr) {
          var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error al guardar';
          Swal.fire({ icon: 'error', title: 'Error', text: msg });
        }
      });
    } else {
      if (multiflechaNumeroSemi === 2) {
        multiflechaPendienteSemi2[String(multiflechaNumeroLinea)] = lineas;
      } else {
        multiflechaPendiente[String(multiflechaNumeroLinea)] = lineas;
      }
      actualizarHiddenMultiflecha();
      Swal.fire({ icon: 'success', title: 'Listo', text: 'Se guardará al guardar la calibración.', timer: 1500, showConfirmButton: false });
      bootstrap.Modal.getInstance(document.getElementById('modal-multiflecha')).hide();
    }
  });

  $('#tabla-detalle-calib').on('click', '.btn-quitar-linea', function() {
    if ($('#tbody-detalle-calib tr.fila-linea').length <= 1) return;
    $(this).closest('tr').remove();
    renumerarLineas();
    actualizarLitrosCapacidadNominal();
  });
  $('#tabla-detalle-calib').on('input', '.input-capacidad-linea', function() {
    actualizarLitrosCapacidadNominal();
    var capacidad = parseFloat($(this).val()) || 0;
    var vacioLts = capacidad * 0.03;
    $(this).closest('tr').find('.input-vacio-lts').val(vacioLts > 0 ? vacioLts.toFixed(2) : '');
  });

  var reglaHabilitadaDefault = '<?= esc(isset($regla_habilitada_numero) ? $regla_habilitada_numero : '') ?>';

  function valvulasParaSelect(v) {
    if (v === 1 || v === '1') return 'ABIERTAS';
    if (v === 0 || v === '0') return 'CERRADAS';
    if (v === 'ABIERTAS' || v === 'CERRADAS') return v;
    return 'ABIERTAS';
  }

  function mostrarBloqueSemi2(mostrar) {
    if (mostrar) {
      $('#bloque-semi2').removeClass('d-none');
      $('#wrap_multi_flecha_semi2').removeClass('d-none');
      actualizarEstadoBotonesMF();
    } else {
      $('#bloque-semi2').addClass('d-none');
      $('#wrap_multi_flecha_semi2').addClass('d-none');
      $('#multi_flecha_semi2').val('NO');
      $('#tbody-detalle-calib-semi2 tr.fila-linea-semi2').not(':first').remove();
      $('#tbody-detalle-calib-semi2 tr.fila-linea-semi2').first().find('input').val(function() {
        var name = $(this).attr('name') || '';
        if (name.indexOf('capacidad') >= 0 || name.indexOf('enrase') >= 0 || name.indexOf('referen') >= 0) return '0';
        return '';
      });
      renumerarLineasSemi2();
      multiflechaPendienteSemi2 = {};
      actualizarHiddenMultiflecha();
    }
  }

  function rellenarDetalleSemi2(detalle_semi2) {
    if (!detalle_semi2 || !detalle_semi2.length) {
      $('#tbody-detalle-calib-semi2 tr.fila-linea-semi2').not(':first').remove();
      $('#tbody-detalle-calib-semi2 tr.fila-linea-semi2').first().find('input').val(function() {
        var name = $(this).attr('name') || '';
        if (name.indexOf('capacidad') >= 0 || name.indexOf('enrase') >= 0 || name.indexOf('referen') >= 0) return '0';
        return '';
      });
      renumerarLineasSemi2();
      return;
    }
    $('#tbody-detalle-calib-semi2 tr.fila-linea-semi2').remove();
    $.each(detalle_semi2, function(i, lin) {
      var tr = '<tr class="fila-linea-semi2" data-linea-index="' + (i + 1) + '">' +
        '<td class="text-center align-middle fw-bold numero-cisterna-semi2">' + (i + 1) + '</td>' +
        '<td class="text-center align-middle"><button type="button" class="btn btn-outline-secondary btn-sm btn-mflec-semi2" title="Multiflecha 2do semi">MF</button></td>' +
        '<td><input type="number" name="detalle_semi2_capacidad[]" class="form-control form-control-sm input-capacidad-linea-semi2" step="0.01" min="0" value="' + (lin.capacidad ?? 0) + '"></td>' +
        '<td><input type="number" name="detalle_semi2_enrase[]" class="form-control form-control-sm" step="0.01" value="' + (lin.enrase ?? 0) + '"></td>' +
        '<td><input type="number" name="detalle_semi2_referen[]" class="form-control form-control-sm" step="0.01" value="' + (lin.referen ?? 0) + '"></td>' +
        '<td><input type="text" name="detalle_semi2_vacio[]" class="form-control form-control-sm" readonly placeholder="Calc" value="' + (lin.vacio_calc ?? '') + '"></td>' +
        '<td><input type="number" name="detalle_semi2_vacio_lts[]" class="form-control form-control-sm input-vacio-lts-semi2" step="0.01" min="0" value="' + (lin.vacio_lts ?? '') + '"></td>' +
        '<td><input type="text" name="detalle_semi2_precinto_campana[]" class="form-control form-control-sm" value="' + (lin.precinto_campana ?? '') + '"></td>' +
        '<td><input type="text" name="detalle_semi2_precinto_soporte[]" class="form-control form-control-sm" value="' + (lin.precinto_soporte ?? '') + '"></td>' +
        '<td><input type="text" name="detalle_semi2_precinto_hombre[]" class="form-control form-control-sm" value="' + (lin.precinto_hombre ?? '') + '"></td>' +
        '<td><input type="text" name="detalle_semi2_precinto_ultima[]" class="form-control form-control-sm" readonly value="' + (lin.precinto_ultima ?? '') + '"></td>' +
        '<td class="text-center align-middle"><button type="button" class="btn btn-outline-danger btn-sm btn-quitar-linea-semi2" title="Quitar línea"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg></button></td></tr>';
      $('#tbody-detalle-calib-semi2').append(tr);
    });
    renumerarLineasSemi2();
  }

  function renumerarLineasSemi2() {
    $('#tbody-detalle-calib-semi2 tr.fila-linea-semi2').each(function(idx) {
      var n = idx + 1;
      $(this).attr('data-linea-index', n);
      $(this).find('.numero-cisterna-semi2').text(n);
    });
    var total = $('#tbody-detalle-calib-semi2 tr.fila-linea-semi2').length;
    $('#tbody-detalle-calib-semi2 .btn-quitar-linea-semi2').prop('disabled', total <= 1);
  }

  function agregarLineaSemi2() {
    var n = $('#tbody-detalle-calib-semi2 tr.fila-linea-semi2').length + 1;
    var tr = '<tr class="fila-linea-semi2" data-linea-index="' + n + '">' +
      '<td class="text-center align-middle fw-bold numero-cisterna-semi2">' + n + '</td>' +
      '<td class="text-center align-middle"><button type="button" class="btn btn-outline-secondary btn-sm btn-mflec-semi2" title="Multiflecha 2do semi">MF</button></td>' +
      '<td><input type="number" name="detalle_semi2_capacidad[]" class="form-control form-control-sm input-capacidad-linea-semi2" step="0.01" min="0" value="0"></td>' +
      '<td><input type="number" name="detalle_semi2_enrase[]" class="form-control form-control-sm" step="0.01" value="0"></td>' +
      '<td><input type="number" name="detalle_semi2_referen[]" class="form-control form-control-sm" step="0.01" value="0"></td>' +
      '<td><input type="text" name="detalle_semi2_vacio[]" class="form-control form-control-sm" readonly placeholder="Calc"></td>' +
      '<td><input type="number" name="detalle_semi2_vacio_lts[]" class="form-control form-control-sm input-vacio-lts-semi2" step="0.01" min="0" placeholder="3%"></td>' +
      '<td><input type="text" name="detalle_semi2_precinto_campana[]" class="form-control form-control-sm" placeholder=""></td>' +
      '<td><input type="text" name="detalle_semi2_precinto_soporte[]" class="form-control form-control-sm" placeholder=""></td>' +
      '<td><input type="text" name="detalle_semi2_precinto_hombre[]" class="form-control form-control-sm" placeholder=""></td>' +
      '<td><input type="text" name="detalle_semi2_precinto_ultima[]" class="form-control form-control-sm" readonly placeholder=""></td>' +
      '<td class="text-center align-middle"><button type="button" class="btn btn-outline-danger btn-sm btn-quitar-linea-semi2" title="Quitar línea"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg></button></td></tr>';
    $('#tbody-detalle-calib-semi2').append(tr);
    renumerarLineasSemi2();
  }

  $('#btn-agregar-linea-semi2').on('click', agregarLineaSemi2);
  $(document).on('click', '.btn-quitar-linea-semi2', function() {
    if ($('#tbody-detalle-calib-semi2 tr.fila-linea-semi2').length <= 1) return;
    $(this).closest('tr').remove();
    renumerarLineasSemi2();
  });

  function abrirModalNueva() {
    $('#id_calibracion').val('');
    $('#form-calibracion')[0].reset();
    $('#patente_select').val('');
    $('#patente').val('');
    $('#tipo_unidad').val('');
    $('#patente-seleccionada-texto').text('');
    $('#titulo-modal-calibracion').text('Crear calibración');
    $('#n_regla').val(reglaHabilitadaDefault);
    $('#multi_flecha').val('NO');
    $('#valvulas').val('ABIERTAS');
    $('#tbody-detalle-calib tr.fila-linea').not(':first').remove();
    $('#tbody-detalle-calib tr.fila-linea').find('input').val(function() {
      var name = $(this).attr('name') || '';
      if ($(this).hasClass('input-capacidad-linea') || name.indexOf('enrase') >= 0 || name.indexOf('referen') >= 0) return '0';
      return '';
    });
    renumerarLineas();
    actualizarLitrosCapacidadNominal();
    $('#contenedor-imprimir-calib').addClass('d-none');
    multiflechaPendiente = {};
    multiflechaPendienteSemi2 = {};
    mostrarBloqueSemi2(false);
    actualizarHiddenMultiflecha();
  }

  function actualizarBotonesImpresion(yaImpresa) {
    var $btn = $('#btn-imprimir-original-reimprimir');
    var $cont = $('#contenedor-imprimir-calib');
    var id = $('#id_calibracion').val();
    if (!id) {
      $cont.addClass('d-none');
      return;
    }
    $cont.removeClass('d-none');
    var $btnInforme = $('#btn-informe-carga-segura');
    var puedeUsarInforme = $btnInforme.data('puede-usar-informe') === 1 || $btnInforme.attr('data-puede-usar-informe') === '1';
    if (puedeUsarInforme) {
      $btnInforme.attr('href', '<?= site_url('calibracion/informe-carga-segura/') ?>' + id).removeClass('disabled').off('click.informe-block');
    } else {
      $btnInforme.attr('href', '#').addClass('disabled').off('click.informe-block').on('click.informe-block', function(e) {
        e.preventDefault();
        var urlMarcas = $btnInforme.data('url-marcas') || $btnInforme.attr('data-url-marcas');
        var msg = 'Para usar el Informe de Carga Segura debe cargar al menos una marca de sensor. Vaya a Marcas sensor y cargue las marcas.';
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'warning',
            title: 'Marcas de sensor requeridas',
            text: msg,
            confirmButtonText: 'Ir a Marcas sensor',
            showCancelButton: true,
            cancelButtonText: 'Cerrar'
          }).then(function(r) { if (r.isConfirmed && urlMarcas) window.location.href = urlMarcas; });
        } else {
          Swal.fire({
            icon: 'warning',
            title: 'Marcas de sensor requeridas',
            text: msg + (urlMarcas ? ' Puede ir a Marcas sensor.' : ''),
            confirmButtonText: urlMarcas ? 'Ir a Marcas sensor' : 'Cerrar',
            showCancelButton: !!urlMarcas,
            cancelButtonText: 'Cerrar'
          }).then(function(r) { if (r.isConfirmed && urlMarcas) window.location.href = urlMarcas; });
        }
      });
    }
    if (yaImpresa) {
      $btn.text('Reimprimir').attr('data-impresa', '1');
    } else {
      $btn.text('Imprimir Original').attr('data-impresa', '0');
    }
  }

  $('#btn-imprimir-original-reimprimir').on('click', function() {
    var id = $('#id_calibracion').val();
    if (!id) return;
    var urlBase = $(this).data('url-base');
    var impresa = $(this).attr('data-impresa') === '1';
    if (impresa) {
      $('#reimpresion-mensaje').val('').removeClass('is-invalid');
      $('#reimpresion-mensaje-error').text('').hide();
      $('#modal-reimpresion').data('url-reimprimir', urlBase + id);
      var mod = new bootstrap.Modal(document.getElementById('modal-reimpresion'));
      mod.show();
    } else {
      var url = urlBase + id + '?original=1';
      if ($('#incluir_observaciones_impresion').is(':checked')) url += '&incluir_observaciones=1';
      window.open(url, '_blank', 'noopener,noreferrer');
      $(this).text('Reimprimir').attr('data-impresa', '1');
    }
  });

  $('#btn-confirmar-reimpresion').on('click', function() {
    var id = $('#id_calibracion').val();
    var urlReimprimir = $('#modal-reimpresion').data('url-reimprimir');
    if (!id || !urlReimprimir) return;
    var mensaje = $.trim($('#reimpresion-mensaje').val());
    var $btn = $(this).prop('disabled', true);
    var $textarea = $('#reimpresion-mensaje').removeClass('is-invalid');
    $('#reimpresion-mensaje-error').text('').hide();
    var payload = {
      id_calibracion: id,
      mensaje: mensaje
    };
    var $csrf = $('#form-calibracion input[name="<?= csrf_token() ?>"]');
    var headers = { 'Content-Type': 'application/json' };
    if ($csrf.length) headers['X-CSRF-TOKEN'] = $csrf.val();
    $.ajax({
      url: '<?= site_url('calibracion/registrar-reimpresion') ?>',
      type: 'POST',
      contentType: 'application/json',
      headers: headers,
      data: JSON.stringify(payload),
      dataType: 'json',
      success: function(res) {
        $btn.prop('disabled', false);
        if (res.success) {
          bootstrap.Modal.getInstance(document.getElementById('modal-reimpresion')).hide();
          $('#reimpresion-mensaje').val('');
          var urlAbrir = urlReimprimir;
          if ($('#incluir_observaciones_impresion').is(':checked')) urlAbrir += (urlReimprimir.indexOf('?') >= 0 ? '&' : '?') + 'incluir_observaciones=1';
          window.open(urlAbrir, '_blank', 'noopener,noreferrer');
          Swal.fire({ icon: 'success', title: 'Listo', text: res.message, timer: 1500, showConfirmButton: false });
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: res.message });
        }
      },
      error: function(xhr) {
        $btn.prop('disabled', false);
        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error al registrar la reimpresión';
        Swal.fire({ icon: 'error', title: 'Error', text: msg });
      }
    });
  });

  $('#btn-imprimir-borrador').on('click', function() {
    var id = $('#id_calibracion').val();
    if (!id) return;
    var urlBase = $(this).data('url-base');
    var url = urlBase + id + '?borrador=1';
    if ($('#incluir_observaciones_impresion').is(':checked')) url += '&incluir_observaciones=1';
    window.open(url, '_blank', 'noopener,noreferrer');
  });

  $('#btn-nueva-calibracion').on('click', function() {
    abrirModalNueva();
  });

  function cargarCalibradoresEnSelect(callback) {
    $.getJSON('<?= site_url('calibradores/opciones') ?>', function(res) {
      var sel = $('#id_calibrador');
      var valActual = sel.val();
      sel.find('option:not(:first)').remove();
      if (res.success && res.data && res.data.length) {
        $.each(res.data, function(i, c) {
          sel.append($('<option></option>').attr('value', c.id_calibrador).text(c.calibrador));
        });
      }
      if (valActual) sel.val(valActual);
      if (typeof callback === 'function') callback();
    });
  }

  $('#modal-calibracion').on('show.bs.modal', function() {
    if (!$('#id_calibracion').val()) abrirModalNueva();
    cargarPatentesEnSelect();
    cargarCalibradoresEnSelect();
    actualizarEstadoBotonesMF();
    $('#btn-modal-notas').prop('disabled', !$('#id_calibracion').val());
  });

  // Una vez seleccionado Si/No o Abiertas/Cerradas, quitar la opción "-- Seleccionar --"
  $('#multi_flecha').on('change', function() {
    actualizarEstadoBotonesMF();
  });
  $('#multi_flecha_semi2').on('change', function() {
    actualizarEstadoBotonesMF();
  });
  $('#valvulas').on('change', function() {
    if ($(this).val() !== '') {
      $(this).find('option[value=""]').remove();
    }
  });

  // Habilitar/deshabilitar botones MF según multi_flecha = SI (1er semi) y multi_flecha_semi2 = SI (2do semi)
  function actualizarEstadoBotonesMF() {
    var habilitado = $('#multi_flecha').val() === 'SI';
    $('#modal-calibracion .btn-mflec').prop('disabled', !habilitado);
    if (habilitado) {
      $('#modal-calibracion .btn-mflec').removeClass('btn-outline-secondary').addClass('btn-outline-primary');
    } else {
      $('#modal-calibracion .btn-mflec').removeClass('btn-outline-primary').addClass('btn-outline-secondary');
    }
    var habilitadoSemi2 = $('#multi_flecha_semi2').val() === 'SI';
    $('#modal-calibracion .btn-mflec-semi2').prop('disabled', !habilitadoSemi2);
    if (habilitadoSemi2) {
      $('#modal-calibracion .btn-mflec-semi2').removeClass('btn-outline-secondary').addClass('btn-outline-primary');
    } else {
      $('#modal-calibracion .btn-mflec-semi2').removeClass('btn-outline-primary').addClass('btn-outline-secondary');
    }
  }

  // --- Patente: listado de unidades + opción agregar ---
  function cargarPatentesEnSelect(q, callback) {
    var url = '<?= site_url('equipos/patentes') ?>';
    if (typeof q === 'string' && q.length) url += '?q=' + encodeURIComponent(q);
    $.getJSON(url, function(res) {
      var sel = $('#patente_select');
      var valActual = sel.val();
      sel.find('option:not(:first)').remove();
      if (res.success && res.data && res.data.length) {
        $.each(res.data, function(i, u) {
          var pat = u.patente_semi_delantero || u.patente || '';
          sel.append($('<option></option>').attr('value', pat).attr('data-tipo', u.tipo_unidad || '').text(pat + (u.tipo_unidad ? ' (' + u.tipo_unidad + ')' : '')));
        });
      }
      sel.append($('<option value="__agregar__">+ Agregar patente nueva...</option>'));
      if (valActual && valActual !== '__agregar__') sel.val(valActual);
      if (typeof callback === 'function') callback();
    });
  }

  function fechaHoyMasAnios(anios) {
    var d = new Date();
    d.setFullYear(d.getFullYear() + (anios || 0));
    return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2);
  }

  function rellenarFormularioDesdeCalibracion(c) {
    var hoy = fechaHoyMasAnios(0);
    var vtoDefault = fechaHoyMasAnios(2);
    $('#fecha_calib').val(hoy);
    $('#vto_calib').val(vtoDefault);
    $('#id_calibrador').val(c.id_calibrador || '');
    $('#temp_agua').val(c.temp_agua ?? '');
    $('#valvulas').val(valvulasParaSelect(c.valvulas));
    $('#observaciones').val(c.observaciones || '');
    $('#tipo_unidad').val(c.tipo_unidad || '');
    $('#multi_flecha').val(c.multi_flecha === 'SI' ? 'SI' : 'NO');
    $('#multi_flecha_semi2').val(c.multi_flecha_semi2 === 'SI' ? 'SI' : 'NO');
    $('#n_regla').val(reglaHabilitadaDefault);
    mostrarBloqueSemi2(!!(c.unidad && c.unidad.patente_semi_trasero));
    if (c.unidad && c.unidad.patente_semi_trasero) {
      rellenarDetalleSemi2(c.detalle_semi2 || []);
    }
    actualizarEstadoBotonesMF();
    cargarCalibradoresEnSelect(function() {
      $('#id_calibrador').val(c.id_calibrador || '');
    });
    var detalle = c.detalle || [];
    $('#tbody-detalle-calib tr.fila-linea').remove();
    if (detalle.length === 0) {
      $('#tbody-detalle-calib').append(trPrimeraLinea());
    } else {
      $.each(detalle, function(i, lin) {
        var tr = '<tr class="fila-linea" data-linea-index="' + (i + 1) + '">' +
          '<td class="text-center align-middle fw-bold numero-cisterna">' + (i + 1) + '</td>' +
          '<td class="text-center align-middle"><button type="button" class="btn btn-outline-secondary btn-sm btn-mflec" title="Multiflecha">MF</button></td>' +
          '<td><input type="number" name="detalle_capacidad[]" class="form-control form-control-sm input-capacidad-linea" step="0.01" min="0" value="' + (lin.capacidad ?? 0) + '"></td>' +
          '<td><input type="number" name="detalle_enrase[]" class="form-control form-control-sm" step="0.01" value="' + (lin.enrase ?? 0) + '"></td>' +
          '<td><input type="number" name="detalle_referen[]" class="form-control form-control-sm" step="0.01" value="' + (lin.referen ?? 0) + '"></td>' +
          '<td><input type="text" name="detalle_vacio[]" class="form-control form-control-sm" readonly placeholder="Calc" value="' + (lin.vacio_calc ?? '') + '"></td>' +
          '<td><input type="number" name="detalle_vacio_lts[]" class="form-control form-control-sm input-vacio-lts" step="0.01" min="0" value="' + (lin.vacio_lts ?? '') + '"></td>' +
          '<td><input type="text" name="detalle_precinto_campana[]" class="form-control form-control-sm" value="' + (lin.precinto_campana ?? '') + '"></td>' +
          '<td><input type="text" name="detalle_precinto_soporte[]" class="form-control form-control-sm" value="' + (lin.precinto_soporte ?? '') + '"></td>' +
          '<td><input type="text" name="detalle_precinto_hombre[]" class="form-control form-control-sm" value="' + (lin.precinto_hombre ?? '') + '"></td>' +
          '<td><input type="text" name="detalle_precinto_ultima[]" class="form-control form-control-sm" readonly value="' + (lin.precinto_ultima ?? '') + '"></td>' +
          '<td class="text-center align-middle"><button type="button" class="btn btn-outline-danger btn-sm btn-quitar-linea" title="Quitar línea"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg></button></td></tr>';
        $('#tbody-detalle-calib').append(tr);
      });
    }
    renumerarLineas();
    actualizarLitrosCapacidadNominal();
  }

  $('#patente_select').on('change', function() {
    var v = $(this).val();
    if (v === '' || v === '__agregar__') {
      if (v === '__agregar__') {
        $('#nueva_patente_input').val($('#patente').val() || '');
        $('#nueva_tipo_unidad_select').val('CHASIS');
        var mod = new bootstrap.Modal(document.getElementById('modal-agregar-unidad-calib'));
        mod.show();
      }
      $('#patente').val('');
      $('#tipo_unidad').val('');
      $('#patente-seleccionada-texto').text('');
      $(this).val('');
      return;
    }
    var opt = $(this).find('option:selected');
    $('#patente').val(v);
    $('#tipo_unidad').val(opt.data('tipo') || '');
    $('#patente-seleccionada-texto').text('Seleccionada: ' + v);

    if (!$('#id_calibracion').val()) {
      $.getJSON('<?= site_url('equipos/info-patente/') ?>' + encodeURIComponent(v), function(resUnidad) {
        if (resUnidad.success && resUnidad.data && resUnidad.data.patente_semi_trasero) {
          mostrarBloqueSemi2(true);
        } else {
          mostrarBloqueSemi2(false);
        }
      });
      $.getJSON('<?= site_url('calibracion/ultima-por-patente') ?>?patente=' + encodeURIComponent(v), function(res) {
        if (res.success && res.data) {
          Swal.fire({
            title: 'Calibración previa',
            text: 'Existe una calibración previa para esta patente. ¿Desea cargar los datos de la última calibración para autocompletar el formulario?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, cargar datos',
            cancelButtonText: 'No'
          }).then(function(result) {
            if (result.isConfirmed) {
              rellenarFormularioDesdeCalibracion(res.data);
            }
          });
        }
      });
    }
  });

  $('#btn-agregar-patente-modal').on('click', function() {
    $('#nueva_patente_input').val($('#patente').val() || '');
    $('#nueva_tipo_unidad_select').val('CHASIS');
    var mod = new bootstrap.Modal(document.getElementById('modal-agregar-unidad-calib'));
    mod.show();
  });

  $('#btn-guardar-nueva-unidad').on('click', function() {
    var pat = $.trim($('#nueva_patente_input').val());
    var tipo = $('#nueva_tipo_unidad_select').val();
    $('#nueva_patente_input, #nueva_tipo_unidad_select').removeClass('is-invalid');
    $('#nueva_patente_error, #nueva_tipo_unidad_error').text('').hide();
    if (!pat) {
      $('#nueva_patente_input').addClass('is-invalid');
      $('#nueva_patente_error').text('La patente es obligatoria').show();
      return;
    }
    if (!tipo) {
      $('#nueva_tipo_unidad_select').addClass('is-invalid');
      $('#nueva_tipo_unidad_error').text('El tipo de equipo es obligatorio').show();
      return;
    }
    $.ajax({
      url: '<?= site_url('equipos/guardar') ?>',
      type: 'POST',
      contentType: 'application/json',
      data: JSON.stringify({ patente: pat, tipo_unidad: tipo }),
      dataType: 'json',
      success: function(res) {
        if (res.success) {
          bootstrap.Modal.getInstance(document.getElementById('modal-agregar-unidad-calib')).hide();
          cargarPatentesEnSelect();
          $('#patente_select').val(pat);
          $('#patente').val(pat);
          $('#tipo_unidad').val(tipo);
          $('#patente-seleccionada-texto').text('Seleccionada: ' + pat);
          Swal.fire({ icon: 'success', title: 'Listo', text: res.message, timer: 2000, showConfirmButton: false });
        } else {
          if (res.errors && res.errors.patente) { $('#nueva_patente_input').addClass('is-invalid'); $('#nueva_patente_error').text(res.errors.patente).show(); }
          if (res.errors && res.errors.tipo_unidad) { $('#nueva_tipo_unidad_select').addClass('is-invalid'); $('#nueva_tipo_unidad_error').text(res.errors.tipo_unidad).show(); }
          Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Error al guardar' });
        }
      },
      error: function() {
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo agregar el equipo' });
      }
    });
  });

  $('#btn-modal-guardar').on('click', function() {
    $('#form-calibracion').submit();
  });

  $('#form-calibracion').on('submit', function(e) {
    e.preventDefault();
    // TODO: AJAX guardar; al éxito cerrar modal y refrescar lista
    var modal = bootstrap.Modal.getInstance(document.getElementById('modal-calibracion'));
    if (modal) modal.hide();
  });

  // Toggle fullscreen / ventana: por defecto modal-xl (chico); clic en el ícono para expandir o reducir
  $('#btn-modal-fullscreen-toggle').on('click', function() {
    var dialog = $('#modal-calibracion .modal-dialog');
    var btn = $(this);
    if (dialog.hasClass('modal-fullscreen')) {
      dialog.removeClass('modal-fullscreen').addClass('modal-xl modal-dialog-scrollable');
      dialog.find('.modal-content').removeClass('h-100');
      btn.attr('title', 'Expandir a pantalla completa');
    } else {
      dialog.removeClass('modal-xl modal-dialog-scrollable').addClass('modal-fullscreen');
      dialog.find('.modal-content').addClass('h-100');
      btn.attr('title', 'Reducir ventana');
    }
  });

  renumerarLineas();
  actualizarLitrosCapacidadNominal();

  // Tabla de listado: mismo estilo que Transportistas, Calibradores, etc.
  var tableCalibraciones = $('#calibraciones-datatable').DataTable({
    language: { url: '<?= base_url('assets/js/datatable/esp.json') ?>' },
    processing: true,
    serverSide: false,
    responsive: true,
    pageLength: 10,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
    ajax: {
      url: '<?= site_url('calibracion/listar') ?>',
      type: 'POST',
      data: function(d) {
        d.numero = $('#buscar_numero').val();
        d.patente = $('#buscar_patente').val();
        var $csrf = $('#form-calibracion input[name="<?= csrf_token() ?>"]');
        if ($csrf.length) { d[$csrf.attr('name')] = $csrf.val(); }
      }
    },
    columnDefs: [
      { targets: 0, responsivePriority: 1 },
      { targets: 1, responsivePriority: 2 },
      { targets: 2, responsivePriority: 3 },
      { targets: 3, responsivePriority: 4 },
      { targets: 4, responsivePriority: 5 }
    ],
    columns: [
      { data: 'numero' },
      { data: 'patente' },
      { data: 'fecha_calib' },
      { data: 'calibrador' },
      {
        data: null,
        orderable: false,
        className: 'text-center',
        render: function(data, type, row) {
          var id = row.id_calibracion || '';
          var urlInforme = urlInformeCargaSeguraBase + id;
          var btns = '<div class="btn-group" role="group">' +
            '<button class="btn btn-sm btn-primary btn-tablet ver-calibracion" data-id="' + id + '" title="Ver / Editar">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/></svg>' +
            '</button>';
          if (puedeUsarInformeCargaSegura) {
            btns += '<a href="' + urlInforme + '" class="btn btn-sm btn-outline-info btn-tablet" title="Informe de Carga Segura">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16"><path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/><path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5v2z"/></svg>' +
            '</a>';
          }
          btns += '</div>';
          return btns;
        }
      }
    ],
    order: [[0, 'desc']],
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
  });

  $('#btn-buscar-calibraciones').on('click', function() {
    tableCalibraciones.ajax.reload();
  });

  $(document).on('click', '.ver-calibracion', function() {
    var id = $(this).data('id');
    if (!id) return;
    $.getJSON('<?= site_url('calibracion/obtener/') ?>' + id, function(res) {
      if (!res.success || !res.data) {
        Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'No se encontró la calibración' });
        return;
      }
      var c = res.data;
      multiflechaPendiente = {};
      actualizarHiddenMultiflecha();
      $('#id_calibracion').val(c.id_calibracion || '');
      $('#patente').val(c.patente || '');
      $('#patente-seleccionada-texto').text(c.patente ? 'Seleccionada: ' + c.patente : '');
      $('#fecha_calib').val(c.fecha_calib || '');
      $('#vto_calib').val(c.vto_calib || '');
      $('#id_calibrador').val(c.id_calibrador || '');
      $('#temp_agua').val(c.temp_agua ?? '');
      $('#valvulas').val(valvulasParaSelect(c.valvulas));
      $('#observaciones').val(c.observaciones || '');
      $('#tipo_unidad').val(c.tipo_unidad || '');
      $('#multi_flecha').val(c.multi_flecha === 'SI' ? 'SI' : 'NO');
      $('#multi_flecha_semi2').val(c.multi_flecha_semi2 === 'SI' ? 'SI' : 'NO');
      $('#n_regla').val(c.n_regla || '');
      mostrarBloqueSemi2(!!(c.unidad && c.unidad.patente_semi_trasero));
      if (c.unidad && c.unidad.patente_semi_trasero) {
        rellenarDetalleSemi2(c.detalle_semi2 || []);
      }
      actualizarEstadoBotonesMF();
      $('#titulo-modal-calibracion').text('Editar calibración');
      cargarPatentesEnSelect(null, function() {
        $('#patente_select').val(c.patente || '');
      });
      cargarCalibradoresEnSelect(function() {
        $('#id_calibrador').val(c.id_calibrador || '');
      });
      var detalle = c.detalle || [];
      $('#tbody-detalle-calib tr.fila-linea').remove();
      if (detalle.length === 0) {
        $('#tbody-detalle-calib').append(trPrimeraLinea());
      } else {
        $.each(detalle, function(i, lin) {
          var tr = '<tr class="fila-linea" data-linea-index="' + (i + 1) + '">' +
            '<td class="text-center align-middle fw-bold numero-cisterna">' + (i + 1) + '</td>' +
            '<td class="text-center align-middle"><button type="button" class="btn btn-outline-secondary btn-sm btn-mflec" title="Multiflecha">MF</button></td>' +
            '<td><input type="number" name="detalle_capacidad[]" class="form-control form-control-sm input-capacidad-linea" step="0.01" min="0" value="' + (lin.capacidad ?? 0) + '"></td>' +
            '<td><input type="number" name="detalle_enrase[]" class="form-control form-control-sm" step="0.01" value="' + (lin.enrase ?? 0) + '"></td>' +
            '<td><input type="number" name="detalle_referen[]" class="form-control form-control-sm" step="0.01" value="' + (lin.referen ?? 0) + '"></td>' +
            '<td><input type="text" name="detalle_vacio[]" class="form-control form-control-sm" readonly placeholder="Calc" value="' + (lin.vacio_calc ?? '') + '"></td>' +
            '<td><input type="number" name="detalle_vacio_lts[]" class="form-control form-control-sm input-vacio-lts" step="0.01" min="0" value="' + (lin.vacio_lts ?? '') + '"></td>' +
            '<td><input type="text" name="detalle_precinto_campana[]" class="form-control form-control-sm" value="' + (lin.precinto_campana ?? '') + '"></td>' +
            '<td><input type="text" name="detalle_precinto_soporte[]" class="form-control form-control-sm" value="' + (lin.precinto_soporte ?? '') + '"></td>' +
            '<td><input type="text" name="detalle_precinto_hombre[]" class="form-control form-control-sm" value="' + (lin.precinto_hombre ?? '') + '"></td>' +
            '<td><input type="text" name="detalle_precinto_ultima[]" class="form-control form-control-sm" readonly value="' + (lin.precinto_ultima ?? '') + '"></td>' +
            '<td class="text-center align-middle"><button type="button" class="btn btn-outline-danger btn-sm btn-quitar-linea" title="Quitar línea"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg></button></td></tr>';
          $('#tbody-detalle-calib').append(tr);
        });
      }
      renumerarLineas();
      actualizarLitrosCapacidadNominal();
      actualizarBotonesImpresion(!!(c.fecha_impresion));
      $('#btn-modal-notas').prop('disabled', false);
      var mod = new bootstrap.Modal(document.getElementById('modal-calibracion'));
      mod.show();
    });
  });

  // --- Modal Notas del calibrador ---
  $('#btn-modal-notas').on('click', function() {
    var idCalib = $('#id_calibracion').val();
    if (!idCalib) return;
    $('#modal-notas').data('id-calibracion', idCalib);
    $('#modal-notas-textarea').val('');
    $('#modal-notas-info').text('');
    $.getJSON('<?= site_url('calibracion/notas/') ?>' + idCalib, function(res) {
      if (res.success) {
        $('#modal-notas-textarea').val(res.notas || '');
        if (res.updated_at) {
          var fecha = res.updated_at.replace(/^(\d{4})-(\d{2})-(\d{2})\s(\d{2}):(\d{2}).*/, '$3/$2/$1 $4:$5');
          $('#modal-notas-info').text('Última actualización: ' + fecha + (res.id_usuario ? ' (usuario ID ' + res.id_usuario + ')' : ''));
        }
      }
    }).fail(function() {
      $('#modal-notas-info').text('No se pudieron cargar las notas.');
    });
    var modNotas = new bootstrap.Modal(document.getElementById('modal-notas'));
    modNotas.show();
  });

  $('#btn-guardar-notas').on('click', function() {
    var idCalib = $('#modal-notas').data('id-calibracion');
    var notas = $('#modal-notas-textarea').val();
    if (!idCalib) return;
    var $btn = $(this).prop('disabled', true);
    var d = { id_calibracion: idCalib, notas: notas };
    var $csrf = $('#form-calibracion input[name="<?= csrf_token() ?>"]');
    if ($csrf.length) d[$csrf.attr('name')] = $csrf.val();
    $.ajax({
      url: '<?= site_url('calibracion/notas-guardar') ?>',
      method: 'POST',
      data: d,
      dataType: 'json'
    }).done(function(res) {
      if (res.success) {
        var ahora = new Date();
        var fecha = ('0' + ahora.getDate()).slice(-2) + '/' + ('0' + (ahora.getMonth() + 1)).slice(-2) + '/' + ahora.getFullYear() + ' ' + ('0' + ahora.getHours()).slice(-2) + ':' + ('0' + ahora.getMinutes()).slice(-2);
        $('#modal-notas-info').text('Guardado: ' + fecha).removeClass('text-danger').addClass('text-success');
        Swal.fire({ icon: 'success', title: 'Listo', text: res.message || 'Notas guardadas', timer: 1500, showConfirmButton: false });
      } else {
        $('#modal-notas-info').text(res.message || 'Error').addClass('text-danger');
        Swal.fire({ icon: 'error', title: 'Error', text: res.message });
      }
    }).fail(function(xhr) {
      var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error al guardar';
      $('#modal-notas-info').text(msg).addClass('text-danger');
      Swal.fire({ icon: 'error', title: 'Error', text: msg });
    }).always(function() {
      $btn.prop('disabled', false);
    });
  });

  function trPrimeraLinea() {
    return '<tr class="fila-linea" data-linea-index="1">' +
      '<td class="text-center align-middle fw-bold numero-cisterna">1</td>' +
      '<td class="text-center align-middle"><button type="button" class="btn btn-outline-secondary btn-sm btn-mflec" title="Multiflecha">MF</button></td>' +
      '<td><input type="number" name="detalle_capacidad[]" class="form-control form-control-sm input-capacidad-linea" step="0.01" min="0" value="0"></td>' +
      '<td><input type="number" name="detalle_enrase[]" class="form-control form-control-sm" step="0.01" value="0"></td>' +
      '<td><input type="number" name="detalle_referen[]" class="form-control form-control-sm" step="0.01" value="0"></td>' +
      '<td><input type="text" name="detalle_vacio[]" class="form-control form-control-sm" readonly placeholder="Calc"></td>' +
      '<td><input type="number" name="detalle_vacio_lts[]" class="form-control form-control-sm input-vacio-lts" step="0.01" min="0" placeholder="3% de capacidad"></td>' +
      '<td><input type="text" name="detalle_precinto_campana[]" class="form-control form-control-sm" placeholder=""></td>' +
      '<td><input type="text" name="detalle_precinto_soporte[]" class="form-control form-control-sm" placeholder=""></td>' +
      '<td><input type="text" name="detalle_precinto_hombre[]" class="form-control form-control-sm" placeholder=""></td>' +
      '<td><input type="text" name="detalle_precinto_ultima[]" class="form-control form-control-sm" readonly placeholder=""></td>' +
      '<td class="text-center align-middle"><button type="button" class="btn btn-outline-danger btn-sm btn-quitar-linea" title="Quitar línea"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg></button></td></tr>';
  }

  function validarMultiflechaCompleto() {
    var idCalib = $('#id_calibracion').val() ? parseInt($('#id_calibracion').val(), 10) : 0;
    if ($('#multi_flecha').val() === 'SI') {
      var numLineas = $('#tbody-detalle-calib tr.fila-linea').length;
      var faltan = [];
      if (!idCalib) {
        for (var i = 1; i <= numLineas; i++) {
          var pend = multiflechaPendiente[String(i)];
          if (!pend || !Array.isArray(pend) || pend.length === 0) faltan.push('C' + i);
        }
      }
      if (faltan.length) return { ok: false, mensaje: 'Con multiflecha activo, debe cargar la información de multiflecha para todas las cisternas. Faltan: ' + faltan.join(', ') + '. Use el botón MF de cada línea.' };
    }
    if ($('#multi_flecha_semi2').val() === 'SI' && $('#bloque-semi2').is(':visible')) {
      var numLineasSemi2 = $('#tbody-detalle-calib-semi2 tr.fila-linea-semi2').length;
      var faltanSemi2 = [];
      if (!idCalib) {
        for (var i = 1; i <= numLineasSemi2; i++) {
          var pend = multiflechaPendienteSemi2[String(i)];
          if (!pend || !Array.isArray(pend) || pend.length === 0) faltanSemi2.push('C' + i);
        }
      }
      if (faltanSemi2.length) return { ok: false, mensaje: 'Con multiflecha activo en 2do semi, debe cargar la información de multiflecha para todas las cisternas del 2do semi. Faltan: ' + faltanSemi2.join(', ') + '. Use el botón MF de cada línea del 2do semi.' };
    }
    return { ok: true };
  }

  $('#form-calibracion').on('submit', function(e) {
    e.preventDefault();
    var val = validarMultiflechaCompleto();
    if (!val.ok) {
      Swal.fire({ icon: 'warning', title: 'Multiflecha incompleto', text: val.mensaje });
      return;
    }
    var $form = $(this);
    var $btn = $('#btn-modal-guardar').prop('disabled', true);
    actualizarHiddenMultiflecha();
    $.ajax({
      url: '<?= site_url('calibracion/guardar') ?>',
      type: 'POST',
      data: $form.serialize(),
      dataType: 'json',
      success: function(res) {
        $btn.prop('disabled', false);
        if (res.success) {
          multiflechaPendiente = {};
          actualizarHiddenMultiflecha();
          bootstrap.Modal.getInstance(document.getElementById('modal-calibracion')).hide();
          tableCalibraciones.ajax.reload();
          Swal.fire({ icon: 'success', title: 'Listo', text: res.message, timer: 2000, showConfirmButton: false });
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: res.message });
        }
      },
      error: function(xhr) {
        $btn.prop('disabled', false);
        var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Error al guardar';
        Swal.fire({ icon: 'error', title: 'Error', text: msg });
      }
    });
  });

  $('#modal-calibracion').on('click', 'button.btn-danger.btn-tablet', function() {
    var id = $('#id_calibracion').val();
    if (!id) return;
    var self = this;
    Swal.fire({
      title: '¿Eliminar calibración?',
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(function(r) {
      if (r.isConfirmed) eliminarCalibracion(id, self);
    });
  });

  function eliminarCalibracion(id, btn) {
    var data = {};
    var $tok = $('#form-calibracion input[name="<?= csrf_token() ?>"]');
    if ($tok.length) { data[$tok.attr('name')] = $tok.val(); }
    $.post('<?= site_url('calibracion/eliminar/') ?>' + id, data, 'json')
      .done(function(res) {
        if (res.success) {
          bootstrap.Modal.getInstance(document.getElementById('modal-calibracion')).hide();
          tableCalibraciones.ajax.reload();
          Swal.fire({ icon: 'success', title: 'Eliminado', text: res.message, timer: 2000, showConfirmButton: false });
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: res.message });
        }
      })
      .fail(function() {
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo eliminar' });
      });
  }
});
</script>
<?= $this->endSection() ?>
