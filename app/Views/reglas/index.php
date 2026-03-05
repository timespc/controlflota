<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
Reglas
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
  @media (max-width: 768px) {
    .btn-tablet { padding: 12px 20px; font-size: 16px; min-height: 44px; }
    .form-control, .form-select { font-size: 16px; min-height: 44px; }
    .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
    #modal-regla .modal-dialog { max-width: 95%; margin: 1rem auto; }
  }
  .btn-tablet { padding: 10px 18px; font-size: 15px; min-height: 42px; touch-action: manipulation; }
  .dataTables_wrapper .dataTables_filter input,
  .dataTables_wrapper .dataTables_length select { min-height: 38px; font-size: 15px; }
  .invalid-feedback { display: block; width: 100%; margin-top: 0.25rem; font-size: 0.875em; color: #dc3545; }
  .is-invalid { border-color: #dc3545; padding-right: calc(1.5em + 0.75rem); background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 .4.4.4-.4m0 4.8-.4-.4-.4.4'/%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right calc(0.375em + 0.1875rem) center; background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem); }
</style>
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <div class="card border border-secondary">
      <div class="card-header text-white bg-secondary pt-3 pb-2 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Reglas (varilla de medición)</h5>
        <button type="button" class="btn btn-primary btn-tablet" data-bs-toggle="modal" data-bs-target="#modal-regla" id="btn-nueva-regla">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-plus-circle" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
          </svg>
          Agregar Regla
        </button>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table id="reglas-datatable" class="table table-striped table-bordered table-hover" style="width:100%">
            <thead>
              <tr>
                <th>Nº de regla (serie)</th>
                <th class="text-center">Habilitada</th>
                <th>Creado por</th>
                <th>Creado</th>
                <th>Actualizado</th>
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
<div class="modal fade" id="modal-regla" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-start" role="document">
      <div class="modal-header text-white bg-secondary pt-3 pb-2">
        <h5 id="modal-title">Agregar Regla</h5>
        <button type="button" class="btn-close position-absolute top-0 end-0 mt-3 me-3" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" id="form-regla" name="form-regla" autocomplete="off">
          <input type="hidden" name="id_regla" id="id_regla" value="">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">Número de regla (nº de serie) <span class="text-danger">(*)</span></label>
              <input type="text" name="numero_regla" id="numero_regla" class="form-control" required maxlength="100" placeholder="Ej: R-001">
              <div class="invalid-feedback" id="numero_regla-error"></div>
              <small class="text-muted" id="hint-agregar">La nueva regla queda habilitada y la anterior (si había) se deshabilita automáticamente.</small>
            </div>
            <div class="col-12" id="wrap-habilitada" style="display:none;">
              <label class="form-label">Habilitada</label>
              <select name="habilitada" id="habilitada" class="form-select">
                <option value="1">Sí (en uso)</option>
                <option value="0">No (deshabilitada)</option>
              </select>
              <small class="text-muted">Marcar como deshabilitada cuando la regla deje de usarse; luego agregar la nueva.</small>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button id="btn-guardar-regla" class="btn btn-primary btn-tablet" type="button">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-save" viewBox="0 0 16 16"><path d="M2 1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H9.5a1 1 0 0 0-1 1v7.293l2.646-2.647a.5.5 0 0 1 .708.708l-3.5 3.5a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L7.5 9.293V2a2 2 0 0 1 2-2H14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2z"/></svg>
          Guardar
        </button>
        <button class="btn btn-secondary btn-tablet" type="button" data-bs-dismiss="modal">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-x-circle" viewBox="0 0 16 16"><path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>
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
  var modalEl = document.getElementById('modal-regla');
  var modal = modalEl ? new bootstrap.Modal(modalEl) : null;

  function fmtFecha(s) {
    if (!s) return '—';
    var d = new Date(s);
    if (isNaN(d.getTime())) return s;
    return ('0' + d.getDate()).slice(-2) + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + d.getFullYear() + ' ' + ('0' + d.getHours()).slice(-2) + ':' + ('0' + d.getMinutes()).slice(-2);
  }

  table = $('#reglas-datatable').DataTable({
    language: { url: '<?= base_url('assets/js/datatable/esp.json') ?>' },
    processing: true,
    serverSide: false,
    responsive: true,
    pageLength: 10,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
    ajax: { url: '<?= site_url('reglas/listar') ?>', type: 'POST' },
    columns: [
      { data: 'numero_regla' },
      {
        data: 'habilitada',
        className: 'text-center',
        render: function(data) {
          var v = parseInt(data, 10);
          if (v === 1) return '<span class="badge bg-success">Habilitada</span>';
          return '<span class="badge bg-secondary">Deshabilitada</span>';
        }
      },
      { data: 'usuario_creacion_nombre', defaultContent: '—', orderable: true },
      { data: 'created_at', render: function(d) { return fmtFecha(d); } },
      { data: 'updated_at', render: function(d) { return fmtFecha(d); } }
    ],
    order: [[1, 'desc'], [0, 'asc']],
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
  });

  function limpiarErrores() {
    $('#numero_regla').removeClass('is-invalid');
    $('#numero_regla-error').text('').hide();
  }

  function mostrarErrores(errors) {
    limpiarErrores();
    if (errors && typeof errors === 'object' && errors.numero_regla) {
      $('#numero_regla').addClass('is-invalid');
      $('#numero_regla-error').text(errors.numero_regla).show();
    }
  }

  function limpiarFormulario() {
    $('#form-regla')[0].reset();
    $('#id_regla').val('');
    $('#wrap-habilitada').hide();
    $('#hint-agregar').show();
    $('#habilitada').val('1');
    $('#modal-title').text('Agregar Regla');
    limpiarErrores();
  }

  $('#btn-nueva-regla').on('click', function() { limpiarFormulario(); });

  $('#btn-guardar-regla').on('click', function() {
    limpiarErrores();
    if (!$('#numero_regla').val().trim()) {
      $('#numero_regla').addClass('is-invalid');
      $('#numero_regla-error').text('El número de regla es obligatorio').show();
      Swal.fire({ icon: 'warning', title: 'Campo requerido', text: 'El número de regla es obligatorio', confirmButtonText: 'Aceptar' });
      $('#numero_regla').focus();
      return;
    }
    var esNuevo = !$('#id_regla').val();
    if (esNuevo) $('#habilitada').val('1');
    $.ajax({
      url: '<?= site_url('reglas/guardar') ?>',
      type: 'POST',
      data: $('#form-regla').serialize(),
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
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error al guardar la regla', confirmButtonText: 'Aceptar' });
      }
    });
  });

  $('#numero_regla').on('input', function() {
    if ($(this).hasClass('is-invalid')) { $(this).removeClass('is-invalid'); $('#numero_regla-error').text('').hide(); }
  });

  if (modalEl) {
    modalEl.addEventListener('hidden.bs.modal', function() { limpiarFormulario(); });
  }
});
</script>
<?= $this->endSection() ?>
