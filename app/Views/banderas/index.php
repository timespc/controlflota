<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
Banderas
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
  /* Responsive global: ver public/assets/css/custom.css (btn-tablet, table-responsive, modales) */
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

  /* Columna Acciones reducida (~10%) */
  #banderas-datatable th:nth-child(2),
  #banderas-datatable td:nth-child(2) {
    width: 10%;
    max-width: 10%;
    white-space: nowrap;
  }
</style>
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <div class="card border border-secondary">
      <div class="card-header text-white bg-secondary pt-3 pb-2 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Banderas</h5>
        <button type="button" class="btn btn-primary btn-tablet" data-bs-toggle="modal" data-bs-target="#modal-bandera" id="btn-nueva-bandera">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
          </svg>
          Agregar Bandera
        </button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="banderas-datatable" class="table table-striped table-bordered table-hover" style="width:100%">
            <thead>
              <tr>
                <th>Bandera</th>
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
<div class="modal fade" id="modal-bandera" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-start" role="document">
      <div class="modal-header text-white bg-secondary pt-3 pb-2">
        <h5 id="modal-title">Agregar Bandera</h5>
        <button type="button" class="btn-close position-absolute top-0 end-0 mt-3 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" id="form-bandera" name="form-bandera" autocomplete="off">
          <input type="hidden" name="id_bandera" id="id_bandera" value="">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Bandera <span class="text-danger">(*)</span></label>
              <input type="text" name="bandera" id="bandera" class="form-control" required maxlength="255">
              <div class="invalid-feedback" id="bandera-error"></div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button id="btn-guardar-bandera" class="btn btn-primary btn-tablet" type="button">
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
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
  var table;
  var modalEl = document.getElementById('modal-bandera');
  var modal = modalEl ? new bootstrap.Modal(modalEl) : null;

  table = $('#banderas-datatable').DataTable({
    language: { url: '<?= base_url('assets/js/datatable/esp.json') ?>' },
    processing: true,
    serverSide: false,
    responsive: true,
    pageLength: 10,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
    ajax: {
      url: '<?= site_url('banderas/listar') ?>',
      type: 'POST'
    },
    columnDefs: [
      { targets: 1, width: '10%' }
    ],
    columns: [
      { data: 'bandera' },
      {
        data: null,
        orderable: false,
        className: 'text-center',
        render: function(data, type, row) {
          return '<div class="btn-group" role="group">' +
            '<button class="btn btn-sm btn-primary btn-tablet editar-bandera" data-id="' + row.id_bandera + '" title="Editar">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/></svg>' +
            '</button>' +
            '<button class="btn btn-sm btn-danger btn-tablet eliminar-bandera" data-id="' + row.id_bandera + '" title="Eliminar">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>' +
            '</button>' +
            '</div>';
        }
      }
    ],
    order: [[0, 'asc']],
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
  });

  function limpiarErrores() {
    $('#bandera').removeClass('is-invalid');
    $('#bandera-error').text('').hide();
  }

  function mostrarErrores(errors) {
    limpiarErrores();
    if (errors && typeof errors === 'object' && errors.bandera) {
      $('#bandera').addClass('is-invalid');
      $('#bandera-error').text(errors.bandera).show();
    }
  }

  function limpiarFormulario() {
    $('#form-bandera')[0].reset();
    $('#id_bandera').val('');
    $('#modal-title').text('Agregar Bandera');
    limpiarErrores();
  }

  function cargarRegistro(id) {
    $.ajax({
      url: '<?= site_url('banderas/obtener/') ?>' + id,
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          var d = response.data;
          $('#id_bandera').val(d.id_bandera);
          $('#bandera').val(d.bandera);
          $('#modal-title').text('Editar Bandera');
          if (modal) modal.show();
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo cargar el registro', confirmButtonText: 'Aceptar' });
        }
      },
      error: function() {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error al cargar el registro', confirmButtonText: 'Aceptar' });
      }
    });
  }

  $('#btn-nueva-bandera').on('click', function() { limpiarFormulario(); });

  $('#btn-guardar-bandera').on('click', function() {
    limpiarErrores();
    if (!$('#bandera').val().trim()) {
      $('#bandera').addClass('is-invalid');
      $('#bandera-error').text('El campo Bandera es obligatorio').show();
      Swal.fire({ icon: 'warning', title: 'Campo requerido', text: 'El campo Bandera es obligatorio', confirmButtonText: 'Aceptar' });
      $('#bandera').focus();
      return;
    }
    $.ajax({
      url: '<?= site_url('banderas/guardar') ?>',
      type: 'POST',
      data: $('#form-bandera').serialize(),
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          Swal.fire({ icon: 'success', title: 'Éxito', text: response.message, confirmButtonText: 'Aceptar', confirmButtonColor: '#0d6efd' });
          table.ajax.reload();
          if (modal) modal.hide();
          limpiarFormulario();
        } else {
          if (response.errors) mostrarErrores(response.errors);
          Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Error al guardar', confirmButtonText: 'Aceptar' });
        }
      },
      error: function() {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error al guardar la bandera', confirmButtonText: 'Aceptar' });
      }
    });
  });

  $(document).on('click', '.editar-bandera', function() { cargarRegistro($(this).data('id')); });

  $(document).on('click', '.eliminar-bandera', function() {
    var id = $(this).data('id');
    var nombre = $(this).closest('tr').find('td:first').text();
    Swal.fire({
      title: '¿Está seguro?',
      text: 'Se eliminará la bandera: ' + nombre,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    }).then(function(result) { if (result.isConfirmed) eliminarRegistro(id); });
  });

  function eliminarRegistro(id) {
    $.ajax({
      url: '<?= site_url('banderas/eliminar/') ?>' + id,
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

  $('#bandera').on('input', function() {
    if ($(this).hasClass('is-invalid')) { $(this).removeClass('is-invalid'); $('#bandera-error').text('').hide(); }
  });

  if (modalEl) {
    modalEl.addEventListener('hidden.bs.modal', function() { limpiarFormulario(); });
  }
});
</script>
<?= $this->endSection() ?>
