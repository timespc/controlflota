<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pendiente de aprobación - Montajes Campana</title>
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
          <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="text-warning" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
            <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
          </svg>
        </div>
        <h1 class="h4 mb-3">Cuenta pendiente de aprobación</h1>
        <p class="text-muted mb-4">
          Tu solicitud de acceso fue recibida. Un administrador del sistema debe aprobarte antes de que puedas usar la aplicación.
        </p>
        <p class="small text-muted mb-4">
          Cuando tu cuenta sea aprobada, podrás iniciar sesión con normalidad. Si tenés dudas, contactá al administrador.
        </p>
        <a href="<?= site_url('auth/logout') ?>" class="btn btn-outline-secondary">Cerrar sesión</a>
      </div>
    </div>
  </div>
</body>
</html>
