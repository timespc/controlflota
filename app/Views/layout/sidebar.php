<?php
$uri = trim(uri_string(), '/');
if (strpos($uri, 'index.php/') === 0) {
  $uri = substr($uri, strlen('index.php/'));
}
$basePath = trim((string) parse_url(base_url(), PHP_URL_PATH), '/');
if ($basePath !== '' && $uri === $basePath) {
  $uri = '';
} elseif ($basePath !== '' && strpos($uri, $basePath . '/') === 0) {
  $uri = substr($uri, strlen($basePath) + 1);
}
$isActive = function ($segment) use ($uri) {
  if ($segment === '') {
    return $uri === '' || $uri === 'dashboard';
  }
  return $uri === $segment || strpos($uri, $segment . '/') === 0;
};
$parametrosSegments = ['calibradores', 'banderas', 'cubiertas', 'marcas', 'marcas-sensor', 'nacion', 'reglas', 'inspectores', 'items-censo', 'tipos-cargamentos'];
$isParametrosActive = false;
foreach ($parametrosSegments as $seg) {
  if ($isActive($seg)) {
    $isParametrosActive = true;
    break;
  }
}
?>
<div class="sidebar p-2 py-md-3 @@cardClass">
  <div class="container-fluid">
    <!-- sidebar: title-->
    <div class="d-flex align-items-center justify-content-center mb-4 mt-1 p-2">
      <a href="<?= site_url() ?>"><img class="logo" src="<?= base_url() . '/img/logo/transener_transba1.svg' ?>" alt="logo montajes campaña"></a>
    </div>
    <!-- sidebar: menu list -->
    <div class="main-menu flex-grow-1">
      <ul class="menu-list">
        <li>
          <a class="m-link<?= $isActive('') ? ' active' : '' ?>" href="<?= site_url() ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="currentColor" viewBox="0 0 16 16">
              <path fill-rule="evenodd" d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293l6-6zm5-.793V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z" />
              <path class="fill-iconos" fill-rule="evenodd" d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z" />
            </svg>
            <span class="ms-2">Inicio</span>
          </a>
        </li>
        <li>
          <a class="m-link<?= $isActive('transportistas') ? ' active' : '' ?>" href="<?= site_url('transportistas') ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="currentColor" viewBox="0 0 16 16">
              <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5v7a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 0 10.5v-7zM1.5 3a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.5-.5h-9z"/>
              <path d="M2 4.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z"/>
            </svg>
            <span class="ms-2">Transportistas</span>
          </a>
        </li>
        <li>
          <a class="m-link<?= $isActive('equipos') ? ' active' : '' ?>" href="<?= site_url('equipos') ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="currentColor" viewBox="0 0 16 16">
              <path d="M0 2.5A.5.5 0 0 1 .5 2H2a.5.5 0 0 1 .488.608L1.39 4H.5a.5.5 0 0 1-.5-.5zM3.352 4H4a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1h1.352zM7 4v8a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1zm-2 0v8H5V4H5zM9 4v8a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1zm-2 0v8H7V4H7zm3 0v8a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1zm-2 0v8h1V4h-1zm3 0v8a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1zm-2 0v8h1V4h-1z"/>
            </svg>
            <span class="ms-2">Equipos</span>
          </a>
        </li>
        <li>
          <a class="m-link<?= $isActive('choferes') ? ' active' : '' ?>" href="<?= site_url('choferes') ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="currentColor" viewBox="0 0 16 16">
              <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
            </svg>
            <span class="ms-2">Choferes</span>
          </a>
        </li>
        <li>
          <a class="m-link<?= $isActive('calibracion') ? ' active' : '' ?>" href="<?= site_url('calibracion') ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="currentColor" viewBox="0 0 16 16">
              <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
              <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
            </svg>
            <span class="ms-2">Calibración</span>
          </a>
        </li>
        <li>
          <a class="m-link<?= $isParametrosActive ? ' active' : '' ?>" href="#" data-bs-toggle="collapse" data-bs-target="#submenu-parametros" aria-expanded="<?= $isParametrosActive ? 'true' : 'false' ?>" aria-controls="submenu-parametros">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="currentColor" viewBox="0 0 16 16">
              <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.988 1.988l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.988 1.988l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.988-1.988l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.988-1.988l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
            </svg>
            <span class="ms-2">Parametros</span>
            <svg class="submenu-chevron" xmlns="http://www.w3.org/2000/svg" width="14" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z"/></svg>
          </a>
          <ul class="sub-menu collapse<?= $isParametrosActive ? ' show' : '' ?>" id="submenu-parametros">
            <li><a class="ms-link<?= $isActive('calibradores') ? ' active' : '' ?>" href="<?= site_url('calibradores') ?>">Calibradores</a></li>
            <li><a class="ms-link<?= $isActive('banderas') ? ' active' : '' ?>" href="<?= site_url('banderas') ?>">Banderas</a></li>
            <li><a class="ms-link<?= $isActive('cubiertas') ? ' active' : '' ?>" href="<?= site_url('cubiertas') ?>">Cubiertas</a></li>
            <li><a class="ms-link<?= $isActive('marcas') ? ' active' : '' ?>" href="<?= site_url('marcas') ?>">Marcas</a></li>
            <li><a class="ms-link<?= $isActive('marcas-sensor') ? ' active' : '' ?>" href="<?= site_url('marcas-sensor') ?>">Marcas Sensor</a></li>
            <li><a class="ms-link<?= $isActive('nacion') ? ' active' : '' ?>" href="<?= site_url('nacion') ?>">Nacion</a></li>
            <li><a class="ms-link<?= $isActive('reglas') ? ' active' : '' ?>" href="<?= site_url('reglas') ?>">Regla</a></li>
            <li><a class="ms-link<?= $isActive('inspectores') ? ' active' : '' ?>" href="<?= site_url('inspectores') ?>">Inspectores</a></li>
            <li><a class="ms-link<?= $isActive('items-censo') ? ' active' : '' ?>" href="<?= site_url('items-censo') ?>">Items Censo</a></li>
            <li><a class="ms-link<?= $isActive('tipos-cargamentos') ? ' active' : '' ?>" href="<?= site_url('tipos-cargamentos') ?>">Tipos Cargamentos</a></li>
          </ul>
        </li>
        <li>
          <a class="m-link<?= $isActive('reportes') ? ' active' : '' ?>" href="<?= site_url('reportes') ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="currentColor" viewBox="0 0 16 16">
              <path d="M14 14V4.5L9.5 9H11l3 3V14h-3v-2l-1.5-1.5v2.5H14z"/>
              <path d="M1.5 2h9a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H6v7.5a.5.5 0 0 1-.5.5h-4a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5H5V2.5a.5.5 0 0 1 .5-.5h-4z"/>
            </svg>
            <span class="ms-2">Reportes</span>
          </a>
        </li>
        <?php if (function_exists('es_admin') && es_admin()): ?>
        <li>
          <a class="m-link<?= $isActive('usuarios') ? ' active' : '' ?>" href="<?= site_url('usuarios') ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="currentColor" viewBox="0 0 16 16">
              <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
              <path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1h4.216z"/>
            </svg>
            <span class="ms-2">Usuarios</span>
          </a>
        </li>
        <li>
          <a class="m-link<?= $isActive('notificaciones') ? ' active' : '' ?>" href="<?= site_url('notificaciones') ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="currentColor" viewBox="0 0 16 16">
              <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628.134 2.197.459 3.742.16.767.376 1.566.663 2.258h10.244c.287-.692.502-1.49.663-2.258C12.866 8.197 13 6.628 13 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917z"/>
            </svg>
            <span class="ms-2">Notificaciones</span>
          </a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
    <!-- sidebar: footer link -->
    <ul class="menu-list nav navbar-nav flex-row text-center menu-footer-link">
      <li class="nav-item flex-fill p-2">
        <a class="d-inline-block w-100" href="<?= site_url('auth/logout') ?>" title="Cerrar sesión">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="currentColor" viewBox="0 0 16 16">
            <path d="M7.5 1v7h1V1h-1z" />
            <path class="fill-iconos" d="M3 8.812a4.999 4.999 0 0 1 2.578-4.375l-.485-.874A6 6 0 1 0 11 3.616l-.501.865A5 5 0 1 1 3 8.812z" />
          </svg>
          Salir
        </a>
      </li>
    </ul>
  </div>
</div>



