<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
Reporte de Flota (Equipos) - Montajes Campana
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
  .table-reporte { font-size: 0.9rem; }
</style>
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= site_url('reportes') ?>">Reportes</a></li>
        <li class="breadcrumb-item active">Flota (Equipos)</li>
      </ol>
    </nav>
    <h4 class="mb-3">Reporte de Flota (Equipos)</h4>

    <div class="card border border-secondary">
      <div class="card-header text-white bg-secondary pt-3 pb-2">
        <h5 class="mb-0">Filtros</h5>
      </div>
      <div class="card-body">
        <form id="form-filtros-flota" class="mb-3">
          <div class="row g-3 mb-3">
            <div class="col-12 col-md-6 col-lg-4">
              <label class="form-label">Transportista</label>
              <select name="id_tta" id="id_tta" class="form-select">
                <option value="">Todos</option>
                <?php foreach ($transportistas as $t): ?>
                  <option value="<?= (int)$t['id_tta'] ?>"><?= esc($t['transportista']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
              <label class="form-label">Bitren</label>
              <select name="bitren" id="bitren" class="form-select">
                <option value="">Todos</option>
                <option value="SI">Sí</option>
                <option value="NO">No</option>
              </select>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
              <label class="form-label">Nación</label>
              <select name="nacion" id="nacion" class="form-select">
                <option value="">Todas</option>
                <?php if (!empty($naciones) && is_array($naciones)): foreach ($naciones as $n): ?>
                  <option value="<?= esc($n['nacion']) ?>"><?= esc($n['nacion']) ?></option>
                <?php endforeach; endif; ?>
              </select>
            </div>
          </div>
          <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="submit" class="btn btn-primary" id="btn-buscar-flota">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search me-1" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>Buscar
            </button>
            <button type="button" class="btn btn-outline-secondary" id="btn-limpiar-filtros-flota" title="Vaciar filtros y volver a cargar">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle me-1" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>Limpiar filtros
            </button>
            <button type="button" class="btn btn-outline-success" id="btn-exportar-flota-csv" title="Exportar CSV">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-download me-1" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/></svg>Exportar CSV
            </button>
          </div>
        </form>

        <div id="reporte-flota-mensaje" class="alert alert-info d-none mb-0"></div>
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-reporte" id="tabla-reporte-flota">
            <thead>
              <tr>
                <th>Patente</th>
                <th>Transportista</th>
                <th>Bitren</th>
                <th>Fecha alta</th>
                <th>Nación</th>
                <th>Tractor</th>
                <th>Semi delan.</th>
                <th>Semi trasero</th>
                <th>Tara total</th>
                <th>Peso máx.</th>
                <th>Capacidad total</th>
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
    var params = [];
    var id = document.getElementById('id_tta').value;
    if (id) params.push('id_tta=' + encodeURIComponent(id));
    var bitren = document.getElementById('bitren').value;
    if (bitren) params.push('bitren=' + encodeURIComponent(bitren));
    var nacion = document.getElementById('nacion').value;
    if (nacion) params.push('nacion=' + encodeURIComponent(nacion));
    return params.length ? '?' + params.join('&') : '';
  }

  function cargarFlota() {
    var btn = document.getElementById('btn-buscar-flota');
    var tbody = document.querySelector('#tabla-reporte-flota tbody');
    var msg = document.getElementById('reporte-flota-mensaje');
    btn.disabled = true;
    tbody.innerHTML = '<tr><td colspan="11" class="text-center">Cargando...</td></tr>';
    msg.classList.add('d-none');

    var formData = new FormData(document.getElementById('form-filtros-flota'));
    formData.append(csrf, csrfVal);

    var headers = { 'X-Requested-With': 'XMLHttpRequest' };
    if (csrfVal) headers['X-CSRF-TOKEN'] = csrfVal;
    fetch(baseUrl + '/reportes/listar-flota', {
      method: 'POST',
      headers: headers,
      body: formData
    }).then(function(r) { return r.json(); }).then(function(res) {
      btn.disabled = false;
      if (res.success && res.data && res.data.length) {
        tbody.innerHTML = res.data.map(function(row) {
          return '<tr><td>' + (row.patente || '') + '</td><td>' + (row.transportista || '') + '</td><td>' + (row.bitren || '') + '</td><td>' + (row.fecha_alta || '') + '</td><td>' + (row.nacion || '') + '</td><td>' + (row.tractor_patente || '') + '</td><td>' + (row.semi_delan_patente || '') + '</td><td>' + (row.semi_trasero_patente || '') + '</td><td>' + (row.tara_total || '') + '</td><td>' + (row.peso_maximo || '') + '</td><td>' + (row.capacidad_total || '') + '</td></tr>';
        }).join('');
        msg.classList.add('d-none');
      } else {
        tbody.innerHTML = '<tr><td colspan="11" class="text-center text-muted">No hay equipos con el filtro indicado.</td></tr>';
        msg.classList.remove('d-none');
        msg.textContent = res.data && res.data.length === 0 ? 'No se encontraron equipos.' : (res.message || '');
      }
    }).catch(function() {
      btn.disabled = false;
      tbody.innerHTML = '<tr><td colspan="11" class="text-center text-danger">Error al cargar.</td></tr>';
    });
  }

  document.getElementById('form-filtros-flota').addEventListener('submit', function(e) {
    e.preventDefault();
    cargarFlota();
  });

  document.getElementById('btn-exportar-flota-csv').addEventListener('click', function() {
    window.location.href = baseUrl + '/reportes/exportar-flota-csv' + getQueryString();
  });

  document.getElementById('btn-limpiar-filtros-flota').addEventListener('click', function() {
    document.getElementById('id_tta').value = '';
    document.getElementById('bitren').value = '';
    document.getElementById('nacion').value = '';
    cargarFlota();
  });

  cargarFlota();
})();
</script>
<?= $this->endsection() ?>
