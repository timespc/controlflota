<header class="page-header sticky-top px-xl-4 px-sm-2 px-0 py-lg-2 py-1">
  <div class="container-fluid">
    <nav class="navbar">
      <!-- start: toggle btn -->
      <div class="d-flex">
        <button type="button" class="btn btn-link d-none d-xl-block sidebar-mini-btn p-0 text-primary btn-resize">
          <span class="hamburger-icon">
            <span class="line"></span>
            <span class="line"></span>
            <span class="line"></span>
          </span>
        </button>
        <button type="button" class="btn btn-link d-block d-xl-none menu-toggle p-0 text-primary btn-resize">
          <span class="hamburger-icon">
            <span class="line"></span>
            <span class="line"></span>
            <span class="line"></span>
          </span>
        </button>
      </div>
      <!-- start: search area -->
      <div class="header-left flex-grow-1 d-none d-md-block">
        <div class="main-search px-3 flex-fill">
          <!-- Search area -->
        </div>
      </div>
      <!-- start: link -->
      <ul class="header-right justify-content-end d-flex align-items-center mb-0">
        <?php if (function_exists('es_admin') && es_admin()): ?>
        <li class="nav-item me-2">
          <a href="<?= site_url('notificaciones') ?>" class="nav-link position-relative" title="Notificaciones">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-bell" viewBox="0 0 16 16"><path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628.134 2.197.459 3.742.16.767.376 1.566.663 2.258h10.244c.287-.692.502-1.49.663-2.258C12.866 8.197 13 6.628 13 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1l.22-.22V9c0-2.797.96-5.285 2.36-7.227C4.736 2.294 6.244 1.5 8 1.5s3.264.794 4.42 2.273C13.84 3.715 14.8 6.203 14.8 9v2.78l.22.22z"/></svg>
            <span id="notif-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; display: none;">0</span>
          </a>
        </li>
        <?php endif; ?>
        <?php if (function_exists('usuario_actual') && usuario_actual()): $u = usuario_actual(); ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarUsuario" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <?php if (! empty($u['avatar_url'])): ?>
              <img src="<?= esc($u['avatar_url']) ?>" alt="" class="rounded-circle me-2" width="32" height="32">
            <?php else: ?>
              <span class="rounded-circle bg-secondary text-white d-inline-flex align-items-center justify-content-center me-2" style="width:32px;height:32px;font-size:14px"><?= esc(mb_substr($u['nombre'] ?? 'U', 0, 1)) ?></span>
            <?php endif; ?>
            <span class="d-none d-md-inline"><?= esc($u['nombre'] ?? $u['email']) ?></span>
            <small class="text-muted ms-1 d-none d-lg-inline">(<?= esc($u['rol_nombre'] ?? '') ?>)</small>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUsuario">
            <li><span class="dropdown-item-text small text-muted"><?= esc($u['email']) ?></span></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="<?= site_url('auth/logout') ?>">Cerrar sesión</a></li>
          </ul>
        </li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>




