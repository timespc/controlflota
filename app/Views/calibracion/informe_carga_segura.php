<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
<?= esc($titulo) ?>
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
  .informe-section { margin-bottom: 1.5rem; }
  .informe-section h6 { color: #0d6efd; font-weight: bold; margin-bottom: 0.5rem; border-bottom: 2px solid #dee2e6; padding-bottom: 0.25rem; }
  .informe-table { font-size: 0.9rem; }
  .informe-table th { background: #e7f1ff; }
  .responsable-cargo-block { max-width: 22rem; }
</style>
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<?php
$idCal = (int) ($cal['id_calibracion'] ?? 0);
$informe = $informe ?? [];
$detalle = $detalle ?? [];
$detallePorCisterna = [];
foreach ($detalle as $d) {
    $detallePorCisterna[(int)$d['numero_cisterna']] = $d;
}
$numCisternas = (int) ($numCisternas ?? 0);
$numCisternasSemi1 = (int) ($numCisternasSemi1 ?? $numCisternas);
$numCisternasSemi2 = (int) ($numCisternasSemi2 ?? 0);
$detalleCalib = $detalleCalib ?? [];
$marcasSensor = $marcasSensor ?? [];
$marcasSensorValores = array_column($marcasSensor, 'marca');
$tieneDosSemis = $numCisternasSemi2 > 0;
$bloques = $tieneDosSemis
    ? [['titulo' => 'SEMI 1', 'inicio' => 1, 'fin' => $numCisternasSemi1], ['titulo' => 'SEMI 2', 'inicio' => $numCisternasSemi1 + 1, 'fin' => $numCisternas]]
    : [['titulo' => '', 'inicio' => 1, 'fin' => $numCisternas]];
?>
<div class="row mb-3">
  <div class="col-12">
    <a href="<?= site_url('calibracion') ?>" class="btn btn-secondary btn-sm me-2">Volver a Calibraciones</a>
    <?php if (function_exists('es_admin') && es_admin()): ?>
    <a href="<?= site_url('calibracion/imprimir-informe-carga-segura/' . $idCal) ?>" class="btn btn-primary btn-sm">Imprimir Informe</a>
    <?php endif; ?>
  </div>
</div>

<div class="card border border-secondary">
  <div class="card-header text-white bg-secondary">
    <h5 class="mb-0">Informe de Carga Segura - Calibración N° <?= $idCal ?></h5>
    <small>Transportista: <?= esc($cal['transportista'] ?? '-') ?><?php if (!empty($cal['transportista_cuit'])): ?> (<?= esc($cal['transportista_cuit']) ?>)<?php endif; ?> | Patente: <?= esc($cal['patente'] ?? '-') ?></small>
  </div>
  <div class="card-body">
    <form id="form-informe-carga-segura" method="post" action="<?= site_url('calibracion/guardar-informe-carga-segura/' . $idCal) ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="num_cisternas" value="<?= $numCisternas ?>">

      <?php foreach ($bloques as $bloque): ?>
      <div class="informe-section <?= $bloque['titulo'] ? 'border-top pt-3 mt-3' : '' ?>">
        <?php if ($bloque['titulo']): ?><h5 class="text-secondary mb-3"><?= esc($bloque['titulo']) ?></h5><?php endif; ?>
        <h6>CONTROL VACÍO EN CISTERNAS</h6>
        <div class="table-responsive">
          <table class="table table-bordered table-sm informe-table">
            <thead>
              <tr>
                <th>CISTERNA N°</th>
                <th>VOLUMEN (lts)</th>
                <th>VACÍO REQUERIDO</th>
                <th>VACÍO MEDIDO</th>
                <th>ACCIÓN TOMADA</th>
                <th>VOLUMEN FINAL (lts)</th>
                <th>CUMPLE (SI/NO)</th>
              </tr>
            </thead>
            <tbody>
              <?php for ($n = $bloque['inicio']; $n <= $bloque['fin']; $n++): $d = $detallePorCisterna[$n] ?? []; $numLocal = $n - $bloque['inicio'] + 1; ?>
              <tr>
                <td class="text-center"><?= $numLocal ?></td>
                <td><input type="number" step="0.01" name="cisterna_<?= $n ?>_volumen" class="form-control form-control-sm" value="<?= esc($d['volumen_lts'] ?? ($detalleCalib[$n - 1]['capacidad'] ?? '')) ?>"></td>
                <td><input type="number" step="0.01" name="cisterna_<?= $n ?>_vacio_requerido" class="form-control form-control-sm" value="<?= esc($d['vacio_requerido'] ?? '') ?>"></td>
                <td><input type="number" step="0.01" name="cisterna_<?= $n ?>_vacio_medido" class="form-control form-control-sm" value="<?= esc($d['vacio_medido'] ?? '') ?>"></td>
                <td><input type="text" name="cisterna_<?= $n ?>_accion_tomada" class="form-control form-control-sm" value="<?= esc($d['accion_tomada'] ?? '') ?>" placeholder="N/A"></td>
                <td><input type="number" step="0.01" name="cisterna_<?= $n ?>_volumen_final" class="form-control form-control-sm" value="<?= esc($d['volumen_final_lts'] ?? '') ?>"></td>
                <td>
                  <select name="cisterna_<?= $n ?>_cumple_control" class="form-select form-select-sm">
                    <option value="">—</option>
                    <option value="SI" <?= ($d['cumple_control'] ?? '') === 'SI' ? 'selected' : '' ?>>SI</option>
                    <option value="NO" <?= ($d['cumple_control'] ?? '') === 'NO' ? 'selected' : '' ?>>NO</option>
                  </select>
                </td>
              </tr>
              <?php endfor; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 informe-section">
          <h6>TRAZABILIDAD SENSORES</h6>
          <div class="table-responsive">
            <table class="table table-bordered table-sm informe-table">
              <thead>
                <tr>
                  <th>CISTERNA N°</th>
                  <th>MARCA</th>
                  <th>N° DE SERIE</th>
                  <th>CUMPLE (SI/NO)</th>
                </tr>
              </thead>
              <tbody>
                <?php for ($n = $bloque['inicio']; $n <= $bloque['fin']; $n++): $d = $detallePorCisterna[$n] ?? []; $marcaActual = trim((string)($d['marca_sensor'] ?? '')); $numLocal = $n - $bloque['inicio'] + 1; ?>
                <tr>
                  <td class="text-center"><?= $numLocal ?></td>
                  <td>
                    <select name="cisterna_<?= $n ?>_marca_sensor" class="form-select form-select-sm">
                      <option value="">—</option>
                      <?php foreach ($marcasSensor as $ms): ?>
                      <option value="<?= esc($ms['marca']) ?>" <?= $marcaActual === $ms['marca'] ? 'selected' : '' ?>><?= esc($ms['marca']) ?></option>
                      <?php endforeach; ?>
                      <?php if ($marcaActual !== '' && ! in_array($marcaActual, $marcasSensorValores, true)): ?>
                      <option value="<?= esc($marcaActual) ?>" selected><?= esc($marcaActual) ?></option>
                      <?php endif; ?>
                    </select>
                  </td>
                  <td><input type="text" name="cisterna_<?= $n ?>_numero_serie_sensor" class="form-control form-control-sm" value="<?= esc($d['numero_serie_sensor'] ?? '') ?>"></td>
                  <td>
                    <select name="cisterna_<?= $n ?>_cumple_trazabilidad" class="form-select form-select-sm">
                      <option value="">—</option>
                      <option value="SI" <?= ($d['cumple_trazabilidad'] ?? '') === 'SI' ? 'selected' : '' ?>>SI</option>
                      <option value="NO" <?= ($d['cumple_trazabilidad'] ?? '') === 'NO' ? 'selected' : '' ?>>NO</option>
                    </select>
                  </td>
                </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </div>
        </div>
        <div class="col-md-6 informe-section">
          <h6>POSICIÓN SENSORES</h6>
          <div class="table-responsive">
            <table class="table table-bordered table-sm informe-table">
              <thead>
                <tr>
                  <th>CUMPLE (SI/NO)</th>
                  <th>OBSERVACIÓN</th>
                  <th>LITROS SENSOR/REBALSE</th>
                </tr>
              </thead>
              <tbody>
                <?php for ($n = $bloque['inicio']; $n <= $bloque['fin']; $n++): $d = $detallePorCisterna[$n] ?? []; $numLocal = $n - $bloque['inicio'] + 1; ?>
                <tr>
                  <td>
                    <select name="cisterna_<?= $n ?>_cumple_posicion" class="form-select form-select-sm">
                      <option value="">—</option>
                      <option value="SI" <?= ($d['cumple_posicion'] ?? '') === 'SI' ? 'selected' : '' ?>>SI</option>
                      <option value="NO" <?= ($d['cumple_posicion'] ?? '') === 'NO' ? 'selected' : '' ?>>NO</option>
                    </select>
                  </td>
                  <td>
                    <div class="input-group input-group-sm">
                      <input type="text" name="cisterna_<?= $n ?>_observacion_posicion" class="form-control form-control-sm" value="<?= esc($d['observacion_posicion'] ?? '') ?>" placeholder="Ej: 20">
                      <span class="input-group-text">mm</span>
                    </div>
                  </td>
                  <td>
                    <div class="input-group input-group-sm">
                      <input type="text" name="cisterna_<?= $n ?>_litros_sensor_rebalse" class="form-control form-control-sm" value="<?= esc($d['litros_sensor_rebalse'] ?? '') ?>" placeholder="Ej: 208">
                      <span class="input-group-text">Lts</span>
                    </div>
                  </td>
                </tr>
                <?php endfor; ?>
              </tbody>
            </table>
          </div>
          <?php if ($bloque['titulo'] === '' || $bloque['titulo'] === 'SEMI 2'): ?>
          <div class="mt-2">
            <label class="form-label small text-muted mb-1">Nota (debajo de Posición sensores en la impresión)</label>
            <textarea name="nota_posicion_sensores" class="form-control form-control-sm" rows="2" placeholder="Ej: * No existe la posibilidad de posicionar el sensor de sobrellenado por debajo del valor mencionado."><?= esc($informe['nota_posicion_sensores'] ?? '') ?></textarea>
          </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>

      <div class="informe-section mt-3">
        <h6>RESULTADO FINAL DE AUDITORÍA - CUMPLE SI/NO</h6>
        <div class="resultado-auditoria-lista">
          <div class="d-flex align-items-center gap-2 mb-2">
            <label class="form-label small mb-0 col-form-label" style="min-width: 12rem;">Control vacío cisternas:</label>
            <select name="resultado_control_vacio" class="form-select form-select-sm" style="max-width: 6rem;">
              <option value="">—</option>
              <option value="SI" <?= ($informe['resultado_control_vacio'] ?? '') === 'SI' ? 'selected' : '' ?>>SI</option>
              <option value="NO" <?= ($informe['resultado_control_vacio'] ?? '') === 'NO' ? 'selected' : '' ?>>NO</option>
            </select>
          </div>
          <div class="d-flex align-items-center gap-2 mb-2">
            <label class="form-label small mb-0 col-form-label" style="min-width: 12rem;">Trazabilidad de sensores:</label>
            <select name="resultado_trazabilidad" class="form-select form-select-sm" style="max-width: 6rem;">
              <option value="">—</option>
              <option value="SI" <?= ($informe['resultado_trazabilidad'] ?? '') === 'SI' ? 'selected' : '' ?>>SI</option>
              <option value="NO" <?= ($informe['resultado_trazabilidad'] ?? '') === 'NO' ? 'selected' : '' ?>>NO</option>
            </select>
          </div>
          <div class="d-flex align-items-center gap-2 mb-2">
            <label class="form-label small mb-0 col-form-label" style="min-width: 12rem;">Posición de sensores:</label>
            <select name="resultado_posicion" class="form-select form-select-sm" style="max-width: 6rem;">
              <option value="">—</option>
              <option value="SI" <?= ($informe['resultado_posicion'] ?? '') === 'SI' ? 'selected' : '' ?>>SI</option>
              <option value="NO" <?= ($informe['resultado_posicion'] ?? '') === 'NO' ? 'selected' : '' ?>>NO</option>
            </select>
          </div>
        </div>
      </div>

      <div class="mb-3 responsable-cargo-block">
        <div class="mb-2">
          <label class="form-label">Responsable técnico (nombre)</label>
          <input type="text" name="responsable_nombre" class="form-control form-control-sm" value="<?= esc($informe['responsable_nombre'] ?? '') ?>" placeholder="Ej: Lic. Pablo González">
        </div>
        <div>
          <label class="form-label">Cargo</label>
          <input type="text" name="responsable_cargo" class="form-control form-control-sm" value="<?= esc($informe['responsable_cargo'] ?? '') ?>" placeholder="Ej: Director Técnico">
        </div>
      </div>

      <div class="mt-3">
        <button type="submit" class="btn btn-primary">Guardar Informe</button>
        <a href="<?= site_url('calibracion') ?>" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
  $('#form-informe-carga-segura').on('submit', function(e) {
    e.preventDefault();
    var $form = $(this);
    var url = $form.attr('action');
    $.ajax({
      url: url,
      type: 'POST',
      data: $form.serialize(),
      dataType: 'json',
      success: function(res) {
        if (res.success) {
          if (typeof Swal !== 'undefined') Swal.fire({ icon: 'success', title: 'Guardado', text: res.message }).then(function() {
            if (res.redirect) window.location.href = res.redirect;
          });
          else { Swal.fire({ icon: 'success', title: 'Guardado', text: res.message, confirmButtonText: 'Aceptar' }).then(function() { if (res.redirect) window.location.href = res.redirect; }); }
        } else {
          if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Error al guardar' });
          else Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Error al guardar', confirmButtonText: 'Aceptar' });
        }
      },
      error: function() {
        if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: 'Error al guardar el informe' });
        else Swal.fire({ icon: 'error', title: 'Error', text: 'Error al guardar el informe', confirmButtonText: 'Aceptar' });
      }
    });
  });
});
</script>
<?= $this->endSection() ?>
