<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="<?= base_url() ?>/img/logo/logo-itdelfos.ico" type="image/x-icon">
  <title><?= $this->renderSection('titulo') ?></title>
  <?= $this->renderSection('styles') ?>
</head>
<body>
  <?= $this->renderSection('contenido') ?>
</body>
</html>
