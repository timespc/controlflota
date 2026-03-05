<?php

/**
 * Busca en el dump del sistema viejo grupos (IdTta + PatenteTractor + mismo día)
 * que tengan 2 o más unidades (distintas Patente = semis). Solo se consideran
 * bitren cuando los 2 registros están cargados el mismo día (UltActualiz).
 *
 * Uso: php app/scripts/buscar_bitrenes_unidades_viejo.php
 */

$path = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'montajes-campana-db-sistema-viejo.sql';
if (! is_file($path)) {
    echo "No se encuentra el dump: {$path}\n";
    exit(1);
}

$sql = file_get_contents($path);

// Solo buscar dentro de bloques INSERT INTO `unidades` ... ;
if (! preg_match_all('/INSERT INTO `unidades`[^;]+;/s', $sql, $blocks)) {
    echo "No se encontró INSERT INTO unidades.\n";
    exit(1);
}

$all = [];
$sinFecha = []; // mismo IdTta + PatenteTractor, sin filtrar por día (para comparar)
foreach ($blocks[0] as $block) {
    // Una fila por coincidencia: patente, tractor (o NULL), idTta, fecha (mismo orden que en el dump)
    preg_match_all("/\(\s*'([^']*)'\s*,\s*(?:'([^']*)'|NULL)\s*,\s*(\d+)\s*,[^)]*,'(\d{4}-\d{2}-\d{2}) \d{2}:\d{2}:\d{2}'\)/", $block, $rows, PREG_SET_ORDER);
    foreach ($rows as $r) {
        $patente = trim($r[1]);
        $tractor = isset($r[2]) ? trim($r[2]) : '';
        $idTta   = (int) $r[3];
        $fecha   = $r[4] ?? '';
        if ($tractor === '' || $patente === '') {
            continue;
        }
        $tractorNorm = strtoupper(str_replace(' ', '', $tractor));
        $key = $idTta . '|' . $tractorNorm . '|' . $fecha;
        $keySinFecha = $idTta . '|' . $tractorNorm;
        if (! isset($all[$key])) {
            $all[$key] = [
                'id_tta'   => $idTta,
                'tractor'  => $tractor,
                'fecha'    => $fecha,
                'patentes' => [],
            ];
        }
        if (! in_array($patente, $all[$key]['patentes'], true)) {
            $all[$key]['patentes'][] = $patente;
        }
        if (! isset($sinFecha[$keySinFecha])) {
            $sinFecha[$keySinFecha] = ['patentes' => []];
        }
        if (! in_array($patente, $sinFecha[$keySinFecha]['patentes'], true)) {
            $sinFecha[$keySinFecha]['patentes'][] = $patente;
        }
    }
}

$bitren = array_filter($all, static function ($v) {
    return count($v['patentes']) >= 2;
});
$bitrenSinFecha = array_filter($sinFecha, static function ($v) {
    return count($v['patentes']) >= 2;
});
// Claves (IdTta|PatenteTractor) que sí tienen 2+ patentes el mismo día
$keysConMismoDia = [];
foreach ($bitren as $key => $v) {
    $p = explode('|', $key);
    $keysConMismoDia[$p[0] . '|' . $p[1]] = true;
}
$falsosPositivosKeys = array_filter(array_keys($bitrenSinFecha), static function ($keySinFecha) use ($keysConMismoDia) {
    return ! isset($keysConMismoDia[$keySinFecha]);
});
$falsosPositivos = count($falsosPositivosKeys);

// De los falsos positivos: cuántos tienen todas las fechas de carga en la misma semana
$mismaSemana = 0;
$casosMismaSemana = [];
foreach ($falsosPositivosKeys as $keySinFecha) {
    $fechas = [];
    foreach ($all as $keyAll => $v) {
        if (strpos($keyAll, $keySinFecha . '|') === 0) {
            $fechas[] = $v['fecha'];
        }
    }
    $fechas = array_unique($fechas);
    if (count($fechas) < 2) {
        continue;
    }
    $semanas = array_map(static function ($f) {
        return date('o-W', strtotime($f));
    }, $fechas);
    if (count(array_unique($semanas)) === 1) {
        $mismaSemana++;
        $casosMismaSemana[] = $keySinFecha;
    }
}

echo "Candidatos a bitren SIN filtrar por día (IdTta + PatenteTractor): " . count($bitrenSinFecha) . "\n";
echo "Candidatos a bitren CON mismo día (IdTta + PatenteTractor + fecha): " . count($bitren) . "\n";
echo "Falsos positivos filtrados (mismo tractor, 2+ semis pero en días distintos): " . $falsosPositivos . "\n";
echo "De esos falsos positivos, con fechas de carga en la MISMA SEMANA: " . $mismaSemana . "\n\n";
echo "Total grupos (IdTta + PatenteTractor + fecha) con al menos 1 unidad: " . count($all) . "\n\n";

// Detalle del equipo con cargas en la misma semana (falso positivo)
if (count($casosMismaSemana) > 0) {
    echo "Equipo(s) falso positivo con cargas en la MISMA SEMANA (detalle de las 2 cargas):\n";
    echo str_repeat('-', 80) . "\n";
    foreach ($casosMismaSemana as $keySinFecha) {
        $partes = explode('|', $keySinFecha);
        $idTta = $partes[0];
        $tractorNorm = $partes[1];
        $entradas = [];
        foreach ($all as $keyAll => $v) {
            if (strpos($keyAll, $keySinFecha . '|') === 0) {
                $entradas[] = ['fecha' => $v['fecha'], 'patentes' => $v['patentes'], 'tractor' => $v['tractor']];
            }
        }
        usort($entradas, static function ($a, $b) {
            return strcmp($a['fecha'], $b['fecha']);
        });
        $tractorLabel = $entradas[0]['tractor'] ?? '';
        echo "IdTta={$idTta}  PatenteTractor=\"{$tractorLabel}\"\n";
        foreach ($entradas as $i => $e) {
            $n = $i + 1;
            echo "  Carga {$n}:  UltActualiz={$e['fecha']}  ->  Patente(s) semi: " . implode(', ', $e['patentes']) . "\n";
        }
        echo "\n";
    }
}

if (count($bitren) > 0) {
    echo "Listado (IdTta, Tractor, Fecha -> Patentes de semi):\n";
    echo str_repeat('-', 80) . "\n";
    $n = 0;
    foreach ($bitren as $v) {
        $n++;
        echo sprintf(
            "%3d. IdTta=%s  Tractor=\"%s\"  Fecha=%s  ->  Semis: %s\n",
            $n,
            $v['id_tta'],
            $v['tractor'],
            $v['fecha'] ?? '',
            implode('  |  ', $v['patentes'])
        );
    }
}
