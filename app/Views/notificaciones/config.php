<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
Configurar notificaciones
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <div class="card border border-secondary">
      <div class="card-header text-white bg-secondary pt-3 pb-2 d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Configuración de notificaciones</h5>
        <a href="<?= site_url('notificaciones') ?>" class="btn btn-outline-light btn-sm">Volver a notificaciones</a>
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

        <p class="text-muted small mb-4">Primero elegí de qué eventos querés ser avisado; después cómo querés recibir esas notificaciones (campana, navegador, email). El resto es configuración según lo que elijas.</p>

        <form method="post" action="<?= site_url('notificaciones/guardar-config') ?>">
          <?= csrf_field() ?>

          <div class="row mb-4">
            <div class="col-12">
              <h6 class="border-bottom pb-2 mb-3">Tipos de notificación</h6>
              <p class="small text-muted mb-2">Elegí de qué eventos querés ser avisado. Luego indicás cómo querés recibirlas (campana, navegador, email).</p>
              <?php foreach ($tiposDisponibles as $tipo => $label): ?>
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="tipo_<?= esc($tipo) ?>" id="tipo_<?= esc($tipo) ?>" value="1" <?= ! empty($tiposActivos[$tipo]) ? 'checked' : '' ?>>
                <label class="form-check-label" for="tipo_<?= esc($tipo) ?>"><?= esc($label) ?></label>
              </div>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="row mb-4">
            <div class="col-md-6">
              <h6 class="border-bottom pb-2 mb-3">Cómo recibir</h6>
              <p class="small text-muted mb-2">Indicá por dónde querés recibir las notificaciones de los tipos que marcaste arriba.</p>
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="push_activo" id="push_activo" value="1" <?= ! isset($config['push_activo']) || ! empty($config['push_activo']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="push_activo">Recibir notificaciones generales (campana)</label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="push_browser_activo" id="push_browser_activo" value="1" <?= ! isset($config['push_browser_activo']) || ! empty($config['push_browser_activo']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="push_browser_activo">Recibir push notificación (navegador)</label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="email_activo" id="email_activo" value="1" <?= ! empty($config['email_activo']) ? 'checked' : '' ?>>
                <label class="form-check-label" for="email_activo">Recibir mail</label>
              </div>
              <div class="mb-2 mt-2">
                <label class="form-label small">Dirección de email (opcional)</label>
                <input type="email" name="email_destino" class="form-control form-control-sm" value="<?= esc($config['email_destino'] ?? '') ?>" placeholder="<?= esc($emailUsuario) ?>">
                <small class="text-muted">Si está vacío se usa tu email de la cuenta (<?= esc($emailUsuario) ?>)</small>
              </div>
            </div>
          </div>

          <div class="row mb-4">
            <div class="col-12">
              <h6 class="border-bottom pb-2 mb-3">Recordatorio</h6>
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" name="recordatorio_activo" id="recordatorio_activo" value="1" <?= (int) ($config['recordatorio_minutos'] ?? 0) > 0 ? 'checked' : '' ?>>
                <label class="form-check-label" for="recordatorio_activo">Recibir recordatorios por email cuando no lea notificaciones</label>
              </div>
              <div class="d-flex align-items-center gap-2 ms-4" id="recordatorio-minutos-wrap">
                <label class="form-label small mb-0">Cada</label>
                <input type="number" name="recordatorio_minutos" class="form-control form-control-sm" style="width: 100px;" min="1" value="<?= max(1, (int) ($config['recordatorio_minutos'] ?? 0)) ?>">
                <span class="small">minutos</span>
              </div>
            </div>
          </div>

          <div class="row mb-4">
            <div class="col-12">
              <h6 class="border-bottom pb-2 mb-3">Calibraciones por vencer</h6>
              <p class="small text-muted mb-2">Si marcaste "Calibraciones por vencer" arriba, indicá con cuántos días de anticipación querés que te avisemos. El cron diario crea una notificación con las calibraciones que vencen en ese plazo.</p>
              <div class="d-flex align-items-center gap-2">
                <label class="form-label small mb-0">Avisar cuando falten</label>
                <input type="number" name="dias_aviso_vencimiento" class="form-control form-control-sm" style="width: 80px;" min="1" value="<?= max(1, (int) ($config['dias_aviso_vencimiento'] ?? 30)) ?>">
                <span class="small">días para el vencimiento</span>
              </div>
            </div>
          </div>

          <div class="row mb-4">
            <div class="col-12">
              <h6 class="border-bottom pb-2 mb-3">Dashboard / KPI</h6>
              <p class="small text-muted mb-2">En el dashboard, las calibraciones vencidas que llevan más de X meses vencidas dejan de contarse en el KPI "Equipos vencidos" y en la lista de vencimientos críticos. 0 = sin límite (todas las vencidas cuentan).</p>
              <div class="d-flex align-items-center gap-2">
                <label class="form-label small mb-0">Dejar de contar como vencida después de</label>
                <input type="number" name="meses_vencida_max_kpi" class="form-control form-control-sm" style="width: 80px;" min="0" value="<?= (int) ($meses_vencida_max_kpi ?? 24) ?>">
                <span class="small">meses desde el vencimiento (0 = sin límite)</span>
              </div>
            </div>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Guardar configuración</button>
            <a href="<?= site_url('notificaciones') ?>" class="btn btn-outline-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var cb = document.getElementById('recordatorio_activo');
  var wrap = document.getElementById('recordatorio-minutos-wrap');
  var input = document.querySelector('input[name="recordatorio_minutos"]');
  function toggle() {
    var disabled = !cb.checked;
    if (input) input.disabled = disabled;
    if (wrap) wrap.style.opacity = disabled ? '0.5' : '1';
  }
  if (cb) {
    cb.addEventListener('change', toggle);
    toggle();
  }
});
</script>
<?= $this->endsection() ?>
