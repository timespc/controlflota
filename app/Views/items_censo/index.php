<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
Items Censo
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
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

  #items-censo-datatable th:nth-child(2),
  #items-censo-datatable td:nth-child(2) {
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
        <h5 class="mb-0">Items Censo</h5>
        <button type="button" class="btn btn-primary btn-tablet" data-bs-toggle="modal" data-bs-target="#modal-item-censo" id="btn-nuevo-item-censo">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
          </svg>
          Agregar Ítem
        </button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="items-censo-datatable" class="table table-striped table-bordered table-hover" style="width:100%">
            <thead>
              <tr>
                <th>Ítem</th>
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
<div class="modal fade" id="modal-item-censo" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-start" role="document">
      <div class="modal-header text-white bg-secondary pt-3 pb-2">
        <h5 id="modal-title">Agregar Ítem</h5>
        <button type="button" class="btn-close position-absolute top-0 end-0 mt-3 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" id="form-item-censo" name="form-item-censo" autocomplete="off">
          <input type="hidden" name="id_item_censo" id="id_item_censo" value="">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Ítem <span class="text-danger">(*)</span></label>
              <input type="text" name="item" id="item" class="form-control" required maxlength="255">
              <div class="invalid-feedback" id="item-error"></div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button id="btn-guardar-item-censo" class="btn btn-primary btn-tablet" type="button">
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
  var modalEl = document.getElementById('modal-item-censo');
  var modal = modalEl ? new bootstrap.Modal(modalEl) : null;

  table = $('#items-censo-datatable').DataTable({
    language: { url: '<?= base_url('assets/js/datatable/esp.json') ?>' },
    processing: true,
    serverSide: false,
    responsive: true,
    pageLength: 10,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
    ajax: {
      url: '<?= site_url('items-censo/listar') ?>',
      type: 'POST'
    },
    columnDefs: [
      { targets: 1, width: '10%' }
    ],
    columns: [
      { data: 'item' },
      {
        data: null,
        orderable: false,
        className: 'text-center',
        render: function(data, type, row) {
          return '<div class="btn-group" role="group">' +
            '<button class="btn btn-sm btn-primary btn-tablet editar-item-censo" data-id="' + row.id_item_censo + '" title="Editar">' +
            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/></svg>' +
            '</button>' +
            '<button class="btn btn-sm btn-danger btn-tablet eliminar-item-censo" data-id="' + row.id_item_censo + '" title="Eliminar">' +
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
    $('#item').removeClass('is-invalid');
    $('#item-error').text('').hide();
  }

  function mostrarErrores(errors) {
    limpiarErrores();
    if (errors && typeof errors === 'object' && errors.item) {
      $('#item').addClass('is-invalid');
      $('#item-error').text(errors.item).show();
    }
  }

  function limpiarFormulario() {
    $('#form-item-censo')[0].reset();
    $('#id_item_censo').val('');
    $('#modal-title').text('Agregar Ítem');
    limpiarErrores();
  }

  function cargarRegistro(id) {
    $.ajax({
      url: '<?= site_url('items-censo/obtener/') ?>' + id,
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          var d = response.data;
          $('#id_item_censo').val(d.id_item_censo);
          $('#item').val(d.item);
          $('#modal-title').text('Editar Ítem');
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

  $('#btn-nuevo-item-censo').on('click', function() { limpiarFormulario(); });

  $('#btn-guardar-item-censo').on('click', function() {
    limpiarErrores();
    if (!$('#item').val().trim()) {
      $('#item').addClass('is-invalid');
      $('#item-error').text('El campo Ítem es obligatorio').show();
      Swal.fire({ icon: 'warning', title: 'Campo requerido', text: 'El campo Ítem es obligatorio', confirmButtonText: 'Aceptar' });
      $('#item').focus();
      return;
    }
    $.ajax({
      url: '<?= site_url('items-censo/guardar') ?>',
      type: 'POST',
      data: $('#form-item-censo').serialize(),
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
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error al guardar el ítem', confirmButtonText: 'Aceptar' });
      }
    });
  });

  $(document).on('click', '.editar-item-censo', function() { cargarRegistro($(this).data('id')); });

  $(document).on('click', '.eliminar-item-censo', function() {
    var id = $(this).data('id');
    var nombre = $(this).closest('tr').find('td:first').text();
    Swal.fire({
      title: '¿Está seguro?',
      text: 'Se eliminará el ítem: ' + nombre,
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
      url: '<?= site_url('items-censo/eliminar/') ?>' + id,
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

  $('#item').on('input', function() {
    if ($(this).hasClass('is-invalid')) { $(this).removeClass('is-invalid'); $('#item-error').text('').hide(); }
  });

  if (modalEl) {
    modalEl.addEventListener('hidden.bs.modal', function() { limpiarFormulario(); });
  }
});
</script>
<?= $this->endSection() ?>
