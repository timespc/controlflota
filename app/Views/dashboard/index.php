<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
Dashboard de Vencimientos - Montajes Campana
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
  .card-kpi {
    border-left: 4px solid #dee2e6;
    border-radius: 0.5rem;
    transition: box-shadow 0.2s;
  }
  .card-kpi:hover {
    box-shadow: 0 0.25rem 0.5rem rgba(0,0,0,0.08);
  }
  .card-kpi.kpi-link {
    cursor: pointer;
    transition: box-shadow 0.2s, transform 0.15s;
    text-decoration: none;
    color: inherit;
  }
  .card-kpi.kpi-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.35rem 0.75rem rgba(0,0,0,0.12);
    color: inherit;
  }
  .card-kpi.kpi-active {
    box-shadow: 0 0 0 2px rgba(0,0,0,0.2);
  }
  .card-kpi .card-kpi-num {
    font-size: 1.75rem;
    font-weight: 700;
    color: #212529;
  }
  .card-kpi .card-kpi-label {
    font-size: 0.9rem;
    color: #6c757d;
  }
  .card-kpi.border-vencido { border-left-color: #dc3545; }
  .card-kpi.border-proximo { border-left-color: #ffc107; }
  .card-kpi.border-activo { border-left-color: #198754; }
  .card-kpi.border-calib { border-left-color: #0d6efd; }
  .badge-vencido { background-color: #dc3545; color: #fff; }
  .badge-proximo { background-color: #ffc107; color: #212529; }
  .badge-ok { background-color: #198754; color: #fff; }
  @media (max-width: 768px) {
    .card-kpi .card-kpi-num { font-size: 1.5rem; }
  }
  .dataTables_wrapper .dataTables_filter input,
  .dataTables_wrapper .dataTables_length select {
    min-height: 38px;
    font-size: 15px;
  }
</style>
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <h4 class="mb-3">Dashboard de Vencimientos</h4>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-sm-6 col-xl-3">
        <a href="<?= site_url('dashboard?estado=vencido') ?>" class="card card-kpi border-vencido h-100 kpi-link <?= ($estado_activo ?? 'vencido') === 'vencido' ? 'kpi-active' : '' ?>" title="Ver equipos vencidos">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <div class="card-kpi-num"><?= isset($equipos_vencidos) ? (int)$equipos_vencidos : 0 ?></div>
              <div class="card-kpi-label">Equipos Vencidos</div>
            </div>
            <div class="text-danger opacity-75">
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16"><path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.146.146 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.163.163 0 0 1-.054.06.116.116 0 0 1-.066.017H1.146a.115.115 0 0 1-.066-.017.163.163 0 0 1-.054-.06.176.176 0 0 1 .002-.183L7.884 2.073a.147.147 0 0 1 .054-.057zm1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566z"/><path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995z"/></svg>
            </div>
          </div>
        </a>
      </div>
      <div class="col-12 col-sm-6 col-xl-3">
        <a href="<?= site_url('dashboard?estado=proximo') ?>" class="card card-kpi border-proximo h-100 kpi-link <?= ($estado_activo ?? '') === 'proximo' ? 'kpi-active' : '' ?>" title="Ver próximos a vencer (30 días)">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <div class="card-kpi-num"><?= isset($proximos_30) ? (int)$proximos_30 : 0 ?></div>
              <div class="card-kpi-label">Próximos (30 días)</div>
            </div>
            <div class="text-warning opacity-75">
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/></svg>
            </div>
          </div>
        </a>
      </div>
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-kpi border-activo h-100">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <div class="card-kpi-num"><?= isset($flota_activa) ? (int)$flota_activa : 0 ?></div>
              <div class="card-kpi-label">Flota Activa</div>
            </div>
            <div class="text-success opacity-75">
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/></svg>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-kpi border-calib h-100">
          <div class="card-body d-flex align-items-center justify-content-between">
            <div>
              <div class="card-kpi-num"><?= isset($calibraciones_activas) ? (int)$calibraciones_activas : 0 ?></div>
              <div class="card-kpi-label">Calibraciones Activas</div>
            </div>
            <div class="text-primary opacity-75">
              <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/></svg>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabla Vencimientos Críticos -->
    <div class="card border border-secondary">
      <div class="card-header text-white bg-secondary pt-3 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">Vencimientos Críticos</h5>
        <span class="small opacity-90">
          <?php
          $estado_activo = $estado_activo ?? 'vencido';
          $total = count($vencimientos_criticos ?? []);
          if ($estado_activo === 'proximo') {
              echo 'Próximos a vencer (30 días) · ' . $total . ' registros';
          } else {
              echo 'Equipos vencidos · ' . $total . ' registros';
          }
          ?>
        </span>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="vencimientos-datatable" class="table table-striped table-bordered table-hover" style="width:100%">
            <thead>
              <tr>
                <th>Equipo (Tractor/Semi)</th>
                <th>Transportista</th>
                <th>Documento</th>
                <th>Fecha Vencimiento</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $vencimientos = isset($vencimientos_criticos) && is_array($vencimientos_criticos) ? $vencimientos_criticos : [];
              foreach ($vencimientos as $v):
                $estadoClase = 'badge-ok';
                $estadoTexto = 'OK';
                if (!empty($v['estado'])) {
                  $e = strtoupper($v['estado']);
                  if ($e === 'VENCIDO') { $estadoClase = 'badge-vencido'; $estadoTexto = 'VENCIDO'; }
                  elseif ($e === 'PROXIMO' || $e === 'PRÓXIMO') { $estadoClase = 'badge-proximo'; $estadoTexto = 'Próximo'; }
                }
              ?>
              <tr>
                <td><?= esc($v['equipo'] ?? '-') ?></td>
                <td><?= esc($v['transportista'] ?? '-') ?></td>
                <td><?= esc($v['documento'] ?? '-') ?></td>
                <td><?= esc($v['fecha_vencimiento'] ?? '-') ?></td>
                <td><span class="badge rounded-pill <?= $estadoClase ?>"><?= $estadoTexto ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
  $('#vencimientos-datatable').DataTable({
    language: { url: '<?= base_url('assets/js/datatable/esp.json') ?>' },
    emptyTable: 'No hay vencimientos críticos para mostrar.',
    processing: true,
    serverSide: false,
    responsive: true,
    pageLength: 10,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'Todos']],
    order: [[3, 'asc']],
    columnDefs: [
      { orderable: false, targets: 4 }
    ]
  });
});
</script>
<?= $this->endSection() ?>
