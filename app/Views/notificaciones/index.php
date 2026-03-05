<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
Notificaciones
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <div class="card border border-secondary">
      <div class="card-header text-white bg-secondary pt-3 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">Notificaciones</h5>
        <div class="d-flex gap-2">
          <?php if (! empty($notificaciones) && array_filter($notificaciones, fn($n) => empty($n['leida']))): ?>
            <button type="button" class="btn btn-outline-light btn-sm" id="marcar-todas-leidas">Marcar todas como leídas</button>
          <?php endif; ?>
          <a href="<?= site_url('notificaciones/config') ?>" class="btn btn-outline-light btn-sm">Configurar notificaciones</a>
        </div>
      </div>
      <div class="card-body">
        <?php if (session('success')): ?>
          <div class="alert alert-success alert-dismissible fade show">
            <?= esc(session('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>
        <?php if (session('error')): ?>
          <div class="alert alert-danger alert-dismissible fade show">
            <?= esc(session('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <?php if (empty($notificaciones)): ?>
          <p class="text-muted mb-0">No hay notificaciones.</p>
        <?php else: ?>
          <div class="list-group list-group-flush">
            <?php foreach ($notificaciones as $n): ?>
              <div class="list-group-item d-flex flex-wrap align-items-start gap-2 <?= empty($n['leida']) ? 'bg-light' : '' ?>">
                <div class="flex-grow-1">
                  <div class="d-flex justify-content-between align-items-start">
                    <h6 class="mb-1"><?= esc($n['titulo']) ?></h6>
                    <small class="text-muted"><?= $n['created_at'] ? date('d/m/Y H:i', strtotime($n['created_at'])) : '' ?></small>
                  </div>
                  <?php if (($n['tipo'] ?? '') === \App\Models\NotificacionModel::TIPO_CALIBRACION_POR_VENCER): ?>
                    <p class="mb-2 small"><?= esc($n['mensaje'] ?? '') ?></p>
                    <p class="mb-2 small"><a href="<?= site_url() ?>">Ir al Dashboard</a> para ver el detalle.</p>
                  <?php elseif (! empty($n['mensaje'])): ?>
                    <p class="mb-2 small"><?= esc($n['mensaje']) ?></p>
                  <?php endif; ?>
                  <?php if (($n['tipo'] ?? '') === \App\Models\NotificacionModel::TIPO_NUEVO_USUARIO): ?>
                    <span class="badge bg-info">Usuario dado de alta</span>
                  <?php endif; ?>
                  <?php if (($n['tipo'] ?? '') === \App\Models\NotificacionModel::TIPO_USUARIO_DESACTIVADO): ?>
                    <span class="badge bg-warning text-dark">Usuario desactivado</span>
                  <?php endif; ?>
                  <?php if (($n['tipo'] ?? '') === \App\Models\NotificacionModel::TIPO_CALIBRACION_POR_VENCER): ?>
                    <span class="badge bg-info">Calibraciones por vencer</span>
                  <?php endif; ?>
                </div>
                <?php if (empty($n['leida'])): ?>
                  <button type="button" class="btn btn-sm btn-outline-secondary marcar-leida" data-id="<?= (int) $n['id_notificacion'] ?>">Marcar leída</button>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?= $this->endsection() ?>

<?= $this->section('javascript') ?>
<input type="hidden" id="csrf_token" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
<script>
$(function() {
  $('.marcar-leida').on('click', function() {
    var id = $(this).data('id');
    var btn = $(this);
    var tokenName = $('#csrf_token').attr('name');
    var tokenVal = $('#csrf_token').val();
    var data = { id_notificacion: id };
    data[tokenName] = tokenVal;
    $.post('<?= site_url('notificaciones/marcar-leida') ?>', data, function() {
      btn.closest('.list-group-item').removeClass('bg-light').find('.marcar-leida').remove();
    });
  });
  $('#marcar-todas-leidas').on('click', function() {
    var btn = $(this);
    var tokenName = $('#csrf_token').attr('name');
    var tokenVal = $('#csrf_token').val();
    var data = {};
    data[tokenName] = tokenVal;
    btn.prop('disabled', true);
    $.post('<?= site_url('notificaciones/marcar-todas-leidas') ?>', data, function(res) {
      if (res && res.success && typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'success', title: 'Listo', text: 'Todas las notificaciones fueron marcadas como leídas.', confirmButtonText: 'Aceptar', confirmButtonColor: '#0d6efd' }).then(function() {
          window.location.reload();
        });
      } else {
        window.location.reload();
      }
    }).fail(function() {
      btn.prop('disabled', false);
      if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo completar la acción.', confirmButtonText: 'Aceptar' });
      }
    });
  });
});
</script>
<?= $this->endsection() ?>
