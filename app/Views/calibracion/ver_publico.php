<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($titulo) ?> - Montajes Campana</title>
  <style>
    * { box-sizing: border-box; }
    body { font-family: Arial, sans-serif; font-size: 14px; background: #e8e8e8; margin: 0; padding: 1rem; color: #000; }
    .contenedor-tarjetas { max-width: 480px; margin: 0 auto; }
    .tarjeta-solo-lectura {
      background: #fff;
      border: 1px solid #333;
      margin-bottom: 1.5rem;
      position: relative;
    }
    .cert-header { display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; }
    .cert-logo-left { flex-shrink: 0; }
    .cert-titulo { flex: 1; text-align: center; font-family: Arial, sans-serif; font-weight: bold; font-size: 1.15em; letter-spacing: 0.02em; padding: 0 8px; }
    .cert-logo-right { flex-shrink: 0; text-align: right; font-size: 0.85em; }
    .tarjeta-logo-montajes { height: 70px; }
    .tarjeta-logo-inti { height: 56px; }
    .cert-contacto { padding: 6px 14px 8px; font-size: 0.9em; border-bottom: 1px solid #333; }
    .cert-contacto .contacto-texto { margin: 0 0 4px 0; }
    .cert-contacto .solo-lectura-aviso { font-weight: bold; color: #666; font-size: 0.9em; margin: 0; text-align: right; }
    .cert-block { border: 1px solid #333; padding: 8px 10px; margin: 8px 0; }
    .cert-block-dos-col { display: flex; gap: 20px; }
    .cert-block-dos-col .col { flex: 1; }
    .cert-block-dos-col .label-f { font-weight: bold; }
    .cert-block-dos-col p { margin: 0 0 3px 0; }
    .cert-block-dos-col .valor { font-weight: bold; }
    .cert-block-vehiculo p { margin: 0 0 3px 0; }
    .cert-linea-patente-holograma { display: flex; justify-content: space-between; align-items: center; gap: 12px; }
    .cert-holograma-caja { flex-shrink: 0; font-weight: bold; font-size: 0.9em; }
    .cert-block-vehiculo .label-f { font-weight: bold; }
    .cert-block-vehiculo .valor { font-weight: bold; }
    .patente-roja { color: #c00; font-weight: bold; }
    .cap-nominal-box { display: inline-block; border: 1px solid #333; padding: 1px 6px; font-weight: bold; }
    .cert-body { padding: 0 14px 14px; }
    .cert-body .label-f { font-weight: bold; }
    .cert-body .valor { font-weight: bold; }
    .cert-body p { margin: 0 0 4px 0; line-height: 1.35; }
    .cert-body table { width: 100%; font-size: 0.85em; border-collapse: collapse; margin: 8px 0; }
    .cert-body table th, .cert-body table td { border: 1px solid #333; padding: 3px 5px; text-align: center; }
    .cert-body table th { background: #f0f0f0; font-weight: bold; }
    .cert-body table thead tr:first-child th[colspan="3"] { vertical-align: middle; }
    .cert-pie-calibrador-qr { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-top: 10px; }
    .cert-pie-calibrador { flex: 1; }
    .cert-pie-calibrador .valor { font-weight: bold; }
    .cert-sin-qr-caja {
      flex-shrink: 0; width: 100px; text-align: center; border: 1px solid #333; padding: 6px 4px;
      font-size: 0.7em; color: #666; line-height: 1.25;
    }
    .cert-calibrado { font-weight: bold; margin: 6px 0 4px 0; }
    .empresas-logos { margin-top: 8px; font-size: 0.8em; color: #666; margin-bottom: 4px; }
    .cert-empresas-logos { display: flex; width: 100%; align-items: center; justify-content: space-between; gap: 4px; margin-top: 6px; }
    .cert-empresas-logos .cert-empresa-logo { flex: 1 1 0; min-width: 0; height: 40px; object-fit: contain; object-position: center; }
    .mb-0 { margin-bottom: 0; }
    .ms-2 { margin-left: 0.5rem; }
    .watermark-solo-lectura {
      position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-35deg);
      font-size: 2.5rem; font-weight: bold; color: rgba(0, 0, 0, 0.12); pointer-events: none; z-index: 0;
    }
    .cert-contenido-tarjeta { position: relative; z-index: 1; }
    .barra-impresion { padding: 0.5rem 1rem; background: #f0f0f0; border-bottom: 1px solid #333; display: flex; justify-content: flex-end; gap: 0.5rem; }
    .barra-impresion .btn-imprimir { padding: 0.35rem 0.75rem; font-size: 0.9rem; cursor: pointer; background: #0d6efd; color: #fff; border: none; border-radius: 0.25rem; }
    .barra-impresion .btn-imprimir:hover { background: #0b5ed7; }
    @media print {
      .no-print { display: none !important; }
      .watermark-solo-lectura { color: rgba(0, 0, 0, 0.15); font-size: 3rem !important; }
    }
  </style>
</head>
<body>
<div class="no-print barra-impresion">
  <button type="button" class="btn-imprimir" onclick="window.print();">Imprimir</button>
</div>
<?php
$unidad = $cal['unidad'] ?? [];
$detalleSemi2 = $cal['detalle_semi2'] ?? [];
$tieneDosSemis = ! empty($detalleSemi2);
$empresasLogos = ['axion-energy', 'shell', 'ypf', 'puma-energy', 'cammesa', 'refinor', 'enarsa'];
$exts = ['.jpg', '.jpeg', '.png'];
?>
<div class="contenedor-tarjetas">
  <?php
  $hojas = [['titulo' => 'CERTIFICADO DE CALIBRACIÓN', 'detalle' => $cal['detalle'] ?? [], 'es_semi2' => false]];
  if ($tieneDosSemis) {
    $hojas[] = ['titulo' => 'CERTIFICADO DE CALIBRACIÓN (2do semi)', 'detalle' => $detalleSemi2, 'es_semi2' => true];
  }
  foreach ($hojas as $hoja):
    $detalle = $hoja['detalle'];
    $esSemi2 = $hoja['es_semi2'];
    $capNominal = 0;
    if (! empty($detalle)) {
      foreach ($detalle as $d) { $capNominal += (float)($d['capacidad'] ?? 0); }
    }
    if ($esSemi2 && $tieneDosSemis) {
      $capNominalSemi2 = 0;
      foreach ($detalleSemi2 as $d) { $capNominalSemi2 += (float)($d['capacidad'] ?? 0); }
      $capNominal = $capNominalSemi2;
    }
  ?>
  <div class="tarjeta-solo-lectura">
    <div class="watermark-solo-lectura" aria-hidden="true">SOLO LECTURA</div>
    <div class="cert-contenido-tarjeta">
    <div class="cert-header">
      <div class="cert-logo-left">
        <?php if (! empty(FCPATH) && is_file(FCPATH . 'img/tarjeta-calibracion/logo.png')): ?>
        <img src="<?= base_url('img/tarjeta-calibracion/logo.png') ?>" alt="Montajes Campana" class="tarjeta-logo-montajes">
        <?php else: ?>
        <strong>Montajes Campana</strong>
        <?php endif; ?>
      </div>
      <div class="cert-titulo"><?= esc($hoja['titulo']) ?></div>
      <div class="cert-logo-right">
        <?php if (! empty(FCPATH) && is_file(FCPATH . 'img/tarjeta-calibracion/inti-tarjeta-calibracion.png')): ?>
        <img src="<?= base_url('img/tarjeta-calibracion/inti-tarjeta-calibracion.png') ?>" alt="INTI" class="tarjeta-logo-inti"><br>
        <?php else: ?>
        <strong>INTI</strong><br>
        <?php endif; ?>
        <small>Medidas patrón controladas</small>
      </div>
    </div>
    <div class="cert-contacto">
      <p class="contacto-texto mb-0">Santa Cruz 165 - Campana (2804) - Pcia. Bs. As. - Mail: montajescampanaservicios@gmail.com</p>
      <p class="solo-lectura-aviso">SOLO LECTURA — Sin código QR de validación</p>
    </div>

    <div class="cert-body">
      <div class="cert-block">
        <div class="cert-block-dos-col">
          <div class="col">
            <p><span class="label-f">FECHA:</span> <span class="valor"><?= $cal['fecha_calib'] ? date('d-M-y', strtotime($cal['fecha_calib'])) : '-' ?></span></p>
            <p><span class="label-f">Trans:</span> <span class="valor"><?= esc($cal['transportista'] ?? '-') ?></span></p>
            <p><span class="label-f">Domicilio:</span> <span class="valor"><?= esc($cal['transportista_domicilio'] ?? '-') ?></span></p>
            <p><span class="label-f">Provincia:</span> <span class="valor"><?= esc($cal['transportista_provincia'] ?? '-') ?></span></p>
          </div>
          <div class="col">
            <p><span class="label-f">VENCE:</span> <span class="valor"><?= $cal['vto_calib'] ? date('d-M-y', strtotime($cal['vto_calib'])) : '-' ?></span></p>
            <p><span class="label-f">Código:</span> <span class="valor"><?= esc($cal['transportista_codigo'] ?? '-') ?></span></p>
            <p><span class="label-f">Loc:</span> <span class="valor"><?= esc($cal['transportista_localidad'] ?? '-') ?></span></p>
          </div>
        </div>
      </div>

      <?php if (! $esSemi2): ?>
      <div class="cert-block cert-block-vehiculo">
        <p class="cert-linea-patente-holograma"><span><span class="label-f">Patente:</span> <span class="valor">SEMIRREMOLQUE</span> <span class="patente-roja"><?= esc($cal['patente']) ?></span></span><span class="cert-holograma-caja">Holograma</span></p>
        <p><span class="label-f">Marca:</span> <span class="valor"><?= esc($unidad['marca_nombre'] ?? '-') ?></span>
          <span class="label-f ms-2">Tara:</span> <span class="valor"><?= isset($unidad['semi_delantero_tara']) ? number_format((float)$unidad['semi_delantero_tara'], 0, ',', '.') : '-' ?></span> Kgs.
          <span class="label-f ms-2">Patente Tractor:</span> <span class="valor"><?= esc($unidad['patente_tractor'] ?? '-') ?></span></p>
        <?php
        $ejesTractor = 0;
        if (!empty($unidad['cubierta_tractor_eje1_medida'])) $ejesTractor++;
        if (!empty($unidad['cubierta_tractor_eje2_medida'])) $ejesTractor++;
        if (!empty($unidad['cubierta_tractor_eje3_medida'])) $ejesTractor++;
        $ejesSemi = 0;
        if (!empty($unidad['cubierta_semi_delantero_eje1_medida'])) $ejesSemi++;
        if (!empty($unidad['cubierta_semi_delantero_eje2_medida'])) $ejesSemi++;
        if (!empty($unidad['cubierta_semi_delantero_eje3_medida'])) $ejesSemi++;
        ?>
        <p><span class="label-f">Cubiertas TRACTOR:</span> Can. Ejes: <span class="valor"><?= $ejesTractor ?></span> &nbsp; Eje-1: <span class="valor"><?= esc($unidad['cubierta_tractor_eje1_medida'] ?? '-') ?></span> &nbsp; Eje-2: <span class="valor"><?= esc($unidad['cubierta_tractor_eje2_medida'] ?? '-') ?></span> &nbsp; Eje-3: <span class="valor"><?= esc($unidad['cubierta_tractor_eje3_medida'] ?? '-') ?></span></p>
        <p><span class="label-f">Cubiertas SEMIRREMOLQUE:</span> Can. Ejes: <span class="valor"><?= $ejesSemi ?></span> &nbsp; Eje-1: <span class="valor"><?= esc($unidad['cubierta_semi_delantero_eje1_medida'] ?? '-') ?></span> &nbsp; Eje-2: <span class="valor"><?= esc($unidad['cubierta_semi_delantero_eje2_medida'] ?? '-') ?></span> &nbsp; Eje-3: <span class="valor"><?= esc($unidad['cubierta_semi_delantero_eje3_medida'] ?? '-') ?></span></p>
        <?php if (! empty(trim((string)($unidad['patente_semi_trasero'] ?? '')))): ?>
        <p><span class="label-f">Patente 2do semi:</span> <span class="patente-roja"><?= esc($unidad['patente_semi_trasero']) ?></span></p>
        <?php endif; ?>
        <p><span class="label-f">Cota Delantera:</span> <span class="valor"><?= isset($unidad['cota_delantero']) && $unidad['cota_delantero'] !== '' ? (is_numeric($unidad['cota_delantero']) ? (int)$unidad['cota_delantero'] : esc($unidad['cota_delantero'])) : '-' ?></span> mm.
          <span class="label-f ms-2">Cota Trasera:</span> <span class="valor"><?= isset($unidad['cota_trasero']) && $unidad['cota_trasero'] !== '' ? (is_numeric($unidad['cota_trasero']) ? (int)$unidad['cota_trasero'] : esc($unidad['cota_trasero'])) : '-' ?></span> mm.
          <span class="label-f ms-2">Cap. Nominal:</span> <span class="cap-nominal-box"><?= $capNominal > 0 ? number_format($capNominal, 0, ',', '.') : '-' ?></span> Lts.
          <span class="label-f ms-2">Modelo Año:</span> <span class="valor"><?= esc($unidad['semi_delantero_anio_modelo'] ?? '-') ?></span></p>
      </div>
      <?php else: ?>
      <div class="cert-block cert-block-vehiculo">
        <p><span class="label-f">Patente 2do semi:</span> <span class="patente-roja"><?= esc($unidad['patente_semi_trasero'] ?? '-') ?></span></p>
        <p><span class="label-f">Cap. Nominal 2do semi:</span> <span class="cap-nominal-box"><?= $capNominal > 0 ? number_format($capNominal, 0, ',', '.') : '-' ?></span> Lts.</p>
      </div>
      <?php endif; ?>

      <p class="label-f"><?= $esSemi2 ? 'DATOS DE LA CALIBRACIÓN (2do semi)' : 'DATOS DE LA CALIBRACIÓN' ?></p>
      <?php if (! empty($detalle)): ?>
      <table>
        <thead>
          <tr>
            <th>Cis N°</th>
            <th>Cap (Lts)</th>
            <th>Enrase (mm)</th>
            <th>Vacio (mm)</th>
            <th>Vacio (Lts)</th>
            <th>P.Ref (mm)</th>
            <th colspan="3">PRECINTOS</th>
          </tr>
          <tr>
            <th></th><th></th><th></th><th></th><th></th><th></th>
            <th>Camp</th><th>Sopor</th><th>P.Hom</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($detalle as $d): ?>
          <tr>
            <td><?= (int)($d['numero_linea'] ?? 0) ?></td>
            <td><?= isset($d['capacidad']) ? number_format((float)$d['capacidad'], 0, ',', '.') : '-' ?></td>
            <td><?= isset($d['enrase']) ? number_format((float)$d['enrase'], 0, ',', '') : '-' ?></td>
            <td><?= isset($d['vacio_calc']) ? number_format((float)$d['vacio_calc'], 0, ',', '') : '-' ?></td>
            <td><?= isset($d['vacio_lts']) ? (int)$d['vacio_lts'] : '-' ?></td>
            <td><?= isset($d['referen']) ? number_format((float)$d['referen'], 0, ',', '') : '-' ?></td>
            <td><?= esc($d['precinto_campana'] ?? '-') ?></td>
            <td><?= esc($d['precinto_soporte'] ?? '-') ?></td>
            <td><?= esc($d['precinto_hombre'] ?? '-') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>

      <div class="cert-pie-calibrador-qr">
        <div class="cert-pie-calibrador">
          <p><span class="label-f">TEMP. DEL AGUA:</span> <span class="valor"><?= isset($cal['temp_agua']) ? esc($cal['temp_agua']) : '-' ?></span> °C
            <span class="label-f ms-2">REGLA:</span> <span class="valor"><?= esc($cal['n_regla'] ?? '-') ?></span></p>
          <p><span class="label-f">Calibrador:</span> <span class="valor"><?= esc($cal['calibrador_nombre'] ?? '-') ?></span></p>
        </div>
        <div class="cert-sin-qr-caja">Documento de consulta. No incluye código QR de validación.</div>
      </div>

      <p class="cert-calibrado">CALIBRADO CON VALVULAS <?= ! empty($cal['valvulas']) && $cal['valvulas'] === '1' ? 'ABIERTAS' : 'CERRADAS' ?></p>
      <p class="empresas-logos">Calibración aceptada por Empresas:</p>
      <div class="cert-empresas-logos">
        <?php foreach ($empresasLogos as $emp):
          $imgPath = null;
          foreach ($exts as $ext) {
            $p = 'img/tarjeta-calibracion/empresas/' . $emp . $ext;
            if (! empty(FCPATH) && is_file(FCPATH . $p)) { $imgPath = $p; break; }
          }
          if ($imgPath === null) { continue; }
        ?>
        <img src="<?= base_url($imgPath) ?>" alt="<?= esc(ucfirst(str_replace('-', ' ', $emp))) ?>" class="cert-empresa-logo">
        <?php endforeach; ?>
      </div>
    </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>
</body>
</html>
