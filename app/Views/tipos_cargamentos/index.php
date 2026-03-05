<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
Tipos Cargamentos - Montajes Campana
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
  .table-responsive { overflow-x: auto; -webkit-overflow-scrolling: touch; }
  .aviso-solo-consulta { font-weight: bold; }
</style>
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<div class="row g-3 mt-1">
  <div class="col-12">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2">
        <li class="breadcrumb-item"><a href="<?= site_url() ?>">Inicio</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('calibradores') ?>">Parametros</a></li>
        <li class="breadcrumb-item active">Tipos Cargamentos</li>
      </ol>
    </nav>
    <div class="card border border-secondary">
      <div class="card-header text-white bg-secondary pt-3 pb-2">
        <h5 class="mb-0">Tipos Cargamentos</h5>
      </div>
      <div class="card-body">
        <p class="text-danger aviso-solo-consulta mb-3">PERMITIDO SÓLO CONSULTA E IMPRESIÓN DE TABLA</p>
        <div class="table-responsive">
          <table id="tipos-cargamentos-datatable" class="table table-striped table-bordered table-hover" style="width:100%">
            <thead>
              <tr>
                <th>ID</th>
                <th>TIPO</th>
                <th>Abreviado</th>
                <th>UltActualiz</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
$(document).ready(function() {
  var table = $('#tipos-cargamentos-datatable').DataTable({
    language: { url: '<?= base_url('assets/js/datatable/esp.json') ?>' },
    processing: true,
    serverSide: false,
    responsive: true,
    pageLength: 10,
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
    ajax: {
      url: '<?= site_url('tipos-cargamentos/listar') ?>',
      type: 'POST'
    },
    columns: [
      { data: 'id' },
      { data: 'tipo' },
      { data: 'tipo_carga_abreviado' },
      { data: 'ult_actualiz', defaultContent: '—' }
    ],
    order: [[0, 'asc']],
    dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
  });

});
</script>
<?= $this->endSection() ?>
