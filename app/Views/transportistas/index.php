<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
Transportistas
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
  /* Estilos para tablets y móviles */
  @media (max-width: 768px) {
    .btn-tablet {
      padding: 12px 20px;
      font-size: 16px;
      min-height: 44px;
    }
    .form-control, .form-select {
      font-size: 16px;
      min-height: 44px;
    }
    .table-responsive {
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
    }
  }
  
  .btn-tablet {
    padding: 10px 18px;
    font-size: 15px;
    min-height: 42px;
    touch-action: manipulation;
  }
  
  .dataTables_wrapper .dataTables_filter input,
  .dataTables_wrapper .dataTables_length select {
    min-height: 38px;
    font-size: 15px;
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
</style>
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <div class="card border border-secondary">
      <div class="card-header text-white bg-secondary pt-3 pb-2 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Transportistas</h5>
        <button type="button" class="btn btn-primary btn-tablet" data-bs-toggle="modal" data-bs-target="#modal-transportista" id="btn-nuevo-transportista">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
          </svg>
          Agregar Transportista
        </button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="transportistas-datatable" class="table table-striped table-bordered table-hover" style="width:100%">
            <thead>
              <tr>
                <th>Transportista</th>
                <th>Dirección</th>
                <th>Localidad</th>
                <th>Provincia</th>
                <th>Nación</th>
                <th>Teléfono</th>
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

<!-- Modal para Agregar/Editar Transportista -->
<div class="modal fade" id="modal-transportista" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content text-start" role="document">
      <div class="modal-header text-white bg-secondary pt-3 pb-2">
        <h5 id="modal-title">Agregar Transportista</h5>
        <button type="button" class="btn-close position-absolute top-0 end-0 mt-3 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" id="form-transportista" name="form-transportista" autocomplete="off">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Transportista <span class="text-danger">(*)</span></label>
              <input type="text" name="transportista" id="transportista" class="form-control" required>
              <div class="invalid-feedback" id="transportista-error"></div>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">CUIT</label>
              <input type="text" name="cuit" id="cuit" class="form-control" maxlength="50" placeholder="Ej: 33-70857951-9">
              <div class="invalid-feedback" id="cuit-error"></div>
            </div>
            <div class="col-12">
              <label class="form-label">Dirección</label>
              <input type="text" name="direccion" id="direccion" class="form-control">
              <div class="invalid-feedback" id="direccion-error"></div>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Localidad</label>
              <input type="text" name="localidad" id="localidad" class="form-control">
              <div class="invalid-feedback" id="localidad-error"></div>
            </div>
            <div class="col-12 col-md-3">
              <label class="form-label">Código Postal</label>
              <input type="text" name="codigo_postal" id="codigo_postal" class="form-control">
              <div class="invalid-feedback" id="codigo_postal-error"></div>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Nación <span class="text-danger">(*)</span></label>
              <select name="pais_id" id="pais_id" class="form-control" required>
                <option value="">Seleccione un país</option>
                <?php foreach($paises as $pais): ?>
                <option value="<?= $pais['id'] ?>"><?= esc($pais['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback" id="pais_id-error"></div>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Provincia</label>
              <select name="provincia_id" id="provincia_id" class="form-control" disabled>
                <option value="">Primero seleccione un país</option>
              </select>
              <div class="invalid-feedback" id="provincia_id-error"></div>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Teléfono</label>
              <input type="text" name="telefono" id="telefono" class="form-control">
              <div class="invalid-feedback" id="telefono-error"></div>
            </div>
            <div class="col-12">
              <label class="form-label">Mail Contacto</label>
              <input type="email" name="mail_contacto" id="mail_contacto" class="form-control" multiple>
              <small class="text-muted">Para varias direcciones separar cada una con ";" sin espacios y también al final.</small>
              <div class="invalid-feedback" id="mail_contacto-error"></div>
            </div>
            <div class="col-12">
              <label class="form-label">Comentarios</label>
              <textarea name="comentarios" id="comentarios" class="form-control" rows="3"></textarea>
              <div class="invalid-feedback" id="comentarios-error"></div>
            </div>
            <!-- Documentación (solo al editar) -->
            <div class="col-12 border-top pt-3 mt-2" id="bloque-documentos-transportista" style="display: none;">
              <label class="form-label fw-bold">Documentación</label>
              <p class="text-muted small mb-2">Imágenes (jpg, png, gif, webp) o PDF. Máx. 10 MB.</p>
              <div class="mb-2 d-flex flex-wrap gap-2 align-items-center">
                <input type="file" id="archivo-doc-transportista" class="form-control form-control-sm" style="max-width: 260px;" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf">
                <button type="button" class="btn btn-sm btn-outline-primary" id="btn-subir-doc-transportista">Subir</button>
              </div>
              <ul id="lista-docs-transportista" class="list-group list-group-flush small"></ul>
            </div>
          </div>
          <input type="hidden" name="id_tta" id="id_tta">
        </form>
      </div>
      <div class="modal-footer">
        <button id="btn-guardar-transportista" class="btn btn-primary btn-tablet" type="button">
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

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
  var table;
  var modal = new bootstrap.Modal(document.getElementById('modal-transportista'));
  
  // Inicializar DataTable
  table = $('#transportistas-datatable').DataTable({
    language: {
      url: '<?= base_url('assets/js/datatable/esp.json') ?>'
    },
    processing: true,
    serverSide: false,
    responsive: true,
    pageLength: 10,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
    ajax: {
      url: '<?= site_url('transportistas/listar') ?>',
      type: 'POST'
    },
    columns: [
      { data: 'transportista' },
      { data: 'direccion', responsivePriority: 3 },
      { data: 'localidad', responsivePriority: 4 },
      { data: 'provincia', responsivePriority: 5 },
      { data: 'nacion', responsivePriority: 6 },
      { data: 'telefono', responsivePriority: 7 },
      { 
        data: null,
        orderable: false,
        className: 'text-center',
        responsivePriority: 1,
        render: function(data, type, row) {
          return '<div class="btn-group" role="group">' +
                   '<a href="<?= site_url('transportistas/ver/') ?>' + row.id_tta + '" class="btn btn-sm btn-outline-secondary btn-tablet" title="Ver">' +
                     '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">' +
                       '<path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>' +
                       '<path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>' +
                     '</svg></a>' +
                   '<button class="btn btn-sm btn-primary btn-tablet editar-transportista" data-id="' + row.id_tta + '" title="Editar">' +
                     '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">' +
                       '<path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>' +
                     '</svg>' +
                   '</button>' +
                   '<button class="btn btn-sm btn-danger btn-tablet eliminar-transportista" data-id="' + row.id_tta + '" title="Eliminar">' +
                     '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">' +
                       '<path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>' +
                       '<path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>' +
                     '</svg>' +
                   '</button>' +
                 '</div>';
        }
      }
    ],
    order: [[0, 'asc']],
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
  });

  // Limpiar todos los errores de validación
  function limpiarErrores() {
    $('.form-control, .form-select').removeClass('is-invalid');
    $('.invalid-feedback').text('').hide();
  }

  // Mostrar errores de validación
  function mostrarErrores(errors) {
    limpiarErrores();
    
    if (errors && typeof errors === 'object') {
      $.each(errors, function(field, message) {
        var fieldElement = $('#' + field);
        var errorElement = $('#' + field + '-error');
        
        if (fieldElement.length) {
          fieldElement.addClass('is-invalid');
          if (errorElement.length) {
            errorElement.text(message).show();
          } else {
            // Si no existe el div de error, crear uno
            fieldElement.after('<div class="invalid-feedback" id="' + field + '-error">' + message + '</div>');
            $('#' + field + '-error').show();
          }
        }
      });
    }
  }

  // Limpiar formulario del modal
  function limpiarFormulario() {
    $('#form-transportista')[0].reset();
    $('#id_tta').val('');
    $('#modal-title').text('Agregar Transportista');
    $('#bloque-documentos-transportista').hide();
    $('#lista-docs-transportista').empty();
    $('#archivo-doc-transportista').val('');
    limpiarErrores();
  }

  // Documentación: listar
  function loadDocumentosTransportista(idEntidad) {
    var $lista = $('#lista-docs-transportista');
    $lista.html('<li class="list-group-item text-muted">Cargando...</li>');
    $.get('<?= site_url('documentos/listar/transportista/') ?>' + idEntidad)
      .done(function(res) {
        if (!res.success) {
          $lista.html('<li class="list-group-item text-danger">Error al cargar documentos.</li>');
          return;
        }
        $lista.empty();
        if (!res.data || res.data.length === 0) {
          $lista.html('<li class="list-group-item text-muted">No hay documentos.</li>');
          return;
        }
        $.each(res.data, function(i, doc) {
          var verBtn = '<button type="button" class="btn btn-sm btn-outline-secondary btn-tablet ver-doc" data-id="' + doc.id + '" data-nombre="' + (doc.nombre_original || '').replace(/"/g, '&quot;') + '" data-es-imagen="' + (doc.es_imagen ? '1' : '0') + '" data-url="' + (doc.url_ver || '') + '">Ver</button>';
          var delBtn = '<button type="button" class="btn btn-sm btn-outline-danger btn-tablet eliminar-doc" data-id="' + doc.id + '">Eliminar</button>';
          $lista.append('<li class="list-group-item d-flex justify-content-between align-items-center">' +
            '<span class="text-break">' + (doc.nombre_original || doc.nombre_archivo) + '</span> ' +
            '<span class="ms-2">' + verBtn + ' ' + delBtn + '</span></li>');
        });
      })
      .fail(function() {
        $lista.html('<li class="list-group-item text-danger">Error al cargar documentos.</li>');
      });
  }

  // Documentación: subir
  $('#btn-subir-doc-transportista').on('click', function() {
    var idEntidad = $('#id_tta').val();
    if (!idEntidad) return;
    var input = document.getElementById('archivo-doc-transportista');
    if (!input.files || !input.files[0]) {
      Swal.fire({ icon: 'warning', title: 'Aviso', text: 'Seleccione un archivo', confirmButtonText: 'Aceptar' });
      return;
    }
    var fd = new FormData();
    fd.append('archivo', input.files[0]);
    var $btn = $(this).prop('disabled', true);
    $.ajax({
      url: '<?= site_url('documentos/subir/transportista/') ?>' + idEntidad,
      type: 'POST',
      data: fd,
      processData: false,
      contentType: false,
      dataType: 'json'
    }).done(function(res) {
      if (res.success) {
        loadDocumentosTransportista(idEntidad);
        input.value = '';
        Swal.fire({ icon: 'success', title: res.message, confirmButtonText: 'Aceptar' });
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Error al subir', confirmButtonText: 'Aceptar' });
      }
    }).fail(function() {
      Swal.fire({ icon: 'error', title: 'Error', text: 'Error al subir el archivo', confirmButtonText: 'Aceptar' });
    }).always(function() {
      $btn.prop('disabled', false);
    });
  });

  // Documentación: abrir vista previa
  $(document).on('click', '.ver-doc', function() {
    var id = $(this).data('id');
    var nombre = $(this).data('nombre') || 'Documento';
    var esImagen = $(this).data('es-imagen') === 1 || $(this).data('es-imagen') === '1';
    var url = $(this).data('url') || ('<?= site_url('documentos/ver/') ?>' + id);
    var ext = (nombre.split('.').pop() || '').toLowerCase();
    var esPdf = ext === 'pdf';
    $('#modal-preview-doc-title').text(nombre);
    $('#preview-doc-imagen').hide().attr('src', '');
    $('#preview-doc-iframe').hide().attr('src', '');
    $('#preview-doc-sin-preview').hide();
    $('#preview-doc-descarga').attr('href', url);
    if (esImagen) {
      $('#preview-doc-imagen').attr('src', url).show();
    } else if (esPdf) {
      $('#preview-doc-iframe').attr('src', url).show();
    } else {
      $('#preview-doc-sin-preview').show();
    }
    new bootstrap.Modal(document.getElementById('modal-preview-doc')).show();
  });

  // Documentación: eliminar
  $(document).on('click', '.eliminar-doc', function() {
    var id = $(this).data('id');
    var idEntidad = $('#id_tta').val();
    if (!idEntidad) return;
    Swal.fire({
      title: '¿Eliminar documento?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonText: 'Cancelar',
      confirmButtonText: 'Sí, eliminar'
    }).then(function(result) {
      if (result.isConfirmed) eliminarDocumento(id, idEntidad);
    });
  });
  function eliminarDocumento(id, idEntidad) {
    $.post('<?= site_url('documentos/eliminar/') ?>' + id, {})
      .done(function(res) {
        if (res.success) {
          loadDocumentosTransportista(idEntidad);
          Swal.fire({ icon: 'success', title: res.message, confirmButtonText: 'Aceptar' });
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Error al eliminar', confirmButtonText: 'Aceptar' });
        }
      })
      .fail(function() {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error al eliminar', confirmButtonText: 'Aceptar' });
      });
  }

  // Cargar provincias cuando se selecciona un país
  $('#pais_id').on('change', function() {
    var paisId = $(this).val();
    var provinciaSelect = $('#provincia_id');
    
    if (paisId) {
      provinciaSelect.prop('disabled', false);
      provinciaSelect.html('<option value="">Cargando...</option>');
      
      $.ajax({
        url: '<?= site_url('transportistas/provinciasPorPais/') ?>' + paisId,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
          if (response.success && response.data.length > 0) {
            var options = '<option value="">Seleccione una provincia</option>';
            $.each(response.data, function(index, provincia) {
              options += '<option value="' + provincia.id + '">' + provincia.nombre + '</option>';
            });
            provinciaSelect.html(options);
          } else {
            provinciaSelect.html('<option value="">No hay provincias disponibles</option>');
          }
        },
        error: function() {
          provinciaSelect.html('<option value="">Error al cargar provincias</option>');
        }
      });
    } else {
      provinciaSelect.prop('disabled', true);
      provinciaSelect.html('<option value="">Primero seleccione un país</option>');
    }
  });

  // Cargar registro en el modal
  function cargarRegistro(id) {
    $.ajax({
      url: '<?= site_url('transportistas/obtener/') ?>' + id,
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          var data = response.data;
          $('#id_tta').val(data.id_tta);
          $('#transportista').val(data.transportista);
          $('#cuit').val(data.cuit || '');
          $('#direccion').val(data.direccion || '');
          $('#localidad').val(data.localidad || '');
          $('#codigo_postal').val(data.codigo_postal || '');
          $('#mail_contacto').val(data.mail_contacto || '');
          $('#telefono').val(data.telefono || '');
          $('#comentarios').val(data.comentarios || '');
          
          // Cargar país y provincia si existen
          if (data.pais_id) {
            $('#pais_id').val(data.pais_id).trigger('change');
            // Esperar a que se carguen las provincias antes de seleccionar
            setTimeout(function() {
              if (data.provincia_id) {
                $('#provincia_id').val(data.provincia_id);
              }
            }, 500);
          }
          
          $('#modal-title').text('Editar Transportista');
          $('#bloque-documentos-transportista').show();
          loadDocumentosTransportista(data.id_tta);
          modal.show();
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo cargar el registro',
            confirmButtonText: 'Aceptar'
          });
        }
      },
      error: function() {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error al cargar el registro',
          confirmButtonText: 'Aceptar'
        });
      }
    });
  }

  // Botón nuevo transportista
  $('#btn-nuevo-transportista').on('click', function() {
    limpiarFormulario();
  });

  // Función para validar email múltiple
  function validarEmailMultiple(email) {
    if (!email || email.trim() === '') {
      return true; // Vacío es válido (opcional)
    }
    
    var emails = email.split(';');
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    for (var i = 0; i < emails.length; i++) {
      var emailTrimmed = emails[i].trim();
      if (emailTrimmed !== '' && !emailRegex.test(emailTrimmed)) {
        return false;
      }
    }
    
    return true;
  }

  // Guardar transportista
  $('#btn-guardar-transportista').on('click', function() {
    // Limpiar errores previos
    limpiarErrores();
    
    // Validar campo obligatorio
    if (!$('#transportista').val().trim()) {
      $('#transportista').addClass('is-invalid');
      $('#transportista-error').text('El campo Transportista es obligatorio').show();
      Swal.fire({
        icon: 'warning',
        title: 'Campo requerido',
        text: 'El campo Transportista es obligatorio',
        confirmButtonText: 'Aceptar'
      });
      $('#transportista').focus();
      return;
    }

    // Validar email
    var mailContacto = $('#mail_contacto').val();
    if (mailContacto && !validarEmailMultiple(mailContacto)) {
      $('#mail_contacto').addClass('is-invalid');
      $('#mail_contacto-error').text('Uno o más emails no son válidos. Separe múltiples emails con ";"').show();
      Swal.fire({
        icon: 'warning',
        title: 'Email inválido',
        text: 'Uno o más emails no son válidos. Verifique que estén separados por ";" y que cada email sea válido.',
        confirmButtonText: 'Aceptar'
      });
      $('#mail_contacto').focus();
      return;
    }

    var formData = $('#form-transportista').serialize();
    
    $.ajax({
      url: '<?= site_url('transportistas/guardar') ?>',
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          Swal.fire({
            icon: 'success',
            title: 'Éxito',
            text: 'Transportista guardado correctamente',
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#0d6efd'
          });
          table.ajax.reload();
          modal.hide();
          limpiarFormulario();
        } else {
          // Si hay errores de validación, mostrarlos en los campos
          if (response.errors && Object.keys(response.errors).length > 0) {
            mostrarErrores(response.errors);
            Swal.fire({
              icon: 'error',
              title: 'Error de validación',
              text: response.message || 'Por favor, corrija los errores en el formulario',
              confirmButtonText: 'Aceptar'
            });
            
            // Scroll al primer campo con error
            var firstError = $('.is-invalid').first();
            if (firstError.length) {
              $('html, body').animate({
                scrollTop: firstError.offset().top - 100
              }, 500);
              firstError.focus();
            }
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: response.message || 'Error al guardar el transportista',
              confirmButtonText: 'Aceptar'
            });
          }
        }
      },
      error: function() {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error al guardar el transportista',
          confirmButtonText: 'Aceptar'
        });
      }
    });
  });

  // Editar desde la tabla
  $(document).on('click', '.editar-transportista', function() {
    var id = $(this).data('id');
    cargarRegistro(id);
  });

  // Eliminar desde la tabla
  $(document).on('click', '.eliminar-transportista', function() {
    var id = $(this).data('id');
    var transportista = $(this).closest('tr').find('td:first').text();
    Swal.fire({
      title: '¿Está seguro?',
      text: 'Se eliminará el transportista: ' + transportista,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if (result.isConfirmed) {
        eliminarRegistro(id);
      }
    });
  });

  function eliminarRegistro(id) {
    $.ajax({
      url: '<?= site_url('transportistas/eliminar/') ?>' + id,
      type: 'POST',
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          Swal.fire({
            icon: 'success',
            title: 'Eliminado',
            text: 'Transportista eliminado correctamente',
            confirmButtonText: 'Aceptar'
          });
          table.ajax.reload();
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: response.message || 'Error al eliminar el registro',
            confirmButtonText: 'Aceptar'
          });
        }
      }
    });
  }

  // Validar email en tiempo real
  $('#mail_contacto').on('blur', function() {
    var mailContacto = $(this).val();
    if (mailContacto && !validarEmailMultiple(mailContacto)) {
      $(this).addClass('is-invalid');
      $('#mail_contacto-error').text('Uno o más emails no son válidos. Separe múltiples emails con ";"').show();
    } else {
      $(this).removeClass('is-invalid');
      $('#mail_contacto-error').text('').hide();
    }
  });

  // Limpiar errores al escribir en los campos
  $('.form-control, .form-select').on('input change', function() {
    if ($(this).hasClass('is-invalid')) {
      $(this).removeClass('is-invalid');
      var fieldName = $(this).attr('id');
      $('#' + fieldName + '-error').text('').hide();
    }
  });

  // Limpiar formulario al cerrar el modal
  $('#modal-transportista').on('hidden.bs.modal', function() {
    limpiarFormulario();
    $('#provincia_id').prop('disabled', true);
  });
});
</script>
<?= $this->endSection() ?>
