<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= lang('Auth.login_heading') ?> - Montajes Campana</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --login-bg: #f5f3f0;
      --login-card-bg: #fff;
      --login-text: #2d2d2d;
      --login-text-muted: #5f6368;
      --login-divider: #e8eaed;
      --login-shadow: 0 1px 3px rgba(0,0,0,.08);
      --login-shadow-hover: 0 2px 8px rgba(0,0,0,.12);
    }
    body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--login-bg); margin: 0; }
    .login-wrap { width: 100%; max-width: 420px; padding: 1rem; }
    .login-card { background: var(--login-card-bg); border-radius: 12px; box-shadow: var(--login-shadow); padding: 2.5rem 2rem; text-align: center; }
    .login-logo { max-width: 180px; max-height: 80px; object-fit: contain; margin-bottom: 1.5rem; }
    .login-title { font-size: 1.75rem; font-weight: 700; color: var(--login-text); margin-bottom: 0.5rem; }
    .login-subtitle { font-size: 0.95rem; color: var(--login-text-muted); margin-bottom: 1rem; }
    .login-divider { height: 1px; background: var(--login-divider); margin: 1.25rem 0; }
    .btn-google-wrap { display: flex; justify-content: center; margin-top: 1rem; }
    .alert-login { border-radius: 8px; text-align: left; }
  </style>
  <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>
  <div class="login-wrap">
    <div class="login-card">
      <img src="<?= base_url('img/logo/logo.png') ?>" alt="Montajes Campana" class="login-logo">
      <h1 class="login-title"><?= lang('Auth.login_heading') ?></h1>
      <p class="login-subtitle"><?= lang('Auth.login_subheading') ?></p>

      <?php if (! empty($message)): ?>
        <div class="alert alert-info alert-login alert-dismissible fade show" role="alert">
          <?= strip_tags($message) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
      <?php endif; ?>

      <?= form_open(site_url('auth/login'), ['class' => 'text-start']) ?>
        <?= csrf_field() ?>
        <div class="mb-3">
          <label for="identity" class="form-label"><?= lang('Auth.login_identity_label') ?></label>
          <input type="text" name="identity" id="identity" class="form-control" value="<?= esc(set_value('identity')) ?>" autocomplete="username" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label"><?= lang('Auth.login_password_label') ?></label>
          <input type="password" name="password" id="password" class="form-control" autocomplete="current-password" required>
        </div>
        <div class="mb-3 form-check">
          <input type="checkbox" name="remember" id="remember" class="form-check-input" value="1">
          <label class="form-check-label" for="remember"><?= lang('Auth.login_remember_label') ?></label>
        </div>
        <button type="submit" class="btn btn-primary w-100"><?= lang('Auth.login_submit_btn') ?></button>
      <?= form_close() ?>

      <div class="login-divider"></div>

      <p class="small text-muted mb-2"><?= lang('Auth.login_with_google') ?></p>
      <div class="btn-google-wrap">
        <div id="g_id_onload" data-client_id="<?= esc(env('GOOGLE_CLIENT_ID')) ?>" data-context="signin" data-login_uri="<?= esc(site_url('auth/google-sign-in')) ?>" data-auto_prompt="false"></div>
        <div class="g_id_signin" data-type="standard" data-size="large" data-theme="outline" data-text="signin_with" data-shape="rectangular" data-logo_alignment="left"></div>
      </div>

      <p class="small text-muted mt-3 mb-0">Solo usuarios ya registrados pueden acceder con Google.</p>
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
