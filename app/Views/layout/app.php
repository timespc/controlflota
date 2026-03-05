<!DOCTYPE html>
<html class="no-js" lang="es">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=Edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Sistema de gestión de montajes y calibraciones">
  <meta name="keyword" content="montajes, calibraciones, equipos, unidades">
  <meta name="csrf-token-name" content="<?= csrf_token() ?>">
  <meta name="csrf-token-value" content="<?= csrf_hash() ?>">
  <title><?= $this->renderSection('titulo') ?></title>
  <?= $this->include('layout/style') ?>
  <?= $this->renderSection('styles') ?>
</head>

<body class="layout-1" data-luno="theme-itdelfos" id="cargador-spinner">
  <?= $this->include('layout/sidebar') ?>
  <div class="sidebar-backdrop" id="sidebar-backdrop" aria-hidden="true"></div>
  <div class="wrapper">
    <?= $this->include('layout/header') ?>
    <!-- start: body area -->
    <div class="content-wrapper">
      <div class="page-body px-xl-4 px-sm-2 px-0 py-lg-2 py-1 mt-0 mt-lg-3">
        <div class="container-fluid">
          <?= $this->renderSection('contenido') ?>
        </div>
      </div>
      <!-- start: page footer -->
      <footer class="page-footer px-xl-4 px-sm-2 px-0 py-3">
        <!-- content footer -->
      </footer>
    </div>
  </div>
  <?= $this->include('layout/scripts') ?>
  <?= $this->renderSection('javascript') ?>
</body>

</html>



