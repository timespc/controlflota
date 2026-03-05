<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Acceso no autorizado - Montajes Campana</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%); }
    .card { max-width: 480px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="card shadow-sm border-0 p-4">
      <div class="card-body text-center">
        <div class="mb-3">
          <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="text-danger" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
          </svg>
        </div>
        <h1 class="h4 mb-3">Acceso no autorizado</h1>
        <p class="text-muted mb-4">
          Tu solicitud de acceso al sistema no fue aprobada. No podés usar la aplicación con esta cuenta.
        </p>
        <p class="small text-muted mb-4">
          Si creés que es un error, contactate con el administrador del sistema<?php if (! empty($adminEmail)): ?>
          por mail: <a href="mailto:<?= esc($adminEmail) ?>"><?= esc($adminEmail) ?></a><?php else: ?>.
          <?php endif; ?>
        </p>
        <a href="<?= site_url('auth/logout') ?>" class="btn btn-outline-secondary">Cerrar sesión</a>
      </div>
    </div>
  </div>
</body>
</html>
