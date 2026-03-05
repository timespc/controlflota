<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
Choferes - Montajes Campana
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= site_url() ?>">Inicio</a></li>
        <li class="breadcrumb-item active">Choferes</li>
      </ol>
    </nav>
    <div class="card border border-secondary">
      <div class="card-header text-white bg-secondary pt-3 pb-2">
        <h5 class="mb-0">Choferes</h5>
      </div>
      <div class="card-body text-center py-5">
        <p class="text-muted mb-0">Este módulo está en desarrollo.</p>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
