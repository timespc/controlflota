<?= $this->extend('layout/print') ?>

<?= $this->section('titulo') ?>
Tipos Cargamentos - Montajes Campana
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
  body { font-family: Arial, sans-serif; font-size: 12px; padding: 16px; }
  h1 { font-size: 18px; margin-bottom: 12px; }
  table { width: 100%; border-collapse: collapse; margin-top: 8px; }
  th, td { border: 1px solid #333; padding: 6px 8px; text-align: left; }
  th { background: #eee; font-weight: bold; }
  .aviso { color: #c00; font-weight: bold; margin-bottom: 8px; }
  @media print { body { padding: 0; } }
</style>
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<h1>Tipos Cargamentos</h1>
<p class="aviso">PERMITIDO SÓLO CONSULTA E IMPRESIÓN DE TABLA</p>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>TIPO</th>
      <th>Abreviado</th>
      <th>UltActualiz</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($lista as $row): ?>
    <tr>
      <td><?= (int) $row['id'] ?></td>
      <td><?= esc($row['tipo']) ?></td>
      <td><?= esc($row['tipo_carga_abreviado'] ?? '') ?></td>
      <td><?= esc($row['ult_actualiz'] ?? '') ?></td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<p class="mt-3 small text-muted">Impreso el <?= date('d/m/Y H:i:s') ?></p>
<script>window.onload = function() { window.print(); }</script>
<?= $this->endSection() ?>
