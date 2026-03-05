<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
Equipo <?= esc($equipo['patente_semi_delantero'] ?? '') ?>
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
  .equipo-ver-card .card-body { padding-top: 1rem; }
  .equipo-ver-card .nav-tabs {
    border-bottom: 2px solid #dee2e6;
    margin-bottom: 1.25rem;
  }
  .equipo-ver-card .nav-tabs .nav-link {
    color: #495057;
    font-weight: 500;
    border: none;
    border-bottom: 3px solid transparent;
    padding: 0.5rem 1rem;
  }
  .equipo-ver-card .nav-tabs .nav-link:hover {
    border-color: transparent;
    color: #0d6efd;
  }
  .equipo-ver-card .nav-tabs .nav-link.active {
    color: #495057;
    background: transparent;
    border-bottom-color: #6c757d;
  }
  .equipo-ver-card .section-title {
    font-weight: bold;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
    margin-top: 0;
  }
  .equipo-ver-card .bloque-datos {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1.25rem;
    height: 100%;
  }
  .equipo-ver-card .bloque-datos h6 {
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
  }
  .equipo-ver-card .form-control.bg-light { font-size: 0.9375rem; }
</style>
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <div class="card border border-secondary equipo-ver-card">
      <div class="card-header text-white bg-secondary pt-3 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
          <h5 class="mb-1">Equipo <?= esc($equipo['patente_semi_delantero'] ?? '') ?></h5>
          <p class="mb-0 small opacity-90"><?= esc($equipo['transportista'] ?? '—') ?></p>
        </div>
        <a href="<?= site_url('equipos') ?>" class="btn btn-outline-light btn-sm">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-1" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/></svg>
          Volver a Equipos
        </a>
      </div>
      <div class="card-body">
        <ul class="nav nav-tabs" id="equipoTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-datos" data-bs-toggle="tab" data-bs-target="#panel-datos" type="button" role="tab">Datos del equipo</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-cisternas" data-bs-toggle="tab" data-bs-target="#panel-cisternas" type="button" role="tab">Cisternas</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-historial" data-bs-toggle="tab" data-bs-target="#panel-historial" type="button" role="tab">Historial inspección</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-documentos" data-bs-toggle="tab" data-bs-target="#panel-documentos" type="button" role="tab">Documentación</button>
          </li>
        </ul>

        <div class="tab-content" id="equipoTabContent">
          <!-- Tab Datos del equipo -->
          <div class="tab-pane fade show active" id="panel-datos" role="tabpanel">
            <h6 class="section-title">Datos Generales del Equipo</h6>
            <div class="row g-3 align-items-stretch">
              <div class="col-12 col-md-4">
                <div class="bloque-datos">
                  <h6>Tractor</h6>
                  <div class="mb-2">
                    <label class="form-label small text-muted mb-0">Patente Tractor</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($equipo['patente_tractor'] ?? '—') ?>" readonly>
                  </div>
                  <div class="mb-2">
                    <label class="form-label small text-muted mb-0">Modelo (Año)</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($equipo['tractor_anio_modelo'] ?? '—') ?>" readonly>
                  </div>
                  <div>
                    <label class="form-label small text-muted mb-0">Cant. Ejes</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($equipo['ejes_tractor'] ?? '—') ?>" readonly>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="bloque-datos">
                  <h6>Semi-Remolque</h6>
                  <div class="mb-2">
                    <label class="form-label small text-muted mb-0">Patente Semi</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($equipo['patente_semi_delantero'] ?? '—') ?>" readonly>
                  </div>
                  <div class="mb-2">
                    <label class="form-label small text-muted mb-0">Modelo (Año)</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($equipo['semi_delantero_anio_modelo'] ?? '—') ?>" readonly>
                  </div>
                  <div class="mb-2">
                    <label class="form-label small text-muted mb-0">Cant. Ejes</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($equipo['ejes_semi_delantero'] ?? '—') ?>" readonly>
                  </div>
                  <div>
                    <label class="form-label small text-muted mb-0">Marca</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($equipo['marca_nombre'] ?? '—') ?>" readonly>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="bloque-datos">
                  <h6>Conjunto</h6>
                  <div class="mb-2">
                    <label class="form-label small text-muted mb-0">Tara (Kgs)</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= isset($equipo['tara_total']) && $equipo['tara_total'] !== '' && $equipo['tara_total'] !== null ? number_format((float)$equipo['tara_total'], 0, ',', '.') : '—' ?>" readonly>
                  </div>
                  <div class="mb-2">
                    <label class="form-label small text-muted mb-0">Peso Máx. (Kgs)</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= isset($equipo['peso_maximo']) && $equipo['peso_maximo'] !== '' && $equipo['peso_maximo'] !== null ? number_format((float)$equipo['peso_maximo'], 0, ',', '.') : '—' ?>" readonly>
                  </div>
                  <div>
                    <label class="form-label small text-muted mb-0">Tipo de Carga</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($equipo['modo_carga'] ?? '—') ?>" readonly>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Tab Cisternas -->
          <div class="tab-pane fade" id="panel-cisternas" role="tabpanel">
            <h6 class="section-title">Capacidades de Cisternas (Lts)</h6>
            <div class="row g-2">
              <?php for ($i = 1; $i <= 10; $i++): $key = 'cisterna_' . $i . '_capacidad'; $val = $equipo[$key] ?? 0; ?>
              <div class="col-6 col-md-4 col-lg-2">
                <label class="form-label small text-muted mb-0">C<?= $i ?></label>
                <input type="text" class="form-control form-control-sm bg-light" value="<?= $val !== '' && $val !== null ? number_format((float)$val, 2, ',', '.') : '0' ?>" readonly>
              </div>
              <?php endfor; ?>
              <div class="col-12 col-md-4">
                <label class="form-label small text-muted mb-0">Cap. Total (Lts)</label>
                <input type="text" class="form-control form-control-sm bg-light fw-bold" value="<?= isset($equipo['capacidad_total']) && $equipo['capacidad_total'] !== '' && $equipo['capacidad_total'] !== null ? number_format((float)$equipo['capacidad_total'], 2, ',', '.') : '0' ?>" readonly>
              </div>
            </div>

            <h6 class="section-title mt-4">Tipos de Checklist</h6>
            <div class="d-flex flex-wrap gap-2">
              <?php
              $checklistTipos = [
                'checklist_asfalto'    => 'Asfalto',
                'checklist_alcohol'    => 'Alcohol',
                'checklist_biodiesel'  => 'Biodiesel',
                'checklist_comb_liv'   => 'Comb. Liv.',
                'checklist_comb_pes'   => 'Comb. Pes.',
                'checklist_solvente'   => 'Solvente',
                'checklist_coke'       => 'Coke',
                'checklist_lubes_gra'  => 'Lubes Gra.',
                'checklist_lubes_env'  => 'Lubes Env.',
                'checklist_glp'        => 'GLP',
              ];
              foreach ($checklistTipos as $campo => $etiqueta):
                $activo = !empty($equipo[$campo]);
              ?>
              <span class="badge fs-6 fw-normal px-3 py-2 text-white <?= $activo ? 'bg-success' : 'bg-secondary' ?>">
                <?= esc($etiqueta) ?>
                <span class="ms-1 fw-bold"><?= $activo ? 'SI' : 'NO' ?></span>
              </span>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Tab Documentación -->
          <div class="tab-pane fade" id="panel-documentos" role="tabpanel">
            <h6 class="section-title">Documentación adjunta</h6>
            <p class="text-muted small mb-2">Imágenes (jpg, png, gif, webp) o PDF. Máx. 10 MB.</p>
            <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
              <input type="file" id="archivo-doc-equipo" class="form-control form-control-sm" style="max-width: 260px;" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf">
              <button type="button" class="btn btn-sm btn-outline-primary" id="btn-subir-doc-equipo">Subir</button>
            </div>
            <ul id="lista-docs-equipo" class="list-group list-group-flush small"></ul>
          </div>

          <!-- Tab Historial inspección -->
          <div class="tab-pane fade" id="panel-historial" role="tabpanel">
            <h6 class="section-title">Historial de Inspecciones y Calibraciones</h6>
            <div class="table-responsive">
              <table class="table table-striped table-bordered table-sm">
                <thead class="table-light">
                  <tr>
                    <th>Fecha</th>
                    <th>Tipo</th>
                    <th>Inspector / Taller</th>
                    <th>Observaciones</th>
                    <th>Vencimiento</th>
                    <th class="text-center">Detalle</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($historial)): ?>
                  <tr>
                    <td colspan="6" class="text-muted text-center py-4">No hay registros de calibración para este equipo.</td>
                  </tr>
                  <?php else: ?>
                  <?php foreach ($historial as $h): ?>
                  <tr>
                    <td><?= esc($h['fecha']) ?></td>
                    <td><?= esc($h['tipo']) ?></td>
                    <td><?= esc($h['inspector_taller']) ?></td>
                    <td><?= esc($h['observaciones']) ?></td>
                    <td><?= esc($h['vencimiento']) ?></td>
                    <td class="text-center">
                      <?php if (!empty($h['token_publico'])): ?>
                      <button type="button" class="btn btn-sm btn-outline-primary btn-ver-calibracion" data-url="<?= esc(site_url('calibracion/ver/' . $h['token_publico'])) ?>">Ver calibración</button>
                      <?php else: ?>
                      <span class="text-muted">—</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal vista previa documento -->
<div class="modal fade" id="modal-preview-doc" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title text-break" id="modal-preview-doc-title">Documento</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body text-center p-3" id="modal-preview-doc-body">
        <img id="preview-doc-imagen" src="" alt="Vista previa" class="img-fluid" style="max-height: 70vh; display: none;">
        <iframe id="preview-doc-iframe" src="" class="w-100" style="height: 70vh; display: none; border: 0;"></iframe>
        <p id="preview-doc-sin-preview" class="text-muted mb-0" style="display: none;">Sin vista previa. <a id="preview-doc-descarga" href="" target="_blank" rel="noopener">Abrir/descargar</a></p>
      </div>
    </div>
  </div>
</div>

<!-- Modal: Certificado de calibración (solo lectura) -->
<div class="modal fade" id="modal-certificado-calibracion" tabindex="-1" aria-labelledby="titulo-modal-cert-calib" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-scrollable" style="max-width: 520px;">
    <div class="modal-content">
      <div class="modal-header py-2">
        <h5 class="modal-title" id="titulo-modal-cert-calib">Certificado de calibración (solo lectura)</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body p-0">
        <iframe id="iframe-certificado-calibracion" src="" style="width:100%; height:75vh; border: none;" title="Certificado de calibración"></iframe>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var idEquipo = <?= (int)($equipo['id_equipo'] ?? 0) ?>;
  var baseUrl = '<?= base_url() ?>';
  var siteUrl = function(path) { return (baseUrl + (path.charAt(0) === '/' ? path.slice(1) : path)); };

  // Certificado calibración
  var modal = document.getElementById('modal-certificado-calibracion');
  var iframe = document.getElementById('iframe-certificado-calibracion');
  if (modal && iframe) {
    modal.addEventListener('show.bs.modal', function(e) {
      var btn = e.relatedTarget;
      var url = btn && btn.getAttribute('data-url');
      if (url) iframe.src = url;
    });
    modal.addEventListener('hidden.bs.modal', function() { iframe.src = ''; });
    document.querySelectorAll('.btn-ver-calibracion').forEach(function(btn) {
      btn.addEventListener('click', function() {
        iframe.src = this.getAttribute('data-url');
        new bootstrap.Modal(modal).show();
      });
    });
  }

  // Documentación: listar (equipo = unidad en backend)
  function loadDocumentosEquipo() {
    var lista = document.getElementById('lista-docs-equipo');
    if (!lista) return;
    lista.innerHTML = '<li class="list-group-item text-muted">Cargando...</li>';
    fetch(siteUrl('documentos/listar/equipo/' + idEquipo))
      .then(function(r) { return r.json(); })
      .then(function(res) {
        lista.innerHTML = '';
        if (!res.success) {
          lista.innerHTML = '<li class="list-group-item text-danger">Error al cargar documentos.</li>';
          return;
        }
        if (!res.data || res.data.length === 0) {
          lista.innerHTML = '<li class="list-group-item text-muted">No hay documentos.</li>';
          return;
        }
        res.data.forEach(function(doc) {
          var nombre = (doc.nombre_original || doc.nombre_archivo || '').replace(/"/g, '&quot;');
          var verBtn = '<button type="button" class="btn btn-sm btn-outline-secondary ver-doc-equipo" data-id="' + doc.id + '" data-nombre="' + nombre + '" data-es-imagen="' + (doc.es_imagen ? '1' : '0') + '" data-url="' + (doc.url_ver || '') + '">Ver</button>';
          var delBtn = '<button type="button" class="btn btn-sm btn-outline-danger eliminar-doc-equipo" data-id="' + doc.id + '">Eliminar</button>';
          var li = document.createElement('li');
          li.className = 'list-group-item d-flex justify-content-between align-items-center';
          li.innerHTML = '<span class="text-break">' + (doc.nombre_original || doc.nombre_archivo) + '</span> <span class="ms-2">' + verBtn + ' ' + delBtn + '</span>';
          lista.appendChild(li);
        });
      })
      .catch(function() {
        lista.innerHTML = '<li class="list-group-item text-danger">Error al cargar documentos.</li>';
      });
  }

  // Cargar documentos al mostrar tab
  document.getElementById('tab-documentos').addEventListener('shown.bs.tab', function() { loadDocumentosEquipo(); });

  // Subir documento
  document.getElementById('btn-subir-doc-equipo').addEventListener('click', function() {
    var input = document.getElementById('archivo-doc-equipo');
    if (!input.files || !input.files[0]) {
      Swal.fire({ icon: 'warning', title: 'Aviso', text: 'Seleccione un archivo', confirmButtonText: 'Aceptar' });
      return;
    }
    var fd = new FormData();
    fd.append('archivo', input.files[0]);
    var btn = this;
    btn.disabled = true;
    var csrfMeta = document.querySelector('meta[name="csrf-token-value"]');
    var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
    var headers = { 'X-Requested-With': 'XMLHttpRequest' };
    if (csrfToken) headers['X-CSRF-TOKEN'] = csrfToken;
    fetch(siteUrl('documentos/subir/equipo/' + idEquipo), { method: 'POST', headers: headers, body: fd })
      .then(function(r) { return r.json(); })
      .then(function(res) {
        if (res.success) {
          loadDocumentosEquipo();
          input.value = '';
          Swal.fire({ icon: 'success', title: 'Éxito', text: res.message, confirmButtonText: 'Aceptar' });
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Error al subir', confirmButtonText: 'Aceptar' });
        }
      })
      .catch(function() {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error al subir el archivo', confirmButtonText: 'Aceptar' });
      })
      .finally(function() { btn.disabled = false; });
  });

  // Ver documento (delegado)
  document.getElementById('lista-docs-equipo').addEventListener('click', function(e) {
    var btn = e.target.closest('.ver-doc-equipo');
    if (!btn) return;
    var id = btn.getAttribute('data-id');
    var nombre = btn.getAttribute('data-nombre') || 'Documento';
    var esImagen = btn.getAttribute('data-es-imagen') === '1';
    var url = btn.getAttribute('data-url') || siteUrl('documentos/ver/' + id);
    var ext = (nombre.split('.').pop() || '').toLowerCase();
    var esPdf = ext === 'pdf';
    document.getElementById('modal-preview-doc-title').textContent = nombre;
    var imgEl = document.getElementById('preview-doc-imagen');
    var iframeEl = document.getElementById('preview-doc-iframe');
    var sinEl = document.getElementById('preview-doc-sin-preview');
    var descargaEl = document.getElementById('preview-doc-descarga');
    imgEl.style.display = 'none'; imgEl.src = '';
    iframeEl.style.display = 'none'; iframeEl.src = '';
    sinEl.style.display = 'none';
    descargaEl.href = url;
    if (esImagen) { imgEl.src = url; imgEl.style.display = 'inline'; }
    else if (esPdf) { iframeEl.src = url; iframeEl.style.display = 'block'; }
    else sinEl.style.display = 'block';
    new bootstrap.Modal(document.getElementById('modal-preview-doc')).show();
  });

  // Eliminar documento (delegado)
  document.getElementById('lista-docs-equipo').addEventListener('click', function(e) {
    var btn = e.target.closest('.eliminar-doc-equipo');
    if (!btn) return;
    var id = btn.getAttribute('data-id');
    var eliminar = function() {
      var csrfMeta = document.querySelector('meta[name="csrf-token-value"]');
      var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
      var headers = { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' };
      if (csrfToken) headers['X-CSRF-TOKEN'] = csrfToken;
      fetch(siteUrl('documentos/eliminar/' + id), { method: 'POST', headers: headers, body: '' })
        .then(function(r) { return r.json(); })
        .then(function(res) {
          if (res.success) {
            loadDocumentosEquipo();
            Swal.fire({ icon: 'success', title: 'Eliminado', text: res.message, confirmButtonText: 'Aceptar' });
          } else {
            Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Error al eliminar', confirmButtonText: 'Aceptar' });
          }
        })
        .catch(function() {
          Swal.fire({ icon: 'error', title: 'Error', text: 'Error al eliminar', confirmButtonText: 'Aceptar' });
        });
    };
    Swal.fire({
      title: '¿Eliminar documento?',
      text: 'Esta acción no se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Sí, eliminar'
    }).then(function(result) { if (result.isConfirmed) eliminar(); });
  });
});
</script>
<?= $this->endsection() ?>
