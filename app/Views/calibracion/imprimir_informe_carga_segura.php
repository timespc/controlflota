<?= $this->extend('layout/print') ?>

<?= $this->section('titulo') ?>
<?= esc($titulo) ?>
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
  * { box-sizing: border-box; }
  body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 1rem; background: #f5f5f5; }
  .row { display: flex; flex-wrap: wrap; margin: 0 -0.5rem 1rem; }
  .row:last-child { margin-bottom: 0; }
  .col-12 { flex: 0 0 100%; padding: 0 0.5rem; }
  .col-md-6 { flex: 0 0 50%; max-width: 50%; padding: 0 0.5rem; }
  @media (max-width: 768px) { .col-md-6 { flex: 0 0 100%; max-width: 100%; } }
  .mb-3 { margin-bottom: 1rem; }
  .me-2 { margin-right: 0.5rem; }
  .mt-1 { margin-top: 0.25rem; }
  .btn { display: inline-block; padding: 0.35rem 0.75rem; font-size: 0.875rem; border-radius: 0.25rem; text-decoration: none; border: 1px solid transparent; cursor: pointer; }
  .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.8125rem; }
  .btn-primary { color: #fff; background: #0d6efd; border-color: #0d6efd; }
  .btn-secondary { color: #fff; background: #6c757d; border-color: #6c757d; }
  .text-muted { color: #6c757d; }
  .small { font-size: 0.875em; }

  .print_informe { font-family: Arial, sans-serif; font-size: 12px; max-width: 210mm; margin: 0 auto; background: #fff; padding: 1rem; }
  .informe-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; border-bottom: 2px solid #333; padding-bottom: 0.5rem; }
  .informe-titulo { font-size: 1.4rem; font-weight: bold; color: #c00; }
  .informe-datos { display: flex; justify-content: space-between; gap: 2rem; margin-bottom: 1.5rem; }
  .informe-datos .col { flex: 1; }
  .informe-datos p { margin: 0 0 4px 0; }
  .informe-datos .label-f { font-weight: bold; }
  .informe-section-title { font-weight: bold; font-size: 1rem; color: #0d6efd; margin: 1rem 0 0.5rem 0; border-bottom: 1px solid #dee2e6; padding-bottom: 2px; }
  .informe-table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; font-size: 0.9rem; }
  .informe-table th, .informe-table td { border: 1px solid #333; padding: 4px 6px; text-align: center; }
  .informe-table th { background: #e7f1ff; font-weight: bold; }
  .informe-resultado { margin: 1rem 0; }
  .informe-resultado ul { list-style: none; padding: 0; margin: 0; }
  .informe-resultado li { margin: 2px 0; }
  .informe-firma { margin-top: 2rem; text-align: right; }
  .informe-firma .cargo { font-weight: bold; margin-bottom: 4px; }
  .informe-firma .nombre { font-style: italic; }
  .informe-footer { margin-top: 2rem; padding-top: 0.5rem; border-top: 1px solid #333; display: flex; justify-content: space-between; align-items: center; font-size: 0.85rem; color: #666; }

  /* Vista previa en pantalla: separación visual entre hojas (SEMI 1 / SEMI 2) */
  @media screen {
    .print_informe .hoja-informe {
      margin-bottom: 2.5rem;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
      border-radius: 2px;
      overflow: hidden;
    }
    .print_informe .hoja-informe:last-child {
      margin-bottom: 0;
    }
  }

  @media print {
    @page { margin: 0.7cm; size: A4; }
    html, body { min-height: 100%; height: auto; background: #fff !important; padding: 0 !important; font-size: 12px !important; }
    .no-print { display: none !important; }
    .print_informe {
      display: flex; flex-direction: column; min-height: 277mm; /* A4 297mm - márgenes aprox */
      max-width: 100%; padding: 0; font-size: 12px;
      -webkit-print-color-adjust: exact; print-color-adjust: exact;
    }
    .print_informe .hoja-informe {
      display: flex; flex-direction: column; min-height: 277mm;
      margin-bottom: 0 !important; box-shadow: none !important; border-radius: 0;
    }
    .print_informe .hoja-informe .informe-body { flex: 1 1 auto; }
    .print_informe .hoja-informe .informe-footer { flex-shrink: 0; margin-top: auto; }
    .print_informe .informe-header {
      margin-bottom: 0.5rem; padding-bottom: 0.35rem;
    }
    .print_informe .informe-header img { height: 52px !important; }
    .print_informe .informe-titulo { font-size: 1.35rem; }
    .print_informe .informe-datos { margin-bottom: 0.5rem; gap: 1rem; }
    .print_informe .informe-datos p { margin: 0 0 2px 0; }
    .print_informe .informe-section-title {
      font-size: 1rem; margin: 0.5rem 0 0.25rem 0; padding-bottom: 2px;
    }
    .print_informe .informe-table {
      font-size: 0.9rem; margin-bottom: 0.5rem;
    }
    .print_informe .informe-table th,
    .print_informe .informe-table td { padding: 4px 6px; }
    .print_informe .row { margin-bottom: 0.5rem; }
    .print_informe .informe-resultado { margin: 0.5rem 0; font-size: 1em; }
    .print_informe .informe-resultado li { margin: 1px 0; }
    .print_informe .informe-firma {
      margin-top: 0.75rem; font-size: 1.05em;
    }
    .print_informe .informe-firma .cargo { margin-bottom: 2px; }
    .print_informe .informe-footer {
      margin-top: 0.7rem; padding-top: 0.35rem; font-size: 0.85rem;
    }
    .print_informe .informe-footer img { height: 34px !important; }
    .print_informe .small.mt-1 { margin-top: 0.2rem; font-size: 0.85rem; }
    .informe-table th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  }
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
$detalleCalib = array_merge($cal['detalle'] ?? [], $cal['detalle_semi2'] ?? []);
$numCisternas = count($detalleCalib) ?: 1;
$numCisternasSemi1 = count($cal['detalle'] ?? []);
$numCisternasSemi2 = count($cal['detalle_semi2'] ?? []);
$tieneDosSemis = $numCisternasSemi2 > 0;
$hojas = $tieneDosSemis
    ? [['titulo' => ' - SEMI 1', 'inicio' => 1, 'fin' => $numCisternasSemi1], ['titulo' => ' - SEMI 2', 'inicio' => $numCisternasSemi1 + 1, 'fin' => $numCisternas]]
    : [['titulo' => '', 'inicio' => 1, 'fin' => $numCisternas]];
$fechaEmision = ($informe['fecha_emision'] ?? null) ?: ($cal['fecha_calib'] ?? null);
$fechaVto = $cal['vto_calib'] ?? null;
$unidad = $cal['unidad'] ?? [];
$patenteTractor = $unidad['patente_tractor'] ?? $cal['patente_tractor'] ?? '-';
$patenteSemi = $cal['patente'] ?? '-';
$notaPos = trim((string)($informe['nota_posicion_sensores'] ?? ''));
?>
<div class="row no-print mb-3">
  <div class="col-12">
    <a href="<?= site_url('calibracion/informe-carga-segura/' . $idCal) ?>" class="btn btn-secondary btn-sm me-2">Volver al formulario</a>
    <button type="button" class="btn btn-primary btn-sm" onclick="window.print();">Imprimir</button>
  </div>
</div>

<div class="print_informe">
  <?php foreach ($hojas as $idx => $hoja): $esUltima = ($idx === count($hojas) - 1); ?>
  <div class="hoja-informe" <?= ! $esUltima ? 'style="page-break-after: always;"' : '' ?>>
    <div class="informe-body">
      <div class="informe-header">
        <div>
          <?php if (is_file(FCPATH . 'img/tarjeta-calibracion/logo.png')): ?>
          <img src="<?= base_url('img/tarjeta-calibracion/logo.png') ?>" alt="Montajes Campana" style="height: 60px;">
          <?php else: ?>
          <strong>Montajes Campana</strong>
          <?php endif; ?>
        </div>
        <div class="informe-titulo">INFORME DE CARGA SEGURA N° <?= $idCal ?><?= esc($hoja['titulo']) ?></div>
      </div>

      <div class="informe-datos">
        <div class="col">
          <p><span class="label-f">TRANSPORTISTA:</span> <?= esc($cal['transportista'] ?? '-') ?></p>
          <p><span class="label-f">CUIT:</span> <?= esc($informe['cuit_transportista'] ?? '-') ?></p>
          <p><span class="label-f">PATENTE TRACTOR:</span> <?= esc($patenteTractor) ?></p>
          <p><span class="label-f">PATENTE SEMIRREMOLQUE:</span> <?= esc($patenteSemi) ?></p>
        </div>
        <div class="col">
          <p><span class="label-f">CALIBRADO N°:</span> <?= $idCal ?></p>
          <p><span class="label-f">FECHA DE EMISIÓN:</span> <?= $fechaEmision ? date('d/m/Y', strtotime($fechaEmision)) : '-' ?></p>
          <p><span class="label-f">FECHA VENCIMIENTO:</span> <?= $fechaVto ? date('d/m/Y', strtotime($fechaVto)) : '-' ?></p>
        </div>
      </div>

      <div class="informe-section-title">CONTROL VACÍO EN CISTERNAS</div>
      <table class="informe-table">
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
          <?php for ($n = $hoja['inicio']; $n <= $hoja['fin']; $n++): $d = $detallePorCisterna[$n] ?? []; $numLocal = $n - $hoja['inicio'] + 1; ?>
          <tr>
            <td><?= $numLocal ?></td>
            <td><?php $vol = $d['volumen_lts'] ?? null; if ($vol === null || $vol === '') { $vol = $detalleCalib[$n - 1]['capacidad'] ?? null; } echo $vol !== null && $vol !== '' ? esc($vol) : '—'; ?></td>
            <td><?= isset($d['vacio_requerido']) && $d['vacio_requerido'] !== '' ? esc($d['vacio_requerido']) : '—' ?></td>
            <td><?= isset($d['vacio_medido']) && $d['vacio_medido'] !== '' ? esc($d['vacio_medido']) : '—' ?></td>
            <td><?= esc($d['accion_tomada'] ?? 'N/A') ?></td>
            <td><?= isset($d['volumen_final_lts']) && $d['volumen_final_lts'] !== '' ? esc($d['volumen_final_lts']) : '—' ?></td>
            <td><?= esc($d['cumple_control'] ?? '—') ?></td>
          </tr>
          <?php endfor; ?>
        </tbody>
      </table>

      <div class="row">
        <div class="col-md-6">
          <div class="informe-section-title">TRAZABILIDAD SENSORES</div>
          <table class="informe-table">
            <thead>
              <tr>
                <th>CISTERNA N°</th>
                <th>MARCA</th>
                <th>N° DE SERIE</th>
                <th>CUMPLE (SI/NO)</th>
              </tr>
            </thead>
            <tbody>
              <?php for ($n = $hoja['inicio']; $n <= $hoja['fin']; $n++): $d = $detallePorCisterna[$n] ?? []; $numLocal = $n - $hoja['inicio'] + 1; ?>
              <tr>
                <td><?= $numLocal ?></td>
                <td><?= esc($d['marca_sensor'] ?? '—') ?></td>
                <td><?= esc($d['numero_serie_sensor'] ?? '—') ?></td>
                <td><?= esc($d['cumple_trazabilidad'] ?? '—') ?></td>
              </tr>
              <?php endfor; ?>
            </tbody>
          </table>
        </div>
        <div class="col-md-6">
          <div class="informe-section-title">POSICIÓN SENSORES</div>
          <table class="informe-table">
            <thead>
              <tr>
                <th>CUMPLE (SI/NO)</th>
                <th>OBSERVACIÓN</th>
                <th>LITROS SENSOR/REBALSE</th>
              </tr>
            </thead>
            <tbody>
              <?php for ($n = $hoja['inicio']; $n <= $hoja['fin']; $n++): $d = $detallePorCisterna[$n] ?? []; $numLocal = $n - $hoja['inicio'] + 1; ?>
              <tr>
                <td><?= esc($d['cumple_posicion'] ?? '—') ?></td>
                <td><?php $obs = trim((string)($d['observacion_posicion'] ?? '')); echo $obs !== '' ? esc($obs) . ' mm' : '—'; ?></td>
                <td><?php $lts = trim((string)($d['litros_sensor_rebalse'] ?? '')); echo $lts !== '' ? esc($lts) . ' Lts' : '—'; ?></td>
              </tr>
              <?php endfor; ?>
            </tbody>
          </table>
          <p class="small text-muted mt-1"><?= $notaPos !== '' ? nl2br(esc($notaPos)) : '* No existe la posibilidad de posicionar el sensor de sobrellenado por debajo del valor mencionado.' ?></p>
        </div>
      </div>

      <div class="informe-section-title">RESULTADO FINAL DE AUDITORÍA - CUMPLE SI/NO</div>
      <div class="informe-resultado">
        <ul>
          <li><strong>CONTROL VACÍO CISTERNAS:</strong> <?= esc($informe['resultado_control_vacio'] ?? '—') ?></li>
          <li><strong>TRAZABILIDAD DE SENSORES:</strong> <?= esc($informe['resultado_trazabilidad'] ?? '—') ?></li>
          <li><strong>POSICIÓN DE SENSORES:</strong> <?= esc($informe['resultado_posicion'] ?? '—') ?></li>
        </ul>
      </div>

      <div class="informe-firma">
        <div class="cargo">RESPONSABLE TÉCNICO</div>
        <div class="nombre"><?= esc($informe['responsable_nombre'] ?? '') ?></div>
        <div><?= esc($informe['responsable_cargo'] ?? '') ?></div>
      </div>
    </div>

    <div class="informe-footer">
      <div>
        <?php if (is_file(FCPATH . 'img/tarjeta-calibracion/logo.png')): ?>
        <img src="<?= base_url('img/tarjeta-calibracion/logo.png') ?>" alt="Logo" style="height: 36px;">
        <?php endif; ?>
      </div>
      <div>
        Santa Cruz 165, Campana (2804), Pcia. Bs. As.<br>
        montajescampanaservicios@gmail.com
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<?= $this->endSection() ?>
