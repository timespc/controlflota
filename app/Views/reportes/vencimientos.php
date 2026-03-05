<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
Reporte de Vencimientos - Montajes Campana
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
  .table-reporte { font-size: 0.9rem; }
  .dias-negativo { color: #dc3545; font-weight: 600; }
</style>
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= site_url('reportes') ?>">Reportes</a></li>
        <li class="breadcrumb-item active">Vencimientos</li>
      </ol>
    </nav>
    <h4 class="mb-3">Reporte de Vencimientos</h4>

    <div class="card border border-secondary">
      <div class="card-header text-white bg-secondary pt-3 pb-2">
        <h5 class="mb-0">Filtros</h5>
      </div>
      <div class="card-body">
        <form id="form-filtros-vencimientos" class="mb-3">
          <div class="d-flex flex-wrap align-items-end gap-2">
            <div>
              <label class="form-label">Próximos días</label>
              <select name="dias" id="dias" class="form-select" style="min-width: 180px;">
<option value="7" selected>Próximos 7 días</option>
              <option value="30">Próximos 30 días</option>
                <option value="60">Próximos 60 días</option>
                <option value="90">Próximos 90 días</option>
                <option value="vencidos">Vencidos</option>
              </select>
            </div>
            <button type="button" class="btn btn-outline-success" id="btn-exportar-vencimientos-csv" title="Exportar CSV">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download me-1" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>Exportar CSV
            </button>
          </div>
        </form>

        <div id="reporte-vencimientos-mensaje" class="alert alert-info d-none mb-0"></div>
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-reporte" id="tabla-reporte-vencimientos">
            <thead>
              <tr>
                <th>Nº calib.</th>
                <th>Patente</th>
                <th>Equipo</th>
                <th>Transportista</th>
                <th>Fecha vto.</th>
                <th>Días restantes</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endsection() ?>

<?= $this->section('javascript') ?>
<script>
(function() {
  var baseUrl = '<?= site_url() ?>';
  var csrf = '<?= csrf_token() ?>';
  var csrfVal = '<?= csrf_hash() ?>';

  function getDias() { var v = document.getElementById('dias').value; return v === '' ? '7' : v; }

  function cargarVencimientos() {
    var sel = document.getElementById('dias');
    var tbody = document.querySelector('#tabla-reporte-vencimientos tbody');
    var msg = document.getElementById('reporte-vencimientos-mensaje');
    sel.disabled = true;
    tbody.innerHTML = '<tr><td colspan="6" class="text-center">Cargando...</td></tr>';
    msg.classList.add('d-none');

    var formData = new FormData();
    formData.append('dias', getDias());
    formData.append(csrf, csrfVal);

    var headers = { 'X-Requested-With': 'XMLHttpRequest' };
    if (csrfVal) headers['X-CSRF-TOKEN'] = csrfVal;
    fetch(baseUrl + '/reportes/listar-vencimientos', {
      method: 'POST',
      headers: headers,
      body: formData
    }).then(function(r) { return r.json(); }).then(function(res) {
      sel.disabled = false;
      if (res.success && res.data && res.data.length) {
        tbody.innerHTML = res.data.map(function(row) {
          var dr = row.dias_restantes;
          var cls = (typeof dr === 'number' && dr < 0) ? ' dias-negativo' : '';
          return '<tr><td>' + (row.id_calibracion || '') + '</td><td>' + (row.patente || '') + '</td><td>' + (row.equipo || '') + '</td><td>' + (row.transportista || '') + '</td><td>' + (row.fecha_vencimiento || '') + '</td><td class="' + cls + '">' + (dr !== undefined && dr !== null ? dr : '') + '</td></tr>';
        }).join('');
        msg.classList.add('d-none');
      } else {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No hay vencimientos en el período indicado.</td></tr>';
        msg.classList.remove('d-none');
        msg.textContent = res.data && res.data.length === 0 ? 'No se encontraron vencimientos.' : (res.message || '');
      }
    }).catch(function() {
      sel.disabled = false;
      tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error al cargar.</td></tr>';
    });
  }

  document.getElementById('dias').addEventListener('change', cargarVencimientos);

  document.getElementById('btn-exportar-vencimientos-csv').addEventListener('click', function() {
    window.location.href = baseUrl + '/reportes/exportar-vencimientos-csv?dias=' + encodeURIComponent(getDias());
  });

  cargarVencimientos();
})();
</script>
<?= $this->endsection() ?>
