<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
Usuarios - Montajes Campana
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
  .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
  .form-control, .form-select { font-size: 16px; min-height: 44px; }
  #usuarios-datatable th:nth-child(8), #usuarios-datatable td:nth-child(8) { width: 12%; }
  .badge-grupo { font-size: 0.8rem; }
</style>
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= site_url() ?>">Inicio</a></li>
        <li class="breadcrumb-item active">Usuarios</li>
      </ol>
    </nav>
    <div class="card border border-secondary">
      <div class="card-header text-white bg-secondary pt-3 pb-2 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Usuarios del sistema</h5>
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal-usuario" id="btn-nuevo-usuario">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-person-plus" viewBox="0 0 16 16"><path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H1s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C9.516 10.68 8.289 10 6 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/><path d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z"/></svg>
          Agregar usuario
        </button>
      </div>
      <div class="card-body">
        <p class="text-muted small mb-3">Los usuarios listados pueden iniciar sesión con Google. Agregá el correo para dar de alta a alguien nuevo.</p>
        <div class="table-responsive">
          <table id="usuarios-datatable" class="table table-striped table-bordered table-hover" style="width:100%">
            <thead>
              <tr>
                <th>Email</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Usuario</th>
                <th>Grupo</th>
                <th class="text-center">Activo</th>
                <th>Último login</th>
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
<div class="modal fade" id="modal-usuario" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header text-white bg-secondary">
        <h5 class="modal-title" id="modal-usuario-title">Agregar usuario</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="form-usuario" autocomplete="off">
          <input type="hidden" name="id" id="usuario_id" value="">
          <div class="mb-3">
            <label class="form-label">Email <span class="text-danger">(*)</span></label>
            <input type="email" name="email" id="usuario_email" class="form-control" required placeholder="correo@ejemplo.com">
            <div class="invalid-feedback" id="usuario_email_error"></div>
            <span class="form-text small" id="usuario_email_help">El usuario podrá entrar con Google con este correo.</span>
          </div>
          <div class="row g-2">
            <div class="col-md-6 mb-3">
              <label class="form-label">Nombre</label>
              <input type="text" name="first_name" id="usuario_nombre" class="form-control" placeholder="Ej: Juan">
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Apellido</label>
              <input type="text" name="last_name" id="usuario_apellido" class="form-control" placeholder="Ej: Pérez">
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Usuario (login)</label>
            <input type="text" name="username" id="usuario_username" class="form-control bg-light" readonly placeholder="Se calcula: primera letra del nombre + apellido en minúsculas">
            <span class="form-text small">Se calcula automáticamente a partir de nombre y apellido.</span>
          </div>
          <div class="mb-3">
            <label class="form-label">Grupo</label>
            <select name="grupo" id="usuario_grupo" class="form-select">
              <option value="calibrador">Calibrador</option>
              <option value="admin">Administrador</option>
            </select>
          </div>
          <div class="mb-3 d-none" id="wrap-usuario-active">
            <div class="form-check">
              <input type="checkbox" name="active" id="usuario_active" class="form-check-input" value="1">
              <label class="form-check-label" for="usuario_active">Usuario activo (puede entrar al sistema)</label>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btn-guardar-usuario">Guardar</button>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
var idUsuarioActual = <?= (int) ($id_usuario_actual ?? 0) ?>;
$(document).ready(function() {
  var table;
  var modalEl = document.getElementById('modal-usuario');
  var modal = modalEl ? new bootstrap.Modal(modalEl) : null;

  table = $('#usuarios-datatable').DataTable({
    language: { url: '<?= base_url('assets/js/datatable/esp.json') ?>' },
    processing: true,
    serverSide: false,
    responsive: true,
    pageLength: 10,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
    ajax: {
      url: '<?= site_url('usuarios/listar') ?>',
      type: 'POST'
    },
    columns: [
      { data: 'email' },
      { data: 'first_name', defaultContent: '—' },
      { data: 'last_name', defaultContent: '—' },
      { data: 'username', defaultContent: '—' },
      {
        data: 'grupo',
        render: function(val) {
          return val === 'admin'
            ? '<span class="badge bg-danger badge-grupo">Admin</span>'
            : '<span class="badge bg-secondary badge-grupo">Calibrador</span>';
        }
      },
      {
        data: 'active',
        className: 'text-center',
        render: function(val) {
          return val === 1 ? '<span class="text-success">Sí</span>' : '<span class="text-muted">No</span>';
        }
      },
      { data: 'last_login', defaultContent: '—' },
      {
        data: null,
        orderable: false,
        className: 'text-center',
        render: function(data, type, row) {
          var esAdminPrincipal = (row.id === 1);
          var esDev = (String(row.email || '').toLowerCase() === 'francurtofrd@gmail.com');
          var esProtegido = esAdminPrincipal || esDev;
          var btnEdit = '';
          if (!esProtegido) {
            btnEdit = '<button class="btn btn-sm btn-primary editar-usuario" data-id="' + row.id + '" title="Editar">' +
              '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/></svg></button>';
          }
          var esYo = (typeof idUsuarioActual !== 'undefined' && row.id === idUsuarioActual);
          var btnToggle = '';
          if (!esYo && !esProtegido) {
            btnToggle = row.active === 1
              ? '<button class="btn btn-sm btn-warning toggle-activo-usuario" data-id="' + row.id + '" data-active="0" title="Desactivar">Desactivar</button>'
              : '<button class="btn btn-sm btn-success toggle-activo-usuario" data-id="' + row.id + '" data-active="1" title="Activar">Activar</button>';
          }
          var btnRestablecer = '';
          if (!esProtegido) {
            btnRestablecer = '<button class="btn btn-sm btn-outline-secondary restablecer-password-usuario" data-id="' + row.id + '" data-email="' + (row.email || '').replace(/"/g, '&quot;') + '" title="Poner contraseña por defecto (password) y obligar a cambiarla">Restablecer contraseña</button>';
          }
          var btnEliminar = '';
          if (!esProtegido && !esYo) {
            btnEliminar = '<button class="btn btn-sm btn-danger eliminar-usuario" data-id="' + row.id + '" data-email="' + (row.email || '').replace(/"/g, '&quot;') + '" title="Eliminar usuario del sistema (acción irreversible)">Eliminar</button>';
          }
          var contenido = btnEdit + (btnToggle ? ' ' + btnToggle : '') + (btnRestablecer ? ' ' + btnRestablecer : '') + (btnEliminar ? ' ' + btnEliminar : '');
          if (esProtegido) {
            contenido = esAdminPrincipal
              ? '<span class="badge bg-secondary">Administrador principal</span>'
              : '<span class="badge bg-secondary">Desarrollador</span>';
          }
          return '<div class="btn-group btn-group-sm">' + contenido + '</div>';
        }
      }
    ],
    order: [[0, 'asc']],
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
  });

  function sugerirUsername() {
    var id = $('#usuario_id').val();
    if (id) return;
    var nombre = ($('#usuario_nombre').val() || '').trim();
    var apellido = ($('#usuario_apellido').val() || '').trim();
    var user = '';
    if (nombre.length) user += nombre.charAt(0);
    user += apellido;
    $('#usuario_username').val(user.toLowerCase());
  }

  function limpiarFormulario() {
    $('#form-usuario')[0].reset();
    $('#usuario_id').val('');
    $('#usuario_email').prop('readonly', false).removeClass('is-invalid');
    $('#usuario_email_help').show();
    $('#wrap-usuario-active').addClass('d-none');
    $('#modal-usuario-title').text('Agregar usuario');
  }

  function cargarUsuario(id) {
    $.ajax({
      url: '<?= site_url('usuarios/obtener/') ?>' + id,
      type: 'GET',
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          var d = response.data;
          $('#usuario_id').val(d.id);
          $('#usuario_email').val(d.email).prop('readonly', true);
          $('#usuario_email_help').hide();
          $('#usuario_nombre').val(d.first_name || '');
          $('#usuario_apellido').val(d.last_name || '');
          $('#usuario_username').val(d.username || '');
          $('#usuario_grupo').val(d.grupo);
          $('#usuario_active').prop('checked', d.active === 1);
          var esProtegido = (d.id === 1) || (String(d.email || '').toLowerCase() === 'francurtofrd@gmail.com');
          if (d.id === idUsuarioActual || esProtegido) {
            $('#wrap-usuario-active').addClass('d-none');
          } else {
            $('#wrap-usuario-active').removeClass('d-none');
          }
          $('#modal-usuario-title').text('Editar usuario');
          if (modal) modal.show();
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'No se pudo cargar el usuario', confirmButtonText: 'Aceptar' });
        }
      },
      error: function() {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error al cargar el usuario', confirmButtonText: 'Aceptar' });
      }
    });
  }

  $('#usuario_nombre, #usuario_apellido').on('input', function() { sugerirUsername(); });

  $('#btn-nuevo-usuario').on('click', function() { limpiarFormulario(); });

  $('#btn-guardar-usuario').on('click', function() {
    var id = $('#usuario_id').val();
    var email = $('#usuario_email').val().trim();
    if (!id && !email) {
      $('#usuario_email').addClass('is-invalid');
      $('#usuario_email_error').text('El email es obligatorio').show();
      return;
    }
    var payload = {
      id: id || '',
      email: email,
      first_name: $('#usuario_nombre').val().trim(),
      last_name: $('#usuario_apellido').val().trim(),
      username: $('#usuario_username').val().trim(),
      grupo: $('#usuario_grupo').val(),
      active: $('#usuario_active').is(':checked') ? '1' : '0'
    };
    if (!id) delete payload.active;
    Swal.fire({
      title: 'Procesando',
      text: 'Guardando usuario…',
      allowOutsideClick: false,
      allowEscapeKey: false,
      didOpen: function() { Swal.showLoading(); }
    });
    $.ajax({
      url: '<?= site_url('usuarios/guardar') ?>',
      type: 'POST',
      data: payload,
      dataType: 'json',
      success: function(response) {
        Swal.close();
        if (response.success) {
          Swal.fire({ icon: 'success', title: 'Éxito', text: response.message, confirmButtonText: 'Aceptar', confirmButtonColor: '#0d6efd' });
          table.ajax.reload();
          if (modal) modal.hide();
          limpiarFormulario();
        } else {
          if (response.errors && response.errors.email) {
            $('#usuario_email').addClass('is-invalid');
            $('#usuario_email_error').text(response.errors.email).show();
          }
          Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Error al guardar', confirmButtonText: 'Aceptar' });
        }
      },
      error: function() {
        Swal.close();
        Swal.fire({ icon: 'error', title: 'Error', text: 'Error al guardar', confirmButtonText: 'Aceptar' });
      }
    });
  });

  $(document).on('click', '.editar-usuario', function() { cargarUsuario($(this).data('id')); });

  $(document).on('click', '.restablecer-password-usuario', function() {
    var id = $(this).data('id');
    var email = $(this).data('email') || '';
    Swal.fire({
      title: '¿Restablecer contraseña?',
      text: 'Se pondrá una contraseña por defecto para ' + email + '. Deberá cambiarla al entrar.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, restablecer',
      cancelButtonText: 'Cancelar'
    }).then(function(result) {
      if (result.isConfirmed) {
        $.ajax({
          url: '<?= site_url('usuarios/restablecer-password/') ?>' + id,
          type: 'POST',
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              Swal.fire({ icon: 'success', title: 'Listo', text: response.message, confirmButtonText: 'Aceptar' });
              table.ajax.reload();
            } else {
              Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Error', confirmButtonText: 'Aceptar' });
            }
          },
          error: function() {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Error al restablecer', confirmButtonText: 'Aceptar' });
          }
        });
      }
    });
  });

  $(document).on('click', '.eliminar-usuario', function() {
    var id = $(this).data('id');
    var email = $(this).data('email') || '';
    Swal.fire({
      title: '¿Eliminar usuario?',
      html: 'Se dará de baja a <strong>' + email + '</strong>. No podrá volver a entrar al sistema.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#dc3545'
    }).then(function(result) {
      if (result.isConfirmed) {
        Swal.fire({
          title: 'Procesando',
          text: 'Eliminando usuario y notificando a los administradores…',
          allowOutsideClick: false,
          allowEscapeKey: false,
          didOpen: function() { Swal.showLoading(); }
        });
        $.ajax({
          url: '<?= site_url('usuarios/eliminar/') ?>' + id,
          type: 'POST',
          dataType: 'json',
          success: function(response) {
            Swal.close();
            if (response.success) {
              Swal.fire({ icon: 'success', title: 'Listo', text: response.message, confirmButtonText: 'Aceptar' });
              table.ajax.reload();
            } else {
              Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Error', confirmButtonText: 'Aceptar' });
            }
          },
          error: function() {
            Swal.close();
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo eliminar el usuario.', confirmButtonText: 'Aceptar' });
          }
        });
      }
    });
  });

  $(document).on('click', '.toggle-activo-usuario', function() {
    var id = $(this).data('id');
    var active = $(this).data('active');
    var accion = active === 1 ? 'activar' : 'desactivar';
    Swal.fire({
      title: '¿Confirmar?',
      text: '¿Desea ' + accion + ' este usuario?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí',
      cancelButtonText: 'Cancelar'
    }).then(function(result) {
      if (result.isConfirmed) {
        var mensajeProcesando = active === 1
          ? 'Activando usuario…'
          : 'Desactivando usuario y notificando a los administradores…';
        Swal.fire({
          title: 'Procesando',
          text: mensajeProcesando,
          allowOutsideClick: false,
          allowEscapeKey: false,
          didOpen: function() { Swal.showLoading(); }
        });
        $.ajax({
          url: '<?= site_url('usuarios/guardar') ?>',
          type: 'POST',
          data: { id: id, active: active },
          dataType: 'json',
          success: function(response) {
            Swal.close();
            if (response.success) {
              Swal.fire({ icon: 'success', title: 'Listo', text: response.message, confirmButtonText: 'Aceptar' });
              table.ajax.reload();
            } else {
              Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Error', confirmButtonText: 'Aceptar' });
            }
          },
          error: function() {
            Swal.close();
            Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo completar la solicitud.', confirmButtonText: 'Aceptar' });
          }
        });
      }
    });
  });

  if (modalEl) {
    modalEl.addEventListener('hidden.bs.modal', function() { limpiarFormulario(); });
  }
});
</script>
<?= $this->endSection() ?>
