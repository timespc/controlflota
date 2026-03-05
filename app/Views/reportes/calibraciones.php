<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
Reporte de Calibraciones - Montajes Campana
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
  .table-reporte { font-size: 0.9rem; }
  .badge-vigente { background-color: #198754; }
  .badge-vencido { background-color: #dc3545; }
</style>
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= site_url('reportes') ?>">Reportes</a></li>
        <li class="breadcrumb-item active">Calibraciones</li>
      </ol>
    </nav>
    <h4 class="mb-3">Reporte de Calibraciones</h4>

    <div class="card border border-secondary">
      <div class="card-header text-white bg-secondary pt-3 pb-2">
        <h5 class="mb-0">Filtros</h5>
      </div>
      <div class="card-body">
        <form id="form-filtros-calibraciones" class="row g-3 mb-3">
          <div class="col-12 col-md-6 col-lg-2">
            <label class="form-label">Fecha desde</label>
            <input type="date" name="fecha_desde" id="fecha_desde" class="form-control">
          </div>
          <div class="col-12 col-md-6 col-lg-2">
            <label class="form-label">Fecha hasta</label>
            <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control">
          </div>
          <div class="col-12 col-md-6 col-lg-2">
            <label class="form-label">Patente</label>
            <input type="text" name="patente" id="patente" class="form-control" placeholder="Ej: AA123BB">
          </div>
          <div class="col-12 col-md-6 col-lg-2">
            <label class="form-label">Nro de precinto</label>
            <input type="text" name="precinto" id="precinto" class="form-control" placeholder="Cualquier campo precinto">
          </div>
          <div class="col-12 col-md-6 col-lg-3">
            <label class="form-label">Calibrador</label>
            <select name="id_calibrador" id="id_calibrador" class="form-select">
              <option value="">Todos</option>
              <?php foreach ($calibradores as $c): ?>
                <option value="<?= (int)$c['id_calibrador'] ?>"><?= esc($c['calibrador']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12 d-flex align-items-end gap-2 flex-nowrap">
            <button type="submit" class="btn btn-primary" id="btn-buscar-calibraciones">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search me-1" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>Buscar
            </button>
            <button type="button" class="btn btn-outline-secondary" id="btn-limpiar-filtros-calibraciones" title="Vaciar filtros y volver a cargar">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle me-1" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>Limpiar filtros
            </button>
            <button type="button" class="btn btn-outline-success" id="btn-exportar-calibraciones-csv" title="Exportar CSV con los filtros actuales">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download me-1" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>Exportar CSV
            </button>
          </div>
        </form>

        <div id="reporte-calibraciones-mensaje" class="alert alert-info d-none mb-0"></div>
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-reporte" id="tabla-reporte-calibraciones">
            <thead>
              <tr>
                <th>Nº</th>
                <th>Patente</th>
                <th>Equipo</th>
                <th>Transportista</th>
                <th>Fecha calib.</th>
                <th>Vto. calib.</th>
                <th>Calibrador</th>
                <th>Estado</th>
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

  function getQueryString() {
    var fd = document.getElementById('fecha_desde').value;
    var fh = document.getElementById('fecha_hasta').value;
    var p = document.getElementById('patente').value;
    var prec = document.getElementById('precinto').value;
    var idc = document.getElementById('id_calibrador').value;
    var q = [];
    if (fd) q.push('fecha_desde=' + encodeURIComponent(fd));
    if (fh) q.push('fecha_hasta=' + encodeURIComponent(fh));
    if (p) q.push('patente=' + encodeURIComponent(p));
    if (prec) q.push('precinto=' + encodeURIComponent(prec));
    if (idc) q.push('id_calibrador=' + encodeURIComponent(idc));
    return q.length ? '?' + q.join('&') : '';
  }

  function cargarCalibraciones() {
    var btn = document.getElementById('btn-buscar-calibraciones');
    var tbody = document.querySelector('#tabla-reporte-calibraciones tbody');
    var msg = document.getElementById('reporte-calibraciones-mensaje');
    btn.disabled = true;
    tbody.innerHTML = '<tr><td colspan="8" class="text-center">Cargando...</td></tr>';
    msg.classList.add('d-none');

    var formData = new FormData(document.getElementById('form-filtros-calibraciones'));
    formData.append(csrf, csrfVal);

    var headers = { 'X-Requested-With': 'XMLHttpRequest' };
    if (csrfVal) headers['X-CSRF-TOKEN'] = csrfVal;
    fetch(baseUrl + '/reportes/listar-calibraciones', {
      method: 'POST',
      headers: headers,
      body: formData
    }).then(function(r) { return r.json(); }).then(function(res) {
      btn.disabled = false;
      if (res.success && res.data && res.data.length) {
        tbody.innerHTML = res.data.map(function(row) {
          var badge = row.estado === 'Vencido' ? 'badge-vencido' : 'badge-vigente';
          return '<tr><td>' + (row.numero || '') + '</td><td>' + (row.patente || '') + '</td><td>' + (row.equipo || '') + '</td><td>' + (row.transportista || '') + '</td><td>' + (row.fecha_calib || '') + '</td><td>' + (row.vto_calib || '') + '</td><td>' + (row.calibrador || '') + '</td><td><span class="badge ' + badge + '">' + (row.estado || '') + '</span></td></tr>';
        }).join('');
        msg.classList.add('d-none');
      } else {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No hay registros con los filtros indicados.</td></tr>';
        msg.classList.remove('d-none');
        msg.textContent = res.data && res.data.length === 0 ? 'No se encontraron calibraciones.' : (res.message || '');
      }
    }).catch(function() {
      btn.disabled = false;
      tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error al cargar.</td></tr>';
    });
  }

  document.getElementById('form-filtros-calibraciones').addEventListener('submit', function(e) {
    e.preventDefault();
    cargarCalibraciones();
  });

  document.getElementById('btn-exportar-calibraciones-csv').addEventListener('click', function() {
    window.location.href = baseUrl + '/reportes/exportar-calibraciones-csv' + getQueryString();
  });

  document.getElementById('btn-limpiar-filtros-calibraciones').addEventListener('click', function() {
    document.getElementById('fecha_desde').value = '';
    document.getElementById('fecha_hasta').value = '';
    document.getElementById('patente').value = '';
    document.getElementById('precinto').value = '';
    document.getElementById('id_calibrador').value = '';
    cargarCalibraciones();
  });

  cargarCalibraciones();
})();
</script>
<?= $this->endsection() ?>
