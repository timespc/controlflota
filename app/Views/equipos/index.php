<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
Equipos
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
  /* Responsive global: ver public/assets/css/custom.css (btn-tablet, table-responsive, modales) */
  /* Espacio entre el header del modal y el contenido */
  #modal-equipo .modal-body {
    padding-top: 1.25rem;
  }
  
  /* Estilos para errores de validación */
  .invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
  }
  
  .is-invalid {
    border-color: #dc3545;
    padding-right: calc(1.5em + 0.75rem);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 .4.4.4-.4m0 4.8-.4-.4-.4.4'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
  }
  
  .section-title {
    font-weight: bold;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
    margin-top: 1.5rem;
  }
  
  .section-title:first-child {
    margin-top: 0;
  }

  /* Totales y Cisternas: menos margen superior para que queden más centrados en el modal */
  #modal-equipo .section-totales .section-title,
  #modal-equipo .section-cisternas .section-title,
  #modal-equipo .section-cotas .section-title {
    margin-top: 0.5rem;
    margin-bottom: 0.5rem;
  }
  /* Títulos pegados al bloque de arriba; el espacio lo da el mt-2 del col-12 contenedor */
  #modal-equipo h6.section-title.section-cotas,
  #modal-equipo .section-totales .section-title {
    margin-top: 0;
  }

  select.cubierta-eje:disabled {
    background-color: #e9ecef;
    cursor: not-allowed;
  }

  .vehicle-section-card {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    height: 100%;
  }
  .vehicle-section-card h6 {
    border-bottom: 2px solid #0074bd;
    padding-bottom: 10px;
    margin-bottom: 15px;
    font-weight: 600;
  }
</style>
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<?php
$vista = $vista ?? (object)[
  'mostrar_info_general' => true,
  'mostrar_seccion_vehiculos' => true,
  'mostrar_totales' => true,
  'mostrar_cisternas' => true,
  'mostrar_cubiertas_cotas' => true,
  'mostrar_observaciones' => true,
];
?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <div class="card border border-secondary">
      <div class="card-header text-white bg-secondary pt-3 pb-2 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Equipos</h5>
        <button type="button" class="btn btn-primary btn-tablet" data-bs-toggle="modal" data-bs-target="#modal-equipo" id="btn-nuevo-equipo">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
          </svg>
          Agregar equipo
        </button>
      </div>
      <div class="card-body">
        <div class="row g-2 mb-3 align-items-end flex-wrap">
          <div class="col-12 col-md-2">
            <label class="form-label small mb-0">Transportista</label>
            <select id="filtro-transportista" class="form-select form-select-sm">
              <option value="">Todos</option>
              <?php if (isset($transportistas_con_equipos) && is_array($transportistas_con_equipos)): foreach ($transportistas_con_equipos as $t): ?>
              <option value="<?= (int)$t['id_tta'] ?>"><?= esc($t['transportista']) ?> (<?= (int)$t['cantidad_equipos'] ?> equipos)</option>
              <?php endforeach; endif; ?>
            </select>
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label small mb-0">Patente tractor</label>
            <input type="text" id="filtro-patente-tractor" class="form-control form-control-sm" placeholder="Ej. AB123CD" maxlength="20">
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label small mb-0">Patente semi 1</label>
            <input type="text" id="filtro-patente-semi1" class="form-control form-control-sm" placeholder="Ej. AB123CD" maxlength="20">
          </div>
          <div class="col-12 col-md-2">
            <label class="form-label small mb-0">Patente semi 2</label>
            <input type="text" id="filtro-patente-semi2" class="form-control form-control-sm" placeholder="Ej. AB123CD" maxlength="20">
          </div>
          <div class="col-12 col-md-auto d-flex gap-1">
            <button type="button" id="btn-filtrar-equipos" class="btn btn-sm btn-primary btn-tablet">Filtrar</button>
            <button type="button" id="btn-limpiar-filtros-equipos" class="btn btn-sm btn-outline-secondary btn-tablet">Limpiar</button>
          </div>
        </div>
        <div class="table-responsive">
          <table id="equipos-datatable" class="table table-striped table-bordered table-hover" style="width:100%">
            <thead>
              <tr>
                <th>Patente tractor</th>
                <th>Patente semi 1</th>
                <th>Patente semi 2</th>
                <th>Transportista</th>
                <th>Bitren</th>
                <th>Cap. total</th>
                <th>Tara total</th>
                <th>Fecha</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <!-- Los datos se cargarán dinámicamente -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal para Agregar/Editar equipo -->
<div class="modal fade" id="modal-equipo" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" id="modal-equipo-dialog">
    <div class="modal-content text-start" role="document">
      <div class="modal-header text-white bg-secondary pt-3 pb-2">
        <h5 id="modal-title">Agregar equipo</h5>
        <button type="button" class="btn-close position-absolute top-0 end-0 mt-3 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" id="form-equipo" name="form-equipo" autocomplete="off">
          <input type="hidden" name="id_equipo" id="id_equipo">

          <?php if ($vista && !$vista->mostrar_info_general): ?>
          <div class="row g-3 mb-2">
            <div class="col-12 col-md-4">
              <label class="form-label">Transportista <span class="text-danger">(*)</span></label>
              <select name="id_tta" id="id_tta" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php if (isset($transportistas) && is_array($transportistas)): foreach ($transportistas as $trans): ?>
                <option value="<?= $trans['id_tta'] ?>"><?= esc($trans['transportista']) ?></option>
                <?php endforeach; endif; ?>
              </select>
            </div>
          </div>
          <?php endif; ?>

          <?php if ($vista && $vista->mostrar_info_general): ?>
          <div class="row g-3 mb-4">
            <h6 class="section-title">Información General</h6>
            <div class="col-12 col-md-3">
              <label class="form-label">Transportista <span class="text-danger">(*)</span></label>
              <select name="id_tta" id="id_tta" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php if (isset($transportistas) && is_array($transportistas)): foreach ($transportistas as $trans): ?>
                <option value="<?= $trans['id_tta'] ?>"><?= esc($trans['transportista']) ?></option>
                <?php endforeach; endif; ?>
              </select>
              <div class="invalid-feedback" id="id_tta-error"></div>
            </div>
            <div class="col-12 col-md-2">
              <label class="form-label">Bitren</label>
              <select name="bitren" id="bitren" class="form-select">
                <option value="NO">NO</option>
                <option value="SI">SI</option>
              </select>
            </div>
            <div class="col-12 col-md-2">
              <label class="form-label">Fecha Alta</label>
              <input type="date" name="fecha_alta" id="fecha_alta" class="form-control">
            </div>
            <div class="col-12 col-md-3">
              <label class="form-label">Modo Carga</label>
              <select name="modo_carga" id="modo_carga" class="form-select">
                <option value="">—</option>
                <option value="BOTTOM">BOTTOM</option>
                <option value="ENVASADO">ENVASADO</option>
                <option value="GRANEL">GRANEL</option>
                <option value="TOP">TOP</option>
                <option value="FOB">FOB</option>
              </select>
            </div>
            <div class="col-12 col-md-3">
              <label class="form-label">Nación</label>
              <select name="pais_id" id="pais_id" class="form-select">
                <option value="">—</option>
                <?php if (isset($paises) && is_array($paises)): foreach ($paises as $p): ?>
                <option value="<?= $p['id'] ?>"><?= esc($p['nombre']) ?></option>
                <?php endforeach; endif; ?>
              </select>
            </div>
            <div class="col-12 col-md-3">
              <label class="form-label">Bandera</label>
              <select name="id_bandera" id="id_bandera" class="form-select">
                <option value="">—</option>
                <?php if (isset($banderas) && is_array($banderas)): foreach ($banderas as $b): ?>
                <option value="<?= $b['id_bandera'] ?>"><?= esc($b['bandera']) ?></option>
                <?php endforeach; endif; ?>
              </select>
            </div>
            <div class="col-12 col-md-3">
              <label class="form-label">Marca</label>
              <select name="id_marca" id="id_marca" class="form-select">
                <option value="">—</option>
                <?php if (isset($marcas) && is_array($marcas)): foreach ($marcas as $m): ?>
                <option value="<?= $m['id_marca'] ?>"><?= esc($m['marca']) ?></option>
                <?php endforeach; endif; ?>
              </select>
            </div>
          </div>
          <?php endif; ?>

          <?php if ($vista && $vista->mostrar_seccion_vehiculos): ?>
          <div class="row g-3 mb-4">
            <h6 class="section-title">Configuración de Vehículos</h6>
            <div class="col-12 col-md-4">
              <div class="vehicle-section-card">
                <h6 class="text-primary mb-3">Tractor</h6>
                <div class="row g-2">
                  <div class="col-12">
                    <label class="form-label">Patente <span class="text-danger">(*)</span></label>
                    <input type="text" name="patente_tractor" id="patente_tractor" class="form-control" required>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Año Modelo</label>
                    <input type="number" name="tractor_anio_modelo" id="tractor_anio_modelo" class="form-control" min="1900" max="2100">
                  </div>
                  <div class="col-6">
                    <label class="form-label">TARA (Kgs)</label>
                    <input type="number" name="tractor_tara" id="tractor_tara" class="form-control" step="0.001" min="0">
                  </div>
                  <div class="col-6">
                    <label class="form-label">PBT (Kgs)</label>
                    <input type="number" name="tractor_pbt" id="tractor_pbt" class="form-control" step="0.001" min="0">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="vehicle-section-card">
                <h6 class="text-primary mb-3">Semi Delantera</h6>
                <div class="row g-2">
                  <div class="col-12">
                    <label class="form-label">Patente <span class="text-danger">(*)</span></label>
                    <input type="text" name="patente_semi_delantero" id="patente_semi_delantero" class="form-control" required>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Año Modelo</label>
                    <input type="number" name="semi_delantero_anio_modelo" id="semi_delantero_anio_modelo" class="form-control" min="1900" max="2100">
                  </div>
                  <div class="col-6">
                    <label class="form-label">TARA (Kgs)</label>
                    <input type="number" name="semi_delantero_tara" id="semi_delantero_tara" class="form-control" step="0.001" min="0">
                  </div>
                  <div class="col-6">
                    <label class="form-label">PBT (Kgs)</label>
                    <input type="number" name="semi_delantera_pbt" id="semi_delantera_pbt" class="form-control" step="0.001" min="0">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="vehicle-section-card">
                <h6 class="text-primary mb-3">Semi Trasero</h6>
                <div class="row g-2">
                  <div class="col-12">
                    <label class="form-label">Patente</label>
                    <input type="text" name="patente_semi_trasero" id="patente_semi_trasero" class="form-control">
                  </div>
                  <div class="col-12">
                    <label class="form-label">Año Modelo</label>
                    <input type="number" name="semi_trasero_anio_modelo" id="semi_trasero_anio_modelo" class="form-control" min="1900" max="2100">
                  </div>
                  <div class="col-6">
                    <label class="form-label">TARA (Kgs)</label>
                    <input type="number" name="semi_trasero_tara" id="semi_trasero_tara" class="form-control" step="0.001" min="0">
                  </div>
                  <div class="col-6">
                    <label class="form-label">PBT (Kgs)</label>
                    <input type="number" name="semi_trasero_pbt" id="semi_trasero_pbt" class="form-control" step="0.001" min="0">
                  </div>
                </div>
              </div>
            </div>
            <?php if ($vista->mostrar_totales): ?>
            <div class="col-12 mt-3 section-totales">
              <h6 class="section-title mb-2">Totales</h6>
              <div class="row g-3">
                <div class="col-12 col-md-4">
                  <label class="form-label">TARA TOTAL (Kgs)</label>
                  <input type="number" name="tara_total" id="tara_total" class="form-control" step="0.001" min="0" readonly>
                </div>
                <div class="col-12 col-md-4">
                  <label class="form-label">PESO MÁXIMO (Kgs)</label>
                  <input type="number" name="peso_maximo" id="peso_maximo" class="form-control" step="0.001" min="0">
                </div>
              </div>
            </div>
            <?php endif; ?>
          </div>
          <?php else: ?>
          <input type="hidden" name="patente_semi_delantero" id="patente_semi_delantero">
          <input type="hidden" name="patente_tractor" id="patente_tractor">
          <input type="hidden" name="patente_semi_trasero" id="patente_semi_trasero">
          <input type="hidden" name="bitren" id="bitren" value="NO">
          <div class="row g-3 mb-2">
            <div class="col-12 col-md-4">
              <label class="form-label">Patente (semi) <span class="text-danger">(*)</span></label>
              <input type="text" name="patente_semi_delantero" id="patente_semi_delantero" class="form-control" required>
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Pat. Tractor <span class="text-danger">(*)</span></label>
              <input type="text" name="patente_tractor" id="patente_tractor" class="form-control" required>
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Bitren</label>
              <select name="bitren" id="bitren" class="form-select"><option value="NO">NO</option><option value="SI">SI</option></select>
            </div>
            <div class="col-12 col-md-4" id="wrap-patente_semi_trasero">
              <label class="form-label">Patente semi trasero</label>
              <input type="text" name="patente_semi_trasero" id="patente_semi_trasero" class="form-control">
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">Transportista <span class="text-danger">(*)</span></label>
              <select name="id_tta" id="id_tta" class="form-select" required>
                <option value="">Seleccione...</option>
                <?php if (isset($transportistas) && is_array($transportistas)): foreach ($transportistas as $trans): ?>
                <option value="<?= $trans['id_tta'] ?>"><?= esc($trans['transportista']) ?></option>
                <?php endforeach; endif; ?>
              </select>
            </div>
          </div>
          <?php endif; ?>

          <?php if ($vista && $vista->mostrar_totales && !$vista->mostrar_seccion_vehiculos): ?>
          <div class="row g-3 mb-4 section-totales">
            <h6 class="section-title">Totales</h6>
            <div class="col-12 col-md-4">
              <label class="form-label">TARA TOTAL (Kgs)</label>
              <input type="number" name="tara_total" id="tara_total" class="form-control" step="0.001" min="0" readonly>
            </div>
            <div class="col-12 col-md-4">
              <label class="form-label">PESO MÁXIMO (Kgs)</label>
              <input type="number" name="peso_maximo" id="peso_maximo" class="form-control" step="0.001" min="0">
            </div>
          </div>
          <?php endif; ?>

          <?php if ($vista && $vista->mostrar_cisternas): ?>
          <div class="row g-3 mb-4 section-cisternas">
            <h6 class="section-title">Capacidades de Cisternas (Lts)</h6>
            <div class="col-12">
              <div class="row g-2">
                <?php for ($i = 1; $i <= 10; $i++): ?>
                <div class="col-6 col-md-4 col-lg-2">
                  <label class="form-label">C<?= $i ?></label>
                  <input type="number" name="cisterna_<?= $i ?>_capacidad" id="cisterna_<?= $i ?>_capacidad" class="form-control cisterna-capacidad" step="0.01" min="0" value="0">
                </div>
                <?php endfor; ?>
                <div class="col-12 col-md-4">
                  <label class="form-label">CAP. TOTAL (Lts)</label>
                  <input type="number" name="capacidad_total" id="capacidad_total" class="form-control" step="0.01" min="0" readonly>
                </div>
              </div>
            </div>
            <h6 class="section-title mt-3">Tipos de Checklist</h6>
            <div class="col-12">
              <div class="row g-2">
                <?php
                $checklistCampos = [
                  'checklist_asfalto'   => 'Asfalto',
                  'checklist_alcohol'   => 'Alcohol',
                  'checklist_biodiesel' => 'Biodiesel',
                  'checklist_comb_liv'  => 'Comb. Liv.',
                  'checklist_comb_pes'  => 'Comb. Pes.',
                  'checklist_solvente'  => 'Solvente',
                  'checklist_coke'      => 'Coke',
                  'checklist_lubes_gra' => 'Lubes Gra.',
                  'checklist_lubes_env' => 'Lubes Env.',
                  'checklist_glp'       => 'GLP',
                ];
                foreach ($checklistCampos as $name => $label):
                ?>
                <div class="col-6 col-md-4 col-lg-2">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="<?= $name ?>" id="<?= $name ?>" value="1">
                    <label class="form-check-label" for="<?= $name ?>"><?= esc($label) ?></label>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if ($vista && $vista->mostrar_cubiertas_cotas): ?>
          <div class="row g-3 mb-4 align-items-start">
            <h6 class="section-title">Ejes y cubiertas</h6>
            <div class="col-12 col-md-4">
              <div class="vehicle-section-card">
                <h6 class="text-primary mb-3">Tractor</h6>
                <div class="row g-2">
                  <div class="col-12">
                    <label class="form-label">Ejes (0–3)</label>
                    <select name="ejes_tractor" id="ejes_tractor" class="form-select ejes-input">
                      <option value="0">0</option>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                    </select>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Cub. E1</label>
                    <select name="cubierta_tractor_eje1" id="cubierta_tractor_eje1" class="form-select cubierta-eje" disabled>
                      <option value="">—</option>
                      <?php if (isset($cubiertas) && is_array($cubiertas)): foreach ($cubiertas as $c): ?>
                      <option value="<?= $c['id_cubierta'] ?>"><?= esc($c['medida']) ?></option>
                      <?php endforeach; endif; ?>
                    </select>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Cub. E2</label>
                    <select name="cubierta_tractor_eje2" id="cubierta_tractor_eje2" class="form-select cubierta-eje" disabled>
                      <option value="">—</option>
                      <?php if (isset($cubiertas) && is_array($cubiertas)): foreach ($cubiertas as $c): ?>
                      <option value="<?= $c['id_cubierta'] ?>"><?= esc($c['medida']) ?></option>
                      <?php endforeach; endif; ?>
                    </select>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Cub. E3</label>
                    <select name="cubierta_tractor_eje3" id="cubierta_tractor_eje3" class="form-select cubierta-eje" disabled>
                      <option value="">—</option>
                      <?php if (isset($cubiertas) && is_array($cubiertas)): foreach ($cubiertas as $c): ?>
                      <option value="<?= $c['id_cubierta'] ?>"><?= esc($c['medida']) ?></option>
                      <?php endforeach; endif; ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="vehicle-section-card">
                <h6 class="text-primary mb-3">Semi Delantera</h6>
                <div class="row g-2">
                  <div class="col-12">
                    <label class="form-label">Ejes (0–3)</label>
                    <select name="ejes_semi_delantero" id="ejes_semi_delantero" class="form-select ejes-input">
                      <option value="0">0</option>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                    </select>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Cub. E1</label>
                    <select name="cubierta_semi_delantero_eje1" id="cubierta_semi_delantero_eje1" class="form-select cubierta-eje" disabled>
                      <option value="">—</option>
                      <?php if (isset($cubiertas) && is_array($cubiertas)): foreach ($cubiertas as $c): ?>
                      <option value="<?= $c['id_cubierta'] ?>"><?= esc($c['medida']) ?></option>
                      <?php endforeach; endif; ?>
                    </select>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Cub. E2</label>
                    <select name="cubierta_semi_delantero_eje2" id="cubierta_semi_delantero_eje2" class="form-select cubierta-eje" disabled>
                      <option value="">—</option>
                      <?php if (isset($cubiertas) && is_array($cubiertas)): foreach ($cubiertas as $c): ?>
                      <option value="<?= $c['id_cubierta'] ?>"><?= esc($c['medida']) ?></option>
                      <?php endforeach; endif; ?>
                    </select>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Cub. E3</label>
                    <select name="cubierta_semi_delantero_eje3" id="cubierta_semi_delantero_eje3" class="form-select cubierta-eje" disabled>
                      <option value="">—</option>
                      <?php if (isset($cubiertas) && is_array($cubiertas)): foreach ($cubiertas as $c): ?>
                      <option value="<?= $c['id_cubierta'] ?>"><?= esc($c['medida']) ?></option>
                      <?php endforeach; endif; ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="vehicle-section-card">
                <h6 class="text-primary mb-3">Semi Trasero</h6>
                <div class="row g-2">
                  <div class="col-12">
                    <label class="form-label">Ejes (0–3)</label>
                    <select name="ejes_semi_trasero" id="ejes_semi_trasero" class="form-select ejes-input">
                      <option value="0">0</option>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                    </select>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Cub. E1</label>
                    <select name="cubierta_semi_trasero_eje1" id="cubierta_semi_trasero_eje1" class="form-select cubierta-eje" disabled>
                      <option value="">—</option>
                      <?php if (isset($cubiertas) && is_array($cubiertas)): foreach ($cubiertas as $c): ?>
                      <option value="<?= $c['id_cubierta'] ?>"><?= esc($c['medida']) ?></option>
                      <?php endforeach; endif; ?>
                    </select>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Cub. E2</label>
                    <select name="cubierta_semi_trasero_eje2" id="cubierta_semi_trasero_eje2" class="form-select cubierta-eje" disabled>
                      <option value="">—</option>
                      <?php if (isset($cubiertas) && is_array($cubiertas)): foreach ($cubiertas as $c): ?>
                      <option value="<?= $c['id_cubierta'] ?>"><?= esc($c['medida']) ?></option>
                      <?php endforeach; endif; ?>
                    </select>
                  </div>
                  <div class="col-12">
                    <label class="form-label">Cub. E3</label>
                    <select name="cubierta_semi_trasero_eje3" id="cubierta_semi_trasero_eje3" class="form-select cubierta-eje" disabled>
                      <option value="">—</option>
                      <?php if (isset($cubiertas) && is_array($cubiertas)): foreach ($cubiertas as $c): ?>
                      <option value="<?= $c['id_cubierta'] ?>"><?= esc($c['medida']) ?></option>
                      <?php endforeach; endif; ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12 mt-2">
              <h6 class="section-title section-cotas mb-2">Dimensiones (cotas)</h6>
              <div class="row g-3">
                <div class="col-12 col-md-3">
                  <label class="form-label">Cota del. (mm)</label>
                  <input type="number" name="cota_delantero" id="cota_delantero" class="form-control" step="0.01" min="0">
                </div>
                <div class="col-12 col-md-3">
                  <label class="form-label">Cota tras. (mm)</label>
                  <input type="number" name="cota_trasero" id="cota_trasero" class="form-control" step="0.01" min="0">
                </div>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if ($vista && $vista->mostrar_observaciones): ?>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Observaciones</label>
              <textarea name="observaciones" id="observaciones" class="form-control" rows="2"></textarea>
            </div>
          </div>
          <?php endif; ?>
        </form>
      </div>
      <div class="modal-footer">
        <button id="btn-guardar-equipo" class="btn btn-primary btn-tablet" type="button">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-save" viewBox="0 0 16 16">
            <path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2z"/>
          </svg>
          Guardar
        </button>
        <button class="btn btn-secondary btn-tablet" type="button" data-bs-dismiss="modal">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
          </svg>
          Cancelar
        </button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
  var table;
  var modal = new bootstrap.Modal(document.getElementById('modal-equipo'));
  
  // Inicializar DataTable
  table = $('#equipos-datatable').DataTable({
    language: {
      url: '<?= base_url('assets/js/datatable/esp.json') ?>'
    },
    processing: true,
    serverSide: false,
    responsive: true,
    pageLength: 10,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
    ajax: {
      url: '<?= site_url('equipos/listar') ?>',
      type: 'POST',
      data: function(d) {
        d.id_tta = $('#filtro-transportista').val() || '';
        d.patente_tractor = $('#filtro-patente-tractor').val() || '';
        d.patente_semi_delantero = $('#filtro-patente-semi1').val() || '';
        d.patente_semi_trasero = $('#filtro-patente-semi2').val() || '';
        return d;
      }
    },
    columns: [
      { data: 'patente_tractor', responsivePriority: 1 },
      { data: 'patente_semi_delantero', responsivePriority: 2 },
      { data: 'patente_semi_trasero', responsivePriority: 3 },
      { data: 'transportista', responsivePriority: 4 },
      { data: 'bitren', responsivePriority: 5 },
      {
        data: 'capacidad_total',
        render: function(data) {
          return data && data !== '-' ? parseFloat(data).toLocaleString('es-AR') + ' Lts' : '-';
        },
        responsivePriority: 6
      },
      {
        data: 'tara_total',
        render: function(data) {
          return data && data !== '-' ? parseFloat(data).toLocaleString('es-AR') + ' Kgs' : '-';
        },
        responsivePriority: 7
      },
      {
        data: 'created_at',
        responsivePriority: 8,
        render: function(data, type) {
          if (!data) return '-';
          if (type === 'sort') return data;
          var d = new Date(data);
          if (isNaN(d.getTime())) return data;
          var day = ('0' + d.getDate()).slice(-2);
          var month = ('0' + (d.getMonth() + 1)).slice(-2);
          var year = d.getFullYear();
          return day + '/' + month + '/' + year;
        }
      },
      {
        data: null,
        orderable: false,
        className: 'text-center',
        responsivePriority: 1,
        render: function(data, type, row) {
          return '<div class="btn-group" role="group">' +
                   '<a href="<?= site_url('equipos/ver/') ?>' + row.id_equipo + '" class="btn btn-sm btn-outline-secondary btn-tablet" title="Ver equipo">' +
                     '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">' +
                       '<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>' +
                       '<path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>' +
                     '</svg></a>' +
                   '<button class="btn btn-sm btn-primary btn-tablet editar-equipo" data-id="' + row.id_equipo + '" title="Editar">' +
                     '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">' +
                       '<path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>' +
                     '</svg>' +
                   '</button>' +
                   '<button class="btn btn-sm btn-danger btn-tablet eliminar-equipo" data-id="' + row.id_equipo + '" title="Eliminar">' +
                     '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">' +
                       '<path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>' +
                       '<path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>' +
                     '</svg>' +
                   '</button>' +
                 '</div>';
        }
      }
    ],
    order: [[7, 'desc']]
  });

  $('#btn-filtrar-equipos').on('click', function() { table.ajax.reload(); });
  $('#btn-limpiar-filtros-equipos').on('click', function() {
    $('#filtro-transportista').val('');
    $('#filtro-patente-tractor').val('');
    $('#filtro-patente-semi1').val('');
    $('#filtro-patente-semi2').val('');
    table.ajax.reload();
  });
  $('#filtro-transportista').on('change', function() { table.ajax.reload(); });
  $('#filtro-patente-tractor, #filtro-patente-semi1, #filtro-patente-semi2').on('keypress', function(e) {
    if (e.which === 13) { e.preventDefault(); table.ajax.reload(); }
  });

  function calcularTaraTotal() {
    var t = (parseFloat($('#tractor_tara').val()) || 0) + (parseFloat($('#semi_delantero_tara').val()) || 0) + (parseFloat($('#semi_trasero_tara').val()) || 0);
    $('#tara_total').val(t.toFixed(3));
  }
  function calcularCapacidadTotal() {
    var c = 0;
    for (var i = 1; i <= 10; i++) c += (parseFloat($('#cisterna_' + i + '_capacidad').val()) || 0);
    $('#capacidad_total').val(c.toFixed(2));
  }
  $('#tractor_tara, #semi_delantero_tara, #semi_trasero_tara').on('input', calcularTaraTotal);
  $('.cisterna-capacidad').on('input', calcularCapacidadTotal);

  function togglePatenteSemiTrasero(show) {
    $('#wrap-patente_semi_trasero').toggle(show);
    if (!show) $('#patente_semi_trasero').val('');
  }
  $('#bitren').on('change', function() {
    togglePatenteSemiTrasero($(this).val() === 'SI');
  });
  if ($('#wrap-patente_semi_trasero').length) togglePatenteSemiTrasero($('#bitren').val() === 'SI');

  function actualizarCubiertasPorEjes(ejesInputId, cubiertaIds) {
    var n = parseInt($('#' + ejesInputId).val(), 10);
    if (isNaN(n) || n < 0) n = 0;
    if (n > 3) n = 3;
    for (var i = 0; i < 3; i++) {
      var el = $('#' + cubiertaIds[i]);
      if (i < n) {
        el.prop('disabled', false);
      } else {
        el.prop('disabled', true);
        el.val('');
      }
    }
  }
  var gruposCubiertas = [
    { ejes: 'ejes_tractor', cubiertas: ['cubierta_tractor_eje1', 'cubierta_tractor_eje2', 'cubierta_tractor_eje3'] },
    { ejes: 'ejes_semi_delantero', cubiertas: ['cubierta_semi_delantero_eje1', 'cubierta_semi_delantero_eje2', 'cubierta_semi_delantero_eje3'] },
    { ejes: 'ejes_semi_trasero', cubiertas: ['cubierta_semi_trasero_eje1', 'cubierta_semi_trasero_eje2', 'cubierta_semi_trasero_eje3'] }
  ];
  gruposCubiertas.forEach(function(g) {
    actualizarCubiertasPorEjes(g.ejes, g.cubiertas);
    $('#' + g.ejes).on('input change', function() { actualizarCubiertasPorEjes(g.ejes, g.cubiertas); });
  });

  $('#btn-nuevo-equipo').on('click', function() {
    limpiarFormulario();
    $('#modal-title').text('Agregar equipo');
    if ($('#wrap-patente_semi_trasero').length) togglePatenteSemiTrasero($('#bitren').val() === 'SI');
    gruposCubiertas.forEach(function(g) { actualizarCubiertasPorEjes(g.ejes, g.cubiertas); });
    calcularTaraTotal();
    calcularCapacidadTotal();
  });

  $(document).on('click', '.editar-equipo', function() {
    var id = $(this).data('id');
    $.ajax({
      url: '<?= site_url('equipos/obtener') ?>/' + id,
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        if (response.success && response.data) {
          var u = response.data;
          $('#id_equipo').val(u.id_equipo);
          $('#patente_semi_delantero').val(u.patente_semi_delantero);
          $('#patente_tractor').val(u.patente_tractor || '');
          $('#tractor_anio_modelo').val(u.tractor_anio_modelo || '');
          $('#bitren').val(u.bitren || 'NO');
          $('#patente_semi_trasero').val(u.patente_semi_trasero || '');
          $('#id_tta').val(u.id_tta || '');
          $('#fecha_alta').val(u.fecha_alta ? (u.fecha_alta.split && u.fecha_alta.split('/').length === 3 ? u.fecha_alta.split('/').reverse().join('-') : u.fecha_alta) : '');
          $('#modo_carga').val(u.modo_carga || '');
          $('#pais_id').val(u.pais_id || '');
          $('#tractor_tara').val(u.tractor_tara || '');
          $('#tractor_pbt').val(u.tractor_pbt || '');
          $('#semi_delantero_tara').val(u.semi_delantero_tara || '');
          $('#semi_delantera_pbt').val(u.semi_delantera_pbt || '');
          $('#semi_trasero_anio_modelo').val(u.semi_trasero_anio_modelo || '');
          $('#semi_trasero_tara').val(u.semi_trasero_tara || '');
          $('#semi_trasero_pbt').val(u.semi_trasero_pbt || '');
          $('#tara_total').val(u.tara_total || '');
          $('#peso_maximo').val(u.peso_maximo || '');
          for (var i = 1; i <= 10; i++) $('#cisterna_' + i + '_capacidad').val(u['cisterna_' + i + '_capacidad'] || 0);
          $('#capacidad_total').val(u.capacidad_total || '');
          var checklistCampos = ['checklist_asfalto','checklist_alcohol','checklist_biodiesel','checklist_comb_liv','checklist_comb_pes','checklist_solvente','checklist_coke','checklist_lubes_gra','checklist_lubes_env','checklist_glp'];
          checklistCampos.forEach(function(campo) {
            $('#' + campo).prop('checked', u[campo] == 1 || u[campo] === true);
          });
          $('#id_bandera').val(u.id_bandera || '');
          $('#id_marca').val(u.id_marca || '');
          $('#semi_delantero_anio_modelo').val(u.semi_delantero_anio_modelo || '');
          $('#ejes_tractor').val(u.ejes_tractor || '');
          $('#ejes_semi_delantero').val(u.ejes_semi_delantero || '');
          $('#cubierta_tractor_eje1').val(u.cubierta_tractor_eje1 || '');
          $('#cubierta_tractor_eje2').val(u.cubierta_tractor_eje2 || '');
          $('#cubierta_tractor_eje3').val(u.cubierta_tractor_eje3 || '');
          $('#cubierta_semi_delantero_eje1').val(u.cubierta_semi_delantero_eje1 || '');
          $('#cubierta_semi_delantero_eje2').val(u.cubierta_semi_delantero_eje2 || '');
          $('#cubierta_semi_delantero_eje3').val(u.cubierta_semi_delantero_eje3 || '');
          $('#ejes_semi_trasero').val(u.ejes_semi_trasero || '');
          $('#cubierta_semi_trasero_eje1').val(u.cubierta_semi_trasero_eje1 || '');
          $('#cubierta_semi_trasero_eje2').val(u.cubierta_semi_trasero_eje2 || '');
          $('#cubierta_semi_trasero_eje3').val(u.cubierta_semi_trasero_eje3 || '');
          $('#cota_delantero').val(u.cota_delantero || '');
          $('#cota_trasero').val(u.cota_trasero || '');
          $('#observaciones').val(u.observaciones || '');
          if ($('#wrap-patente_semi_trasero').length) togglePatenteSemiTrasero(u.bitren === 'SI');
          gruposCubiertas.forEach(function(g) { actualizarCubiertasPorEjes(g.ejes, g.cubiertas); });
          calcularTaraTotal();
          calcularCapacidadTotal();
          $('#modal-title').text('Editar equipo');
          modal.show();
        } else {
          Swal.fire('Error', response.message || 'No se pudo cargar el equipo', 'error');
        }
      },
      error: function() {
        Swal.fire('Error', 'Error al cargar el equipo', 'error');
      }
    });
  });

  // Eliminar equipo
  $(document).on('click', '.eliminar-equipo', function() {
    var id = $(this).data('id');
    
    Swal.fire({
      title: '¿Estás seguro?',
      text: "Esta acción no se puede deshacer",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: '<?= site_url('equipos/eliminar') ?>/' + id,
          type: 'POST',
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              Swal.fire('Eliminado', response.message, 'success');
              table.ajax.reload();
            } else {
              Swal.fire('Error', response.message, 'error');
            }
          },
          error: function() {
            Swal.fire('Error', 'Error al eliminar el equipo', 'error');
          }
        });
      }
    });
  });

  $('#btn-guardar-equipo').on('click', function() {
    var formData = {
      id_equipo: $('#id_equipo').val(),
      patente_semi_delantero: $('#patente_semi_delantero').val(),
      id_tta: $('#id_tta').val() || null,
      patente_tractor: $('#patente_tractor').val() || null,
      bitren: $('#bitren').val() || 'NO',
      patente_semi_trasero: $('#bitren').val() === 'SI' ? ($('#patente_semi_trasero').val() || null) : null,
      fecha_alta: $('#fecha_alta').val() || null,
      modo_carga: $('#modo_carga').val() || null,
      pais_id: $('#pais_id').val() || null,
      tractor_tara: $('#tractor_tara').val() || null,
      tractor_pbt: $('#tractor_pbt').val() || null,
      tractor_anio_modelo: $('#tractor_anio_modelo').val() || null,
      semi_delantero_tara: $('#semi_delantero_tara').val() || null,
      semi_delantera_pbt: $('#semi_delantera_pbt').val() || null,
      semi_trasero_anio_modelo: $('#semi_trasero_anio_modelo').val() || null,
      semi_trasero_tara: $('#semi_trasero_tara').val() || null,
      semi_trasero_pbt: $('#semi_trasero_pbt').val() || null,
      tara_total: $('#tara_total').val() || null,
      peso_maximo: $('#peso_maximo').val() || null,
      id_bandera: $('#id_bandera').val() || null,
      id_marca: $('#id_marca').val() || null,
      semi_delantero_anio_modelo: $('#semi_delantero_anio_modelo').val() || null,
      cubierta_tractor_eje1: $('#cubierta_tractor_eje1').val() || null,
      cubierta_tractor_eje2: $('#cubierta_tractor_eje2').val() || null,
      cubierta_tractor_eje3: $('#cubierta_tractor_eje3').val() || null,
      ejes_tractor: $('#ejes_tractor').val() || null,
      cubierta_semi_delantero_eje1: $('#cubierta_semi_delantero_eje1').val() || null,
      cubierta_semi_delantero_eje2: $('#cubierta_semi_delantero_eje2').val() || null,
      cubierta_semi_delantero_eje3: $('#cubierta_semi_delantero_eje3').val() || null,
      ejes_semi_delantero: $('#ejes_semi_delantero').val() || null,
      ejes_semi_trasero: $('#ejes_semi_trasero').val() || null,
      cubierta_semi_trasero_eje1: $('#cubierta_semi_trasero_eje1').val() || null,
      cubierta_semi_trasero_eje2: $('#cubierta_semi_trasero_eje2').val() || null,
      cubierta_semi_trasero_eje3: $('#cubierta_semi_trasero_eje3').val() || null,
      cota_delantero: $('#cota_delantero').val() || null,
      cota_trasero: $('#cota_trasero').val() || null,
      observaciones: $('#observaciones').val() || null
    };
    for (var i = 1; i <= 10; i++) formData['cisterna_' + i + '_capacidad'] = $('#cisterna_' + i + '_capacidad').val() || null;
    formData.capacidad_total = $('#capacidad_total').val() || null;
    var checklistCampos = ['checklist_asfalto','checklist_alcohol','checklist_biodiesel','checklist_comb_liv','checklist_comb_pes','checklist_solvente','checklist_coke','checklist_lubes_gra','checklist_lubes_env','checklist_glp'];
    checklistCampos.forEach(function(campo) {
      formData[campo] = $('#' + campo).is(':checked') ? 1 : 0;
    });

    // Limpiar errores previos
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');

    $.ajax({
      url: '<?= site_url('equipos/guardar') ?>',
      type: 'POST',
      contentType: 'application/json',
      data: JSON.stringify(formData),
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          Swal.fire('Éxito', response.message, 'success');
          modal.hide();
          limpiarFormulario();
          table.ajax.reload();
        } else {
          // Mostrar errores de validación
          if (response.errors) {
            $.each(response.errors, function(field, message) {
              $('#' + field).addClass('is-invalid');
              $('#' + field + '-error').text(message);
            });
          } else {
            Swal.fire('Error', response.message || 'Error al guardar el equipo', 'error');
          }
        }
      },
      error: function(xhr) {
        var errorMsg = 'Error al guardar el equipo';
        if (xhr.responseJSON && xhr.responseJSON.message) {
          errorMsg = xhr.responseJSON.message;
        }
        Swal.fire('Error', errorMsg, 'error');
      }
    });
  });

  function limpiarFormulario() {
    $('#form-equipo')[0].reset();
    $('#id_equipo').val('');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    calcularTaraTotal();
    calcularCapacidadTotal();
  }

  // Limpiar formulario al cerrar modal
  $('#modal-equipo').on('hidden.bs.modal', function() {
    limpiarFormulario();
  });
});
</script>
<?= $this->endSection() ?>
