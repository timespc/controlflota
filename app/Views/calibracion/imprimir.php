<?= $this->extend('layout/app') ?>

<?= $this->section('titulo') ?>
<?= esc($titulo) ?>
<?= $this->endsection() ?>

<?= $this->section('styles') ?>
<style>
  /* Siempre espacio como si hubiera MF: cada tarjeta ocupa 50% del ancho, alineado a la izquierda (pantalla e impresión) */
  .pagina-tarjetas {
    display: flex;
    gap: 1rem;
    flex-wrap: nowrap;
    max-width: 92%;
    margin: 0;
    justify-content: flex-start;
    align-items: flex-start;
  }
  .tarjeta-print {
    flex: 0 0 50%;
    width: 50%;
    max-width: 50%;
    min-width: 0;
    background: #fff;
    border: 1px solid #333;
    position: relative;
    font-family: Arial, sans-serif;
    font-size: clamp(12px, 1.1rem, 14px);
  }
  @media (min-width: 768px) {
    .tarjeta-print { font-size: 14px; }
  }
  .hoja-calibracion { page-break-after: always; }
  .hoja-calibracion:last-child { page-break-after: auto; }
  @media print {
    .no-print, .sidebar, .page-footer, nav, .btn, .content-wrapper .container-fluid > .row:first-child .card .card-header .btn { display: none !important; }
    body { background: #fff; margin: 0; padding: 0; }
    .print_invoice { width: 100%; text-align: left; margin: 0; padding: 0; }
    /* 48% + 4% + 48% = 100% para que las dos tarjetas quepan sin cortar el MF */
    .pagina-tarjetas { display: flex !important; flex-wrap: nowrap !important; gap: 4% !important; justify-content: flex-start !important; margin: 0 !important; padding: 0 !important; width: 100%; box-sizing: border-box; }
    .tarjeta-print { box-shadow: none; border: 1px solid #333; break-inside: avoid; font-size: 9px !important; flex: 0 0 48% !important; width: 48% !important; max-width: 48% !important; min-width: 0; box-sizing: border-box; }
    .hoja-calibracion { page-break-after: always; }
    .hoja-calibracion:last-child { page-break-after: auto; }
    /* Reducir tamaños dentro de la tarjeta para que no se corte */
    .cert-header { padding: 4px 6px !important; }
    .cert-titulo { font-size: 1em !important; }
    .tarjeta-logo-montajes { height: 60px !important; }
    .tarjeta-logo-inti { height: 48px !important; }
    .cert-body { padding: 0 6px 8px !important; }
    .cert-body table { font-size: 0.8em !important; }
    .cert-body table th, .cert-body table td { padding: 1px 2px !important; }
    .cert-block { padding: 3px 5px !important; margin: 3px 0 !important; }
    .cert-contacto { padding: 2px 6px 4px !important; font-size: 0.8em !important; }
    .cert-pie-calibrador-qr { margin-top: 4px !important; gap: 6px !important; }
    .cert-pie-calibrador .valor { font-size: 0.95em !important; }
    .cert-qr-caja { width: 56px !important; padding: 2px !important; }
    .cert-qr-caja img { width: 50px !important; height: 50px !important; }
    .cert-qr-caja small { font-size: 0.65em !important; }
    .cert-empresas-logos .cert-empresa-logo { height: 28px !important; }
    .watermark-borrador { font-size: 2rem !important; }
  }

  /* Cabecera: logo izq | título centro (centrado verticalmente en la línea) | INTI der */
  .cert-header { display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; }
  .cert-logo-left { flex-shrink: 0; }
  .cert-titulo { flex: 1; text-align: center; font-family: Arial, sans-serif; font-weight: bold; font-size: 1.15em; letter-spacing: 0.02em; padding: 0 8px; }
  .cert-logo-right { flex-shrink: 0; text-align: right; }
  .tarjeta-logo-montajes { height: 90px; filter: none; }
  .tarjeta-logo-inti { height: 72px; filter: none; }
  .cert-contacto { display: flex; justify-content: space-between; align-items: baseline; flex-wrap: wrap; gap: 2px 0; padding: 4px 14px 8px; font-size: 0.9em; border-bottom: 1px solid #333; }
  .cert-contacto .contacto-texto { margin: 0; }
  .cert-contacto .equipo { font-family: Arial, sans-serif; font-weight: bold; margin: 0; }
  .cert-tipo-impresion { font-family: Arial, sans-serif; font-size: 0.8em; color: #333; margin: 0; width: 100%; text-align: right; }
  .cert-tipo-impresion.es-original { font-weight: bold; color: #0a5; }
  .cert-tipo-impresion.es-reimpresion { font-style: italic; color: #666; }

  /* Bloque de datos generales (FECHA/Trans | VENCE/Código) en caja, dos columnas */
  .cert-block { border: 1px solid #333; padding: 8px 10px; margin: 8px 0; }
  .cert-block-dos-col { display: flex; gap: 20px; }
  .cert-block-dos-col .col { flex: 1; }
  .cert-block-dos-col .label-f { font-family: Arial, sans-serif; font-weight: bold; }
  .cert-block-dos-col p { margin: 0 0 3px 0; }
  .cert-block-dos-col .valor { font-family: Arial, sans-serif; font-weight: bold; }

  /* Bloque vehículo en caja */
  .cert-block-vehiculo p { margin: 0 0 3px 0; }
  .cert-linea-patente-holograma { display: flex; justify-content: space-between; align-items: center; gap: 12px; }
  .cert-holograma-caja { flex-shrink: 0; padding-right: 16px; font-family: Arial, sans-serif; font-weight: bold; font-size: 0.9em; }
  .cert-block-vehiculo .label-f { font-family: Arial, sans-serif; font-weight: bold; }
  .cert-block-vehiculo .valor { font-family: Arial, sans-serif; font-weight: bold; }
  .patente-roja { font-family: Arial, sans-serif; color: #c00; font-weight: bold; }
  .cap-nominal-box { font-family: Arial, sans-serif; display: inline-block; border: 1px solid #333; padding: 1px 6px; font-weight: bold; }
  .cert-body { padding: 0 14px 14px; }
  .cert-body .label-f { font-family: Arial, sans-serif; font-weight: bold; }
  .cert-body .valor { font-family: Arial, sans-serif; font-weight: bold; }
  .cert-body p { margin: 0 0 4px 0; line-height: 1.35; }
  .cert-body table { width: 100%; font-size: 0.9em; border-collapse: collapse; margin: 8px 0; font-family: Arial, sans-serif; }
  .cert-body table { table-layout: fixed; }
  .cert-body table col.cert-tabla-izq { width: 10%; }   /* 60% / 6 columnas = 10% cada una */
  .cert-body table col.cert-tabla-prec { width: 13.333%; } /* 40% / 3 columnas ≈ 13.333% cada una */
  .cert-body table th, .cert-body table td { border: 1px solid #333; padding: 3px 5px; text-align: center; }
  .cert-body table th { background: #f0f0f0; font-weight: bold; }
  .cert-body table thead tr:first-child th { border-bottom: none; }
  .cert-body table thead tr:first-child th[colspan="3"] { vertical-align: middle; border-bottom: 1px solid #000; } /* PRECINTOS: centrado vertical y línea abajo */
  .cert-body table thead tr:nth-child(2) th { border-top: none; border-bottom: 1px solid #333; }

  /* Pie: Calibrador + QR a la derecha en la misma zona */
  .cert-pie-calibrador-qr { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-top: 10px; }
  .cert-pie-calibrador { flex: 1; }
  .cert-pie-calibrador .valor { font-family: Arial, sans-serif; font-weight: bold; }
  .cert-qr-caja { flex-shrink: 0; width: 90px; text-align: center; border: 1px solid #333; padding: 4px; }
  .cert-qr-caja img { display: block; width: 80px; height: 80px; margin: 0 auto; }
  .cert-qr-caja small { font-size: 0.7em; color: #666; display: block; margin-top: 2px; }
  .cert-calibrado { font-family: Arial, sans-serif; font-weight: bold; margin: 6px 0 4px 0; }
  .empresas-logos { margin-top: 8px; font-size: 0.8em; color: #666; margin-bottom: 4px; }
  .cert-empresas-logos { display: flex; width: 100%; align-items: center; justify-content: space-between; gap: 4px; margin-top: 6px; }
  .cert-empresas-logos .cert-empresa-logo { flex: 1 1 0; min-width: 0; height: 48px; object-fit: contain; object-position: center; }
  .watermark-borrador { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-35deg); font-size: 3rem; font-weight: bold; color: rgba(220, 53, 69, 0.25); background: transparent; pointer-events: none; z-index: 2; }
  .cert-contenido-tarjeta { position: relative; z-index: 1; }
</style>
<?= $this->endsection() ?>

<?= $this->section('contenido') ?>
<div class="row no-print mb-3">
  <div class="col-12">
    <a href="<?= site_url('calibracion') ?>" class="btn btn-secondary btn-sm me-2">Volver a Calibraciones</a>
    <button type="button" class="btn btn-primary btn-sm" onclick="window.print();">Imprimir tarjeta</button>
  </div>
</div>

<!-- El tema oculta todo en print (body * { visibility: hidden }) y solo muestra .print_invoice -->
<div class="print_invoice">
<div class="pagina-tarjetas">
  <?php
  $esMultiflecha = ! empty($es_multiflecha);
  $multiflecha = $multiflecha ?? [];
  $tieneDosSemis = ! empty($tiene_dos_semis);
  $esMultiflechaSemi2 = ! empty($es_multiflecha_semi2);
  $multiflechaSemi2 = $multiflecha_semi2 ?? [];
  $detalleSemi2 = $cal['detalle_semi2'] ?? [];
  $unidad = $cal['unidad'] ?? [];
  ?>

  <div class="hoja-calibracion">
  <div class="pagina-tarjetas">
  <!-- Tarjeta izquierda (semi 1, siempre) -->
  <div class="tarjeta-print">
    <?php if (! empty($es_borrador)): ?>
    <div class="watermark-borrador" aria-hidden="true">BORRADOR</div>
    <?php endif; ?>

    <div class="cert-contenido-tarjeta">
    <!-- Cabecera: logo izq | CERTIFICADO DE CALIBRACIÓN centro | INTI der -->
    <div class="cert-header">
      <div class="cert-logo-left">
        <?php if (is_file(FCPATH . 'img/tarjeta-calibracion/logo.png')): ?>
        <img src="<?= base_url('img/tarjeta-calibracion/logo.png') ?>" alt="Montajes Campana" class="tarjeta-logo-montajes">
        <?php else: ?>
        <strong>Montajes Campana</strong>
        <?php endif; ?>
      </div>
      <div class="cert-titulo">CERTIFICADO DE CALIBRACIÓN</div>
      <div class="cert-logo-right">
        <?php if (is_file(FCPATH . 'img/tarjeta-calibracion/inti-tarjeta-calibracion.png')): ?>
        <img src="<?= base_url('img/tarjeta-calibracion/inti-tarjeta-calibracion.png') ?>" alt="INTI" class="tarjeta-logo-inti"><br>
        <?php else: ?>
        <strong>Instituto Nacional de Tecnología Industrial INTI</strong><br>
        <?php endif; ?>
        <small>Medidas patrón controladas</small>
      </div>
    </div>
    <div class="cert-contacto">
      <p class="contacto-texto mb-0">Santa Cruz 165 - Campana (2804) - Pcia. Bs. As. - Mail: montajescampanaservicios@gmail.com</p>
      <p class="equipo mb-0">EQUIPO: <?= esc($cal['id_calibracion']) ?></p>
      <?php if (isset($tipo_impresion) && $tipo_impresion !== null): ?>
      <p class="cert-tipo-impresion mb-0 <?= $tipo_impresion === 'original' ? 'es-original' : 'es-reimpresion' ?>"><?= $tipo_impresion === 'original' ? 'IMPRESIÓN ORIGINAL' : 'REIMPRESIÓN' ?></p>
      <?php endif; ?>
    </div>

    <div class="cert-body">
      <!-- Bloque 1: FECHA/Trans/Domicilio | VENCE/Código/Loc (en caja, dos columnas) -->
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

      <!-- Bloque 2: Vehículo (en caja). Patente en rojo, Cap. Nominal en cajita -->
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
        $capNominal = 0;
        if (! empty($cal['detalle'])) { foreach ($cal['detalle'] as $d) { $capNominal += (float)($d['capacidad'] ?? 0); } }
        ?>
        <p><span class="label-f">Cubiertas TRACTOR:</span> Can. Ejes: <span class="valor"><?= $ejesTractor ?></span> &nbsp; Eje-1: <span class="valor"><?= esc($unidad['cubierta_tractor_eje1_medida'] ?? '-') ?></span> &nbsp; Eje-2: <span class="valor"><?= esc($unidad['cubierta_tractor_eje2_medida'] ?? '-') ?></span> &nbsp; Eje-3: <span class="valor"><?= esc($unidad['cubierta_tractor_eje3_medida'] ?? '-') ?></span></p>
        <p><span class="label-f">Cubiertas SEMIRREMOLQUE:</span> Can. Ejes: <span class="valor"><?= $ejesSemi ?></span> &nbsp; Eje-1: <span class="valor"><?= esc($unidad['cubierta_semi_delantero_eje1_medida'] ?? '-') ?></span> &nbsp; Eje-2: <span class="valor"><?= esc($unidad['cubierta_semi_delantero_eje2_medida'] ?? '-') ?></span> &nbsp; Eje-3: <span class="valor"><?= esc($unidad['cubierta_semi_delantero_eje3_medida'] ?? '-') ?></span></p>
        <?php
        $haySemiTrasero = ! empty(trim((string)($unidad['patente_semi_trasero'] ?? '')));
        if ($haySemiTrasero):
          $ejesSemiTrasero = 0;
          if (!empty($unidad['cubierta_semi_trasero_eje1_medida'])) $ejesSemiTrasero++;
          if (!empty($unidad['cubierta_semi_trasero_eje2_medida'])) $ejesSemiTrasero++;
          if (!empty($unidad['cubierta_semi_trasero_eje3_medida'])) $ejesSemiTrasero++;
        ?>
        <p><span class="label-f">Patente 2do semi:</span> <span class="patente-roja"><?= esc($unidad['patente_semi_trasero']) ?></span></p>
        <p><span class="label-f">Cubiertas SEMIRREMOLQUE (2do semi):</span> Can. Ejes: <span class="valor"><?= $ejesSemiTrasero ?></span> &nbsp; Eje-1: <span class="valor"><?= esc($unidad['cubierta_semi_trasero_eje1_medida'] ?? '-') ?></span> &nbsp; Eje-2: <span class="valor"><?= esc($unidad['cubierta_semi_trasero_eje2_medida'] ?? '-') ?></span> &nbsp; Eje-3: <span class="valor"><?= esc($unidad['cubierta_semi_trasero_eje3_medida'] ?? '-') ?></span></p>
        <?php if (isset($unidad['semi_trasero_tara']) && $unidad['semi_trasero_tara'] !== ''): ?>
        <p><span class="label-f">Tara 2do semi:</span> <span class="valor"><?= is_numeric($unidad['semi_trasero_tara']) ? number_format((float)$unidad['semi_trasero_tara'], 0, ',', '.') : esc($unidad['semi_trasero_tara']) ?></span> Kgs.
          <?php if (isset($unidad['semi_trasero_anio_modelo']) && $unidad['semi_trasero_anio_modelo'] !== ''): ?>
          <span class="label-f ms-2">Modelo Año 2do semi:</span> <span class="valor"><?= esc($unidad['semi_trasero_anio_modelo']) ?></span>
          <?php endif; ?></p>
        <?php endif; ?>
        <?php endif; ?>
        <p><span class="label-f">Cota Delantera:</span> <span class="valor"><?= isset($unidad['cota_delantero']) && $unidad['cota_delantero'] !== '' ? (is_numeric($unidad['cota_delantero']) ? (int)$unidad['cota_delantero'] : esc($unidad['cota_delantero'])) : '-' ?></span> mm.
          <span class="label-f ms-2">Cota Trasera:</span> <span class="valor"><?= isset($unidad['cota_trasero']) && $unidad['cota_trasero'] !== '' ? (is_numeric($unidad['cota_trasero']) ? (int)$unidad['cota_trasero'] : esc($unidad['cota_trasero'])) : '-' ?></span> mm.
          <span class="label-f ms-2">Cap. Nominal:</span> <span class="cap-nominal-box"><?= $capNominal > 0 ? number_format($capNominal, 0, ',', '.') : '-' ?></span> Lts.
          <span class="label-f ms-2">Modelo Año:</span> <span class="valor"><?= esc($unidad['semi_delantero_anio_modelo'] ?? '-') ?></span></p>
      </div>

      <p class="label-f">DATOS DE LA CALIBRACIÓN</p>
      <?php if (! empty($cal['detalle'])): ?>
      <table class="table table-bordered table-sm">
        <colgroup>
          <col span="6" class="cert-tabla-izq">
          <col span="3" class="cert-tabla-prec">
        </colgroup>
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
          <?php foreach ($cal['detalle'] as $d): ?>
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

      <!-- Pie: TEMP, REGLA, Calibrador + QR a la derecha (como en el certificado) -->
      <div class="cert-pie-calibrador-qr">
        <div class="cert-pie-calibrador">
          <p><span class="label-f">TEMP. DEL AGUA:</span> <span class="valor"><?= isset($cal['temp_agua']) ? esc($cal['temp_agua']) : '-' ?></span> °C
            <span class="label-f ms-2">REGLA:</span> <span class="valor"><?= esc($cal['n_regla'] ?? '-') ?></span></p>
          <p><span class="label-f">Calibrador:</span> <span class="valor"><?= esc($cal['calibrador_nombre'] ?? '-') ?></span></p>
        </div>
        <?php if (! empty($url_publica) && empty($es_borrador)): ?>
        <div class="cert-qr-caja">
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=<?= urlencode($url_publica) ?>" alt="QR verificación" width="80" height="80">
          <small>Escanear para verificar</small>
        </div>
        <?php endif; ?>
      </div>

      <p class="cert-calibrado">CALIBRADO CON VALVULAS <?= ! empty($cal['valvulas']) && $cal['valvulas'] === '1' ? 'ABIERTAS' : 'CERRADAS' ?></p>
      <p class="empresas-logos">Calibración aceptada por Empresas:</p>
      <div class="cert-empresas-logos">
        <?php
        $empresasLogos = ['axion-energy', 'shell', 'ypf', 'puma-energy', 'cammesa', 'refinor', 'enarsa'];
        $exts = ['.jpg', '.jpeg', '.png'];
        foreach ($empresasLogos as $emp):
          $imgPath = null;
          foreach ($exts as $ext) {
            $p = 'img/tarjeta-calibracion/empresas/' . $emp . $ext;
            if (is_file(FCPATH . $p)) { $imgPath = $p; break; }
          }
          if ($imgPath === null) { continue; }
        ?>
        <img src="<?= base_url($imgPath) ?>" alt="<?= esc(ucfirst(str_replace('-', ' ', $emp))) ?>" class="cert-empresa-logo">
        <?php endforeach; ?>
      </div>
      <?php if (! empty($mostrar_observaciones) && trim((string)($cal['observaciones'] ?? '')) !== ''): ?>
      <p class="small mt-1"><span class="label-f">Observaciones:</span> <?= esc($cal['observaciones']) ?></p>
      <?php endif; ?>
    </div>
    </div>
  </div>

  <?php if ($esMultiflecha && ! empty($multiflecha)): ?>
  <!-- Tarjeta derecha: Multiflecha (misma estructura que semi 1) -->
  <div class="tarjeta-print">
    <?php if (! empty($es_borrador)): ?>
    <div class="watermark-borrador" aria-hidden="true">BORRADOR</div>
    <?php endif; ?>
    <div class="cert-contenido-tarjeta">
    <div class="cert-header">
      <div class="cert-logo-left">
        <?php if (is_file(FCPATH . 'img/tarjeta-calibracion/logo.png')): ?>
        <img src="<?= base_url('img/tarjeta-calibracion/logo.png') ?>" alt="Montajes Campana" class="tarjeta-logo-montajes">
        <?php else: ?>
        <strong>Montajes Campana</strong>
        <?php endif; ?>
      </div>
      <div class="cert-titulo">CERTIFICADO DE CALIBRACIÓN</div>
      <div class="cert-logo-right">
        <?php if (is_file(FCPATH . 'img/tarjeta-calibracion/inti-tarjeta-calibracion.png')): ?>
        <img src="<?= base_url('img/tarjeta-calibracion/inti-tarjeta-calibracion.png') ?>" alt="INTI" class="tarjeta-logo-inti"><br>
        <?php else: ?>
        <strong>Instituto Nacional de Tecnología Industrial INTI</strong><br>
        <?php endif; ?>
        <small>Medidas patrón controladas</small>
      </div>
    </div>
    <div class="cert-contacto">
      <p class="contacto-texto mb-0">Santa Cruz 165 - Campana (2804) - Pcia. Bs. As. - Mail: montajescampanaservicios@gmail.com</p>
      <p class="equipo mb-0">EQUIPO: <?= esc($cal['id_calibracion']) ?> (Multiflecha)</p>
      <?php if (isset($tipo_impresion) && $tipo_impresion !== null): ?>
      <p class="cert-tipo-impresion mb-0 <?= $tipo_impresion === 'original' ? 'es-original' : 'es-reimpresion' ?>"><?= $tipo_impresion === 'original' ? 'IMPRESIÓN ORIGINAL' : 'REIMPRESIÓN' ?></p>
      <?php endif; ?>
    </div>
    <div class="cert-body">
      <p class="label-f">DATOS DE LA CALIBRACIÓN</p>
      <table class="table table-bordered table-sm">
        <colgroup>
          <col span="6" class="cert-tabla-izq">
          <col span="3" class="cert-tabla-prec">
        </colgroup>
        <thead>
          <tr>
            <th>N°Cis</th>
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
          <?php foreach ($multiflecha as $mf): ?>
          <tr>
            <td><?= (int)($mf['numero_linea'] ?? 0) ?></td>
            <td><?= isset($mf['capacidad']) ? number_format((float)$mf['capacidad'], 0, ',', '.') : '-' ?></td>
            <td><?= isset($mf['enrase']) ? number_format((float)$mf['enrase'], 0, ',', '') : '-' ?></td>
            <td><?= isset($mf['vacio_calc']) ? number_format((float)$mf['vacio_calc'], 0, ',', '') : '-' ?></td>
            <td><?= isset($mf['vacio_lts']) ? (int)$mf['vacio_lts'] : '-' ?></td>
            <td><?= isset($mf['referen']) ? number_format((float)$mf['referen'], 0, ',', '') : '-' ?></td>
            <td><?= esc($mf['precinto_campana'] ?? '-') ?></td>
            <td><?= esc($mf['precinto_soporte'] ?? '-') ?></td>
            <td><?= esc($mf['precinto_hombre'] ?? '-') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <p class="mt-2 mb-0"><span class="label-f">Patente:</span> <span class="patente-roja"><?= esc($cal['patente']) ?></span></p>
      <p class="mb-0"><span class="label-f">Firma Calibrador:</span> <span class="valor"><?= esc($cal['calibrador_nombre'] ?? '-') ?></span></p>
    </div>
    </div>
  </div>
  <?php endif; ?>
  </div>
  </div>

  <?php if ($tieneDosSemis && ! empty($detalleSemi2)): ?>
  <!-- Hoja 2: Tarjeta 2do semi -->
  <div class="hoja-calibracion">
  <div class="pagina-tarjetas">
  <div class="tarjeta-print">
    <?php if (! empty($es_borrador)): ?>
    <div class="watermark-borrador" aria-hidden="true">BORRADOR</div>
    <?php endif; ?>
    <div class="cert-contenido-tarjeta">
    <div class="cert-header">
      <div class="cert-logo-left">
        <?php if (is_file(FCPATH . 'img/tarjeta-calibracion/logo.png')): ?>
        <img src="<?= base_url('img/tarjeta-calibracion/logo.png') ?>" alt="Montajes Campana" class="tarjeta-logo-montajes">
        <?php else: ?>
        <strong>Montajes Campana</strong>
        <?php endif; ?>
      </div>
      <div class="cert-titulo">CERTIFICADO DE CALIBRACIÓN (2do semi)</div>
      <div class="cert-logo-right">
        <?php if (is_file(FCPATH . 'img/tarjeta-calibracion/inti-tarjeta-calibracion.png')): ?>
        <img src="<?= base_url('img/tarjeta-calibracion/inti-tarjeta-calibracion.png') ?>" alt="INTI" class="tarjeta-logo-inti"><br>
        <?php else: ?>
        <strong>Instituto Nacional de Tecnología Industrial INTI</strong><br>
        <?php endif; ?>
        <small>Medidas patrón controladas</small>
      </div>
    </div>
    <div class="cert-contacto">
      <p class="contacto-texto mb-0">Santa Cruz 165 - Campana (2804) - Pcia. Bs. As. - Mail: montajescampanaservicios@gmail.com</p>
      <p class="equipo mb-0">EQUIPO: <?= esc($cal['id_calibracion']) ?> (2do semi)</p>
      <?php if (isset($tipo_impresion) && $tipo_impresion !== null): ?>
      <p class="cert-tipo-impresion mb-0 <?= $tipo_impresion === 'original' ? 'es-original' : 'es-reimpresion' ?>"><?= $tipo_impresion === 'original' ? 'IMPRESIÓN ORIGINAL' : 'REIMPRESIÓN' ?></p>
      <?php endif; ?>
    </div>
    <div class="cert-body">
      <div class="cert-block">
        <div class="cert-block-dos-col">
          <div class="col">
            <p><span class="label-f">FECHA:</span> <span class="valor"><?= $cal['fecha_calib'] ? date('d-M-y', strtotime($cal['fecha_calib'])) : '-' ?></span></p>
            <p><span class="label-f">Trans:</span> <span class="valor"><?= esc($cal['transportista'] ?? '-') ?></span></p>
          </div>
          <div class="col">
            <p><span class="label-f">VENCE:</span> <span class="valor"><?= $cal['vto_calib'] ? date('d-M-y', strtotime($cal['vto_calib'])) : '-' ?></span></p>
          </div>
        </div>
      </div>
      <?php
      $capNominalSemi2 = 0;
      foreach ($detalleSemi2 as $d) { $capNominalSemi2 += (float)($d['capacidad'] ?? 0); }
      ?>
      <div class="cert-block cert-block-vehiculo">
        <p class="cert-linea-patente-holograma"><span><span class="label-f">Patente 2do semi:</span> <span class="patente-roja"><?= esc($unidad['patente_semi_trasero'] ?? '-') ?></span></span><span class="cert-holograma-caja">Holograma</span></p>
        <?php
        $ejesSemiTrasero = 0;
        if (!empty($unidad['cubierta_semi_trasero_eje1_medida'])) $ejesSemiTrasero++;
        if (!empty($unidad['cubierta_semi_trasero_eje2_medida'])) $ejesSemiTrasero++;
        if (!empty($unidad['cubierta_semi_trasero_eje3_medida'])) $ejesSemiTrasero++;
        ?>
        <p><span class="label-f">Cubiertas SEMIRREMOLQUE (2do semi):</span> Can. Ejes: <span class="valor"><?= $ejesSemiTrasero ?></span> &nbsp; Eje-1: <span class="valor"><?= esc($unidad['cubierta_semi_trasero_eje1_medida'] ?? '-') ?></span> &nbsp; Eje-2: <span class="valor"><?= esc($unidad['cubierta_semi_trasero_eje2_medida'] ?? '-') ?></span> &nbsp; Eje-3: <span class="valor"><?= esc($unidad['cubierta_semi_trasero_eje3_medida'] ?? '-') ?></span></p>
        <?php if (isset($unidad['semi_trasero_tara']) && $unidad['semi_trasero_tara'] !== ''): ?>
        <p><span class="label-f">Tara 2do semi:</span> <span class="valor"><?= is_numeric($unidad['semi_trasero_tara']) ? number_format((float)$unidad['semi_trasero_tara'], 0, ',', '.') : esc($unidad['semi_trasero_tara']) ?></span> Kgs.
          <?php if (isset($unidad['semi_trasero_anio_modelo']) && $unidad['semi_trasero_anio_modelo'] !== ''): ?>
          <span class="label-f ms-2">Modelo Año 2do semi:</span> <span class="valor"><?= esc($unidad['semi_trasero_anio_modelo']) ?></span>
          <?php endif; ?></p>
        <?php endif; ?>
        <p><span class="label-f">Cap. Nominal 2do semi:</span> <span class="cap-nominal-box"><?= $capNominalSemi2 > 0 ? number_format($capNominalSemi2, 0, ',', '.') : '-' ?></span> Lts.</p>
      </div>
      <p class="label-f">DATOS DE LA CALIBRACIÓN (2do semi)</p>
      <table class="table table-bordered table-sm">
        <colgroup>
          <col span="6" class="cert-tabla-izq">
          <col span="3" class="cert-tabla-prec">
        </colgroup>
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
          <?php foreach ($detalleSemi2 as $d): ?>
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
      <?php if ($esMultiflechaSemi2 && ! empty($multiflechaSemi2)): ?>
      <p class="label-f mt-2">DATOS DE LA CALIBRACIÓN (Multiflecha 2do semi)</p>
      <table class="table table-bordered table-sm">
        <colgroup>
          <col span="6" class="cert-tabla-izq">
          <col span="3" class="cert-tabla-prec">
        </colgroup>
        <thead>
          <tr>
            <th>N°Cis</th>
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
          <?php foreach ($multiflechaSemi2 as $mf): ?>
          <tr>
            <td><?= (int)($mf['numero_linea'] ?? 0) ?></td>
            <td><?= isset($mf['capacidad']) ? number_format((float)$mf['capacidad'], 0, ',', '.') : '-' ?></td>
            <td><?= isset($mf['enrase']) ? number_format((float)$mf['enrase'], 0, ',', '') : '-' ?></td>
            <td><?= isset($mf['vacio_calc']) ? number_format((float)$mf['vacio_calc'], 0, ',', '') : '-' ?></td>
            <td><?= isset($mf['vacio_lts']) ? (int)$mf['vacio_lts'] : '-' ?></td>
            <td><?= isset($mf['referen']) ? number_format((float)$mf['referen'], 0, ',', '') : '-' ?></td>
            <td><?= esc($mf['precinto_campana'] ?? '-') ?></td>
            <td><?= esc($mf['precinto_soporte'] ?? '-') ?></td>
            <td><?= esc($mf['precinto_hombre'] ?? '-') ?></td>
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
        <?php if (! empty($url_publica) && empty($es_borrador)): ?>
        <div class="cert-qr-caja">
          <img src="https://api.qrserver.com/v1/create-qr-code/?size=80x80&data=<?= urlencode($url_publica) ?>" alt="QR verificación" width="80" height="80">
          <small>Escanear para verificar</small>
        </div>
        <?php endif; ?>
      </div>
      <p class="cert-calibrado">CALIBRADO CON VALVULAS <?= ! empty($cal['valvulas']) && $cal['valvulas'] === '1' ? 'ABIERTAS' : 'CERRADAS' ?></p>
      <p class="empresas-logos">Calibración aceptada por Empresas:</p>
      <div class="cert-empresas-logos">
        <?php foreach ($empresasLogos as $emp):
          $imgPath = null;
          foreach ($exts as $ext) {
            $p = 'img/tarjeta-calibracion/empresas/' . $emp . $ext;
            if (is_file(FCPATH . $p)) { $imgPath = $p; break; }
          }
          if ($imgPath === null) { continue; }
        ?>
        <img src="<?= base_url($imgPath) ?>" alt="<?= esc(ucfirst(str_replace('-', ' ', $emp))) ?>" class="cert-empresa-logo">
        <?php endforeach; ?>
      </div>
    </div>
    </div>
  </div>
  </div>
  </div>
  <?php endif; ?>
</div>
</div>
<?= $this->endsection() ?>
