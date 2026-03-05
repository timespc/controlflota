<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
Choferes - Montajes Campana
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
  .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
  .form-control, .form-select { font-size: 16px; min-height: 44px; }
  .invalid-feedback { display: block; font-size: 0.875em; color: #dc3545; }
  .is-invalid { border-color: #dc3545; }
  .form-text-docum { font-size: 0.8rem; color: #6c757d; }
  .form-text-tta { font-size: 0.8rem; color: #dc3545; }
</style>
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= site_url() ?>">Inicio</a></li>
        <li class="breadcrumb-item active">Choferes</li>
      </ol>
    </nav>
    <div class="card border border-secondary">
      <div class="card-header text-white bg-secondary pt-3 pb-2 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Choferes</h5>
        <button type="button" class="btn btn-primary btn-tablet" data-bs-toggle="modal" data-bs-target="#modal-chofer" id="btn-nuevo-chofer">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
          </svg>
          Agregar chofer
        </button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="choferes-datatable" class="table table-striped table-bordered table-hover" style="width:100%">
            <thead>
              <tr>
                <th>Documento</th>
                <th>Chofer</th>
                <th>Nación</th>
                <th>Transportista</th>
                <th>Ult. actualiz.</th>
                <th class="text-center">Acciones</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Agregar/Editar -->
<div class="modal fade" id="modal-chofer" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header text-white bg-secondary">
        <h5 class="modal-title" id="modal-chofer-title">Agregar Chofer</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="form-chofer" autocomplete="off">
          <input type="hidden" name="id" id="chofer_id" value="">
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Documento <span class="text-danger">(*)</span></label>
              <input type="text" name="documento" id="chofer_documento" class="form-control" required placeholder="DNI, REG.IDENTIDADE CIVIL, CED.IDENTIDAD CHILENA, CED.IDENTIDAD CIVIL (PAR), DU (URU)">
              <div class="invalid-feedback" id="chofer_documento_error"></div>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Chofer <span class="text-danger">(*)</span></label>
              <input type="text" name="nombre" id="chofer_nombre" class="form-control" required placeholder="Nombre del chofer">
              <div class="invalid-feedback" id="chofer_nombre_error"></div>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Nación</label>
              <select name="id_nacion" id="chofer_id_nacion" class="form-select">
                <option value="">— Seleccione —</option>
                <?php foreach ($naciones as $n): ?>
                <option value="<?= (int) $n['id_nacion'] ?>"><?= esc($n['nacion']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Transportista (TTA)</label>
              <select name="id_tta" id="chofer_id_tta" class="form-select">
                <option value="">— Seleccione —</option>
                <?php foreach ($transportistas as $t): ?>
                <option value="<?= (int) $t['id_tta'] ?>" data-id-tta="<?= (int) $t['id_tta'] ?>"><?= esc($t['transportista']) ?></option>
                <?php endforeach; ?>
              </select>
              <span class="form-text form-text-tta">Para choferes de Matlack seleccione Transportes Jose Beraldi (CIF)-COD 212.</span>
            </div>
            <div class="col-12">
              <label class="form-label">Comentarios</label>
              <textarea name="comentarios" id="chofer_comentarios" class="form-control" rows="3" placeholder="Comentarios"></textarea>
            </div>
            <div class="col-12 col-md-6" id="wrap-ult-act" style="display:none;">
              <label class="form-label">Ult. actualiz.</label>
              <input type="text" id="chofer_ult_actualiz" class="form-control" readonly>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btn-guardar-chofer">Guardar</button>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
  var table;
  var modalEl = document.getElementById('modal-chofer');
  var modal = modalEl ? new bootstrap.Modal(modalEl) : null;

  table = $('#choferes-datatable').DataTable({
    language: { url: '<?= base_url('assets/js/datatable/esp.json') ?>' },
    processing: true,
    serverSide: false,
    responsive: true,
    pageLength: 10,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
    ajax: { url: '<?= site_url('choferes/listar') ?>', type: 'POST' },
    columns: [
      { data: 'documento' },
      { data: 'nombre' },
      { data: 'nacion', defaultContent: '—' },
      { data: 'transportista', defaultContent: '—' },
      { data: 'ult_actualiz', defaultContent: '—' },
      {
        data: null,
        orderable: false,
        className: 'text-center',
        render: function(d, type, row) {
          return '<div class="btn-group btn-group-sm">' +
            '<button class="btn btn-primary editar-chofer" data-id="' + row.id + '" title="Editar">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/></svg></button>' +
            '<button class="btn btn-danger eliminar-chofer" data-id="' + row.id + '" data-nombre="' + (row.nombre || '').replace(/"/g, '&quot;') + '" title="Eliminar">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg></button>' +
            '</div>';
        }
      }
    ],
    order: [[0, 'asc']],
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
  });

  function limpiarFormulario() {
    $('#form-chofer')[0].reset();
    $('#chofer_id').val('');
    $('#chofer_documento_error').text('').parent().find('.form-control').removeClass('is-invalid');
    $('#chofer_nombre_error').text('').parent().find('.form-control').removeClass('is-invalid');
    $('#wrap-ult-act').hide();
    $('#modal-chofer-title').text('Agregar Chofer');
  }

  function cargarChofer(id) {
    $.ajax({
      url: '<?= site_url('choferes/obtener/') ?>' + id,
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          var d = response.data;
          $('#chofer_id').val(d.id);
          $('#chofer_documento').val(d.documento || '');
          $('#chofer_nombre').val(d.nombre || '');
          $('#chofer_id_nacion').val(d.id_nacion || '');
          $('#chofer_id_tta').val(d.id_tta || '');
          $('#chofer_comentarios').val(d.comentarios || '');
          if (d.updated_at) {
            var u = d.updated_at;
            if (u.indexOf('-') !== -1) {
              var parts = u.split(' '); var dPart = parts[0].split('-'); var tPart = parts[1] ? parts[1].substring(0, 8) : '00:00:00';
              $('#chofer_ult_actualiz').val(dPart[2] + '/' + dPart[1] + '/' + dPart[0] + ' ' + tPart);
            } else { $('#chofer_ult_actualiz').val(u); }
            $('#wrap-ult-act').show();
          } else { $('#wrap-ult-act').hide(); }
          $('#modal-chofer-title').text('Editar Chofer');
          if (modal) modal.show();
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'No se pudo cargar', confirmButtonText: 'Aceptar' });
        }
      },
      error: function() {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error al cargar el chofer', confirmButtonText: 'Aceptar' });
      }
    });
  }

  $('#btn-nuevo-chofer').on('click', function() { limpiarFormulario(); });

  $('#btn-guardar-chofer').on('click', function() {
    var doc = $('#chofer_documento').val().trim();
    var nom = $('#chofer_nombre').val().trim();
    $('#chofer_documento').removeClass('is-invalid');
    $('#chofer_nombre').removeClass('is-invalid');
    $('#chofer_documento_error').text('');
    $('#chofer_nombre_error').text('');
    if (!doc) {
      $('#chofer_documento').addClass('is-invalid');
      $('#chofer_documento_error').text('El documento es obligatorio');
    }
    if (!nom) {
      $('#chofer_nombre').addClass('is-invalid');
      $('#chofer_nombre_error').text('El nombre es obligatorio');
    }
    if (!doc || !nom) {
      Swal.fire({ icon: 'warning', title: 'Campos requeridos', text: 'Documento y Chofer son obligatorios', confirmButtonText: 'Aceptar' });
      return;
    }
    $.ajax({
      url: '<?= site_url('choferes/guardar') ?>',
      type: 'POST',
      data: $('#form-chofer').serialize(),
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          Swal.fire({ icon: 'success', title: 'Éxito', text: response.message, confirmButtonText: 'Aceptar' });
          table.ajax.reload();
          if (modal) modal.hide();
          limpiarFormulario();
        } else {
          if (response.errors) {
            if (response.errors.documento) { $('#chofer_documento').addClass('is-invalid'); $('#chofer_documento_error').text(response.errors.documento); }
            if (response.errors.nombre) { $('#chofer_nombre').addClass('is-invalid'); $('#chofer_nombre_error').text(response.errors.nombre); }
          }
          Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Error al guardar', confirmButtonText: 'Aceptar' });
        }
      },
      error: function() {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error al guardar', confirmButtonText: 'Aceptar' });
      }
    });
  });

  $(document).on('click', '.editar-chofer', function() { cargarChofer($(this).data('id')); });

  $(document).on('click', '.eliminar-chofer', function() {
    var id = $(this).data('id');
    var nombre = $(this).data('nombre') || '';
    Swal.fire({
      title: '¿Eliminar chofer?',
      text: 'Se eliminará a ' + nombre,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(function(result) {
      if (result.isConfirmed) {
        $.ajax({
          url: '<?= site_url('choferes/eliminar/') ?>' + id,
          type: 'POST',
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              Swal.fire({ icon: 'success', title: 'Eliminado', text: response.message, confirmButtonText: 'Aceptar' });
              table.ajax.reload();
            } else {
              Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Error al eliminar', confirmButtonText: 'Aceptar' });
            }
          }
        });
      }
    });
  });

  if (modalEl) modalEl.addEventListener('hidden.bs.modal', function() { limpiarFormulario(); });
});
</script>
<?= $this->endSection() ?>
