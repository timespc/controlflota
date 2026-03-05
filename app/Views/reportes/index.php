<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
Reportes - Montajes Campana
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
  .reporte-card {
    border-radius: 0.5rem;
    transition: box-shadow 0.2s, transform 0.15s;
    text-decoration: none;
    color: inherit;
    display: block;
    border: 1px solid rgba(0,0,0,.08);
  }
  .reporte-card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.1);
    transform: translateY(-2px);
    color: inherit;
  }
  .reporte-card .card-body { padding: 1.5rem; }
  .reporte-card .reporte-icon { width: 48px; height: 48px; opacity: 0.85; }
</style>
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <h4 class="mb-3">Reportes</h4>
    <p class="text-muted mb-4">Seleccioná el tipo de reporte que querés generar. Podés filtrar y exportar a CSV.</p>

    <div class="row g-3">
      <div class="col-12 col-md-6 col-lg-4">
        <a href="<?= site_url('reportes/calibraciones') ?>" class="card reporte-card h-100">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="reporte-icon text-primary">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995z"/>
              </svg>
            </div>
            <div>
              <h5 class="mb-1">Calibraciones</h5>
              <p class="mb-0 small text-muted">Por período, patente y calibrador. Exportar CSV.</p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-12 col-md-6 col-lg-4">
        <a href="<?= site_url('reportes/vencimientos') ?>" class="card reporte-card h-100">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="reporte-icon text-warning">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/>
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/>
              </svg>
            </div>
            <div>
              <h5 class="mb-1">Vencimientos</h5>
              <p class="mb-0 small text-muted">Próximos X días. Exportar CSV.</p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-12 col-md-6 col-lg-4">
        <a href="<?= site_url('reportes/flota') ?>" class="card reporte-card h-100">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="reporte-icon text-success">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
              </svg>
            </div>
            <div>
              <h5 class="mb-1">Flota (Equipos)</h5>
              <p class="mb-0 small text-muted">Listado de equipos por transportista. Exportar CSV.</p>
            </div>
          </div>
        </a>
      </div>
      <div class="col-12 col-md-6 col-lg-4">
        <a href="<?= site_url('reportes/transportistas') ?>" class="card reporte-card h-100">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="reporte-icon text-info">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" viewBox="0 0 16 16">
                <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5v7a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 0 10.5v-7zM1.5 3a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5v-7a.5.5 0 0 0-.5-.5h-9z"/>
                <path d="M2 4.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5z"/>
              </svg>
            </div>
            <div>
              <h5 class="mb-1">Transportistas</h5>
              <p class="mb-0 small text-muted">Listado con cant. equipos. Exportar CSV.</p>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>
</div>
<?= $this->endsection() ?>
