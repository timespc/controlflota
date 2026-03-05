<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title ?? 'Cambiar contraseña') ?> - Montajes Campana</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --login-bg: #f5f3f0;
      --login-card-bg: #fff;
      --login-text: #2d2d2d;
      --login-text-muted: #5f6368;
      --login-divider: #e8eaed;
      --login-shadow: 0 1px 3px rgba(0,0,0,.08);
    }
    body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--login-bg); margin: 0; }
    .login-wrap { width: 100%; max-width: 420px; padding: 1rem; }
    .login-card { background: var(--login-card-bg); border-radius: 12px; box-shadow: var(--login-shadow); padding: 2.5rem 2rem; text-align: center; }
    .login-logo { max-width: 180px; max-height: 80px; object-fit: contain; margin-bottom: 1.5rem; }
    .login-title { font-size: 1.75rem; font-weight: 700; color: var(--login-text); margin-bottom: 0.5rem; }
    .login-subtitle { font-size: 0.95rem; color: var(--login-text-muted); margin-bottom: 1rem; }
    .alert-login { border-radius: 8px; text-align: left; }
  </style>
</head>
<body>
  <div class="login-wrap">
    <div class="login-card">
      <img src="<?= base_url('img/logo/logo.png') ?>" alt="Montajes Campana" class="login-logo">
      <h1 class="login-title">Cambiar contraseña</h1>
      <p class="login-subtitle">Es obligatorio cambiar la contraseña antes de continuar.</p>

      <?php if (! empty($message)): ?>
        <div class="alert alert-info alert-login alert-dismissible fade show" role="alert">
          <?= $message ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
      <?php endif; ?>

      <?= form_open(site_url('auth/cambiar-contrasena'), ['class' => 'text-start']) ?>
        <?= csrf_field() ?>
        <div class="mb-3 small text-muted border rounded px-3 py-2 bg-light">
          <strong>Requisitos de la contraseña:</strong>
          <ul class="mb-0 ps-3">
            <li>Mínimo 10 caracteres</li>
            <li>Al menos una letra mayúscula</li>
            <li>Al menos una letra minúscula</li>
            <li>Al menos un número</li>
          </ul>
        </div>
        <div class="mb-3">
          <label for="password_nueva" class="form-label">Nueva contraseña</label>
          <input type="password" name="password_nueva" id="password_nueva" class="form-control" autocomplete="new-password" required minlength="10">
        </div>
        <div class="mb-3">
          <label for="password_confirmar" class="form-label">Confirmar nueva contraseña</label>
          <input type="password" name="password_confirmar" id="password_confirmar" class="form-control" autocomplete="new-password" required minlength="10">
        </div>
        <button type="submit" class="btn btn-primary w-100">Guardar nueva contraseña</button>
      <?= form_close() ?>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.querySelector('form')?.addEventListener('submit', function() {
      var btn = this.querySelector('button[type=submit]');
      if (btn) btn.disabled = true;
    });
  </script>
</body>
</html>
