<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
Transportista <?= esc($transportista['transportista'] ?? '') ?>
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
  .transportista-ver-card .card-body { padding-top: 1rem; }
  .transportista-ver-card .nav-tabs {
    border-bottom: 2px solid #dee2e6;
    margin-bottom: 1.25rem;
  }
  .transportista-ver-card .nav-tabs .nav-link {
    color: #495057;
    font-weight: 500;
    border: none;
    border-bottom: 3px solid transparent;
    padding: 0.5rem 1rem;
  }
  .transportista-ver-card .nav-tabs .nav-link:hover {
    border-color: transparent;
    color: #0d6efd;
  }
  .transportista-ver-card .nav-tabs .nav-link.active {
    color: #495057;
    background: transparent;
    border-bottom-color: #6c757d;
  }
  .transportista-ver-card .section-title {
    font-weight: bold;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
    margin-top: 0;
  }
  .transportista-ver-card .bloque-datos {
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 1.25rem;
    height: 100%;
  }
  .transportista-ver-card .bloque-datos h6 {
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
  }
  .transportista-ver-card .form-control.bg-light { font-size: 0.9375rem; }
</style>
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <div class="card border border-secondary transportista-ver-card">
      <div class="card-header text-white bg-secondary pt-3 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0"><?= esc($transportista['transportista'] ?? 'Transportista') ?></h5>
        <a href="<?= site_url('transportistas') ?>" class="btn btn-outline-light btn-sm">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-1" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/></svg>
          Volver a Transportistas
        </a>
      </div>
      <div class="card-body">
        <ul class="nav nav-tabs" id="transportistaTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-datos" data-bs-toggle="tab" data-bs-target="#panel-datos" type="button" role="tab">Datos</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-documentos" data-bs-toggle="tab" data-bs-target="#panel-documentos" type="button" role="tab">Documentación</button>
          </li>
        </ul>

        <div class="tab-content" id="transportistaTabContent">
          <!-- Tab Datos -->
          <div class="tab-pane fade show active" id="panel-datos" role="tabpanel">
            <h6 class="section-title">Datos del transportista</h6>
            <div class="row g-3 align-items-stretch">
              <div class="col-12 col-md-6 col-lg-4">
                <div class="bloque-datos">
                  <h6>Identificación</h6>
                  <div class="mb-2">
                    <label class="form-label small text-muted mb-0">Razón social</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($transportista['transportista'] ?? '—') ?>" readonly>
                  </div>
                  <div>
                    <label class="form-label small text-muted mb-0">CUIT</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($transportista['cuit'] ?? '—') ?>" readonly>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <div class="bloque-datos">
                  <h6>Ubicación</h6>
                  <div class="mb-2">
                    <label class="form-label small text-muted mb-0">Dirección</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($transportista['direccion'] ?? '—') ?>" readonly>
                  </div>
                  <div class="mb-2">
                    <label class="form-label small text-muted mb-0">Localidad</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($transportista['localidad'] ?? '—') ?>" readonly>
                  </div>
                  <div class="mb-2">
                    <label class="form-label small text-muted mb-0">Código postal</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($transportista['codigo_postal'] ?? '—') ?>" readonly>
                  </div>
                  <div>
                    <label class="form-label small text-muted mb-0">Provincia / Nación</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= esc(trim(($transportista['provincia'] ?? '') . ' / ' . ($transportista['nacion'] ?? '—'))) ?>" readonly>
                  </div>
                </div>
              </div>
              <div class="col-12 col-md-6 col-lg-4">
                <div class="bloque-datos">
                  <h6>Contacto</h6>
                  <div class="mb-2">
                    <label class="form-label small text-muted mb-0">Teléfono</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($transportista['telefono'] ?? '—') ?>" readonly>
                  </div>
                  <div class="mb-2">
                    <label class="form-label small text-muted mb-0">Mail contacto</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($transportista['mail_contacto'] ?? '—') ?>" readonly>
                  </div>
                  <?php if (!empty($transportista['ult_actualiz'])): ?>
                  <div>
                    <label class="form-label small text-muted mb-0">Última actualización</label>
                    <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($transportista['ult_actualiz']) ?>" readonly>
                  </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php if (!empty($transportista['comentarios'])): ?>
            <div class="mt-3">
              <label class="form-label small text-muted mb-0">Comentarios</label>
              <textarea class="form-control form-control-sm bg-light" rows="3" readonly><?= esc($transportista['comentarios']) ?></textarea>
            </div>
            <?php endif; ?>
          </div>

          <!-- Tab Documentación -->
          <div class="tab-pane fade" id="panel-documentos" role="tabpanel">
            <h6 class="section-title">Documentación adjunta</h6>
            <p class="text-muted small mb-2">Imágenes (jpg, png, gif, webp) o PDF. Máx. 10 MB.</p>
            <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
              <input type="file" id="archivo-doc-transportista" class="form-control form-control-sm" style="max-width: 260px;" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf">
              <button type="button" class="btn btn-sm btn-outline-primary" id="btn-subir-doc-transportista">Subir</button>
            </div>
            <ul id="lista-docs-transportista" class="list-group list-group-flush small"></ul>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
  var idTransportista = <?= (int)($transportista['id_tta'] ?? 0) ?>;
  var baseUrl = '<?= base_url() ?>';
  var siteUrl = function(path) { return (baseUrl + (path.charAt(0) === '/' ? path.slice(1) : path)); };

  // Documentación: listar
  function loadDocumentosTransportista() {
    var lista = document.getElementById('lista-docs-transportista');
    if (!lista) return;
    lista.innerHTML = '<li class="list-group-item text-muted">Cargando...</li>';
    fetch(siteUrl('documentos/listar/transportista/' + idTransportista))
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
          var verBtn = '<button type="button" class="btn btn-sm btn-outline-secondary ver-doc-tta" data-id="' + doc.id + '" data-nombre="' + nombre + '" data-es-imagen="' + (doc.es_imagen ? '1' : '0') + '" data-url="' + (doc.url_ver || '') + '">Ver</button>';
          var delBtn = '<button type="button" class="btn btn-sm btn-outline-danger eliminar-doc-tta" data-id="' + doc.id + '">Eliminar</button>';
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

  document.getElementById('tab-documentos').addEventListener('shown.bs.tab', function() { loadDocumentosTransportista(); });

  document.getElementById('btn-subir-doc-transportista').addEventListener('click', function() {
    var input = document.getElementById('archivo-doc-transportista');
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
    fetch(siteUrl('documentos/subir/transportista/' + idTransportista), { method: 'POST', headers: headers, body: fd })
      .then(function(r) { return r.json(); })
      .then(function(res) {
        if (res.success) {
          loadDocumentosTransportista();
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

  document.getElementById('lista-docs-transportista').addEventListener('click', function(e) {
    var btn = e.target.closest('.ver-doc-tta');
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

  document.getElementById('lista-docs-transportista').addEventListener('click', function(e) {
    var btn = e.target.closest('.eliminar-doc-tta');
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
            loadDocumentosTransportista();
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
<?= $this->endSection() ?>
