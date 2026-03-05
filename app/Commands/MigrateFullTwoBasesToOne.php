<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Migración unificada: camiones + calibraciones → montajes_campana.
 *
 * Orden: pre-vuelo, (opcional) backup, (opcional) truncate, transacción:
 * transportistas desde calibraciones, sync transportistas desde camiones,
 * equipos desde camiones, equipos faltantes desde unidades, calibraciones/detalle/multiflecha,
 * choferes. Verificación post-migración obligatoria.
 *
 * Uso: php spark migrate:full-2-to-1 [--dry-run] [--truncate] [--backup-dir=ruta]
 */
class MigrateFullTwoBasesToOne extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'migrate:full-2-to-1';
    protected $description = 'Migra camiones + calibraciones a montajes_campana (transportistas, equipos, calibraciones, choferes).';
    protected $usage       = 'migrate:full-2-to-1 [options]';

    /** @var array<string, string> */
    protected $options = [
        '--dry-run'     => 'Solo pre-vuelo y reporte; no escribe en BD.',
        '--truncate'    => 'Vaciar tablas destino antes de migrar (calibraciones y relacionadas, equipos, transportistas).',
        '--backup-dir'  => 'Carpeta donde hacer backup (mysqldump) de montajes_campana antes de truncar.',
    ];

    private \CodeIgniter\Database\BaseConnection $db;
    private \CodeIgniter\Database\BaseConnection $oldDb;
    private \CodeIgniter\Database\BaseConnection $camionesDb;
    private bool $dryRun = false;
    private bool $truncate = false;
    private ?string $backupDir = null;
    private array $log = [];
    private float $startTime;

    private function log(string $msg, string $level = 'info'): void
    {
        $this->log[] = ['t' => microtime(true) - $this->startTime, 'level' => $level, 'msg' => $msg];
        CLI::write($msg, $level === 'error' ? 'red' : ($level === 'warning' ? 'yellow' : 'green'));
    }

    private function normalizarNombre(?string $s): string
    {
        if ($s === null || $s === '') {
            return '';
        }
        $s = trim($s);
        $s = mb_strtoupper($s, 'UTF-8');
        return (string) preg_replace('/\s+/u', ' ', $s);
    }

    private function normalizePatente(?string $s): string
    {
        if ($s === null || $s === '') {
            return '';
        }
        return strtoupper(str_replace(' ', '', trim($s)));
    }

    /**
     * Pre-vuelo: conexiones y tablas mínimas.
     */
    private function preFlight(): bool
    {
        $this->log('Pre-vuelo: verificando conexiones y tablas...', 'info');
        try {
            $this->db         = \Config\Database::connect();
            $this->oldDb      = \Config\Database::connect('old');
            $this->camionesDb = \Config\Database::connect('camiones');
        } catch (\Throwable $e) {
            $this->log('Pre-vuelo fallido: ' . $e->getMessage(), 'error');
            return false;
        }
        $requiredOld = ['transportistas', 'unidades', 'calib', 'cisternas', 'cisternas_multi', 'nacion'];
        foreach ($requiredOld as $t) {
            if (! $this->oldDb->tableExists($t)) {
                $this->log("Pre-vuelo: en BD old falta la tabla: {$t}", 'error');
                return false;
            }
        }
        $requiredCamiones = ['equipos', 'ttas'];
        $config = config('Camiones');
        $tEquipos = $config->tableEquipos ?? 'equipos';
        $tTtas    = $config->tableTtas ?? 'ttas';
        if (! $this->camionesDb->tableExists($tEquipos) || ! $this->camionesDb->tableExists($tTtas)) {
            $this->log('Pre-vuelo: en BD camiones faltan tablas equipos o ttas.', 'error');
            return false;
        }
        $requiredNew = ['transportistas', 'equipos', 'calibraciones', 'calibracion_detalle', 'calibracion_multiflecha', 'items_censo'];
        foreach ($requiredNew as $t) {
            if (! $this->db->tableExists($t)) {
                $this->log("Pre-vuelo: en BD destino falta la tabla: {$t}", 'error');
                return false;
            }
        }
        $this->log('Pre-vuelo OK.', 'info');
        return true;
    }

    /**
     * Truncar tablas en orden (hijas antes que padres). Con FOREIGN_KEY_CHECKS=0.
     */
    private function truncateTables(): void
    {
        $tables = [
            'calibracion_multiflecha',
            'calibracion_detalle',
        ];
        if ($this->db->tableExists('calibracion_reimpresiones')) {
            $tables[] = 'calibracion_reimpresiones';
        }
        if ($this->db->tableExists('calibracion_notas')) {
            $tables[] = 'calibracion_notas';
        }
        $tables[] = 'calibraciones';
        $tables[] = 'equipos';
        $tables[] = 'transportistas';

        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
        foreach ($tables as $t) {
            if ($this->db->tableExists($t)) {
                $this->db->table($t)->truncate();
                $this->log("  Truncada: {$t}", 'info');
            }
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }

    /**
     * Sincronizar transportistas desde camiones (crear faltantes, actualizar tipo/codigo_axion). Inline.
     */
    private function syncTransportistasFromCamiones(): void
    {
        $config   = config('Camiones');
        $tableTtas = $config->tableTtas ?? 'ttas';
        $colNombre = $config->columnTtasNombre ?? 'Transportista';
        $colTipo   = $config->columnTtasTipo ?? 'IdTipoTta';
        $colCodAxion = $config->columnTtasCodAxion ?? 'CodAxion';
        $colNacion = trim((string) ($config->columnTtasNacion ?? ''));
        $mapTtas   = $config->columnMapTtasToTransportistas ?? [];

        $selectParts = [];
        foreach ($mapTtas as $ourCol => $ttasCol) {
            $selectParts[] = $ttasCol . ' as ' . $ourCol;
        }
        if ($selectParts === []) {
            $selectParts = [$colNombre . ' as transportista', $colTipo . ' as tipo', $colCodAxion . ' as codigo_axion'];
            if ($colNacion !== '') {
                $selectParts[] = $colNacion . ' as pais_id';
            }
        }
        $rowsTtas = $this->camionesDb->table($tableTtas)->select(implode(', ', $selectParts))->get()->getResultArray();
        $ourTrans = $this->db->table('transportistas')->select('id_tta, transportista')->get()->getResultArray();
        $ourByNombre = [];
        foreach ($ourTrans as $t) {
            $n = $this->normalizarNombre($t['transportista'] ?? '');
            if ($n !== '') {
                $ourByNombre[$n] = (int) $t['id_tta'];
            }
        }
        $mapNacionIdToNombre = [];
        if ($colNacion !== '') {
            $naciones = $this->db->table('naciones')->select('id_nacion, nacion')->get()->getResultArray();
            foreach ($naciones as $n) {
                $mapNacionIdToNombre[(int) $n['id_nacion']] = $n['nacion'] ?? '';
            }
        }
        $allowedInsert = ['transportista', 'cuit', 'tipo', 'codigo_axion', 'direccion', 'localidad', 'codigo_postal', 'provincia', 'nacion', 'pais_id', 'mail_contacto', 'telefono', 'comentarios'];
        $creados = 0;
        $actualizados = 0;
        foreach ($rowsTtas as $r) {
            $nombre = trim((string) ($r['transportista'] ?? ''));
            if ($nombre === '') {
                continue;
            }
            $nombreNorm = $this->normalizarNombre($nombre);
            $tipo = trim((string) ($r['tipo'] ?? ''));
            $codigoAxion = trim((string) ($r['codigo_axion'] ?? ''));
            $ourId = $ourByNombre[$nombreNorm] ?? null;
            if ($ourId !== null) {
                if (! $this->dryRun) {
                    $this->db->table('transportistas')->where('id_tta', $ourId)->update([
                        'tipo' => $tipo !== '' ? $tipo : null,
                        'codigo_axion' => $codigoAxion !== '' ? $codigoAxion : null,
                    ]);
                }
                $actualizados++;
                continue;
            }
            $dataInsert = ['transportista' => $nombre, 'tipo' => $tipo ?: null, 'codigo_axion' => $codigoAxion ?: null];
            foreach ($allowedInsert as $key) {
                if ($key === 'nacion' || ! array_key_exists($key, $r)) {
                    continue;
                }
                $val = $r[$key];
                if ($key === 'pais_id') {
                    $idPais = is_numeric($val) ? (int) $val : null;
                    $dataInsert['pais_id'] = $idPais;
                    $dataInsert['nacion'] = $idPais !== null ? ($mapNacionIdToNombre[$idPais] ?? null) : null;
                } else {
                    $dataInsert[$key] = is_string($val) ? (trim($val) ?: null) : $val;
                }
            }
            if (! $this->dryRun) {
                $this->db->table('transportistas')->insert($dataInsert);
                $ourByNombre[$nombreNorm] = (int) $this->db->insertID();
            }
            $creados++;
        }
        $this->log("  Transportistas desde camiones: actualizados {$actualizados}, creados {$creados}.", 'info');
    }

    /**
     * Construir mapa id_tta camiones -> id_tta montajes (por nombre).
     */
    private function buildIdTtaCamionesToMontajes(): array
    {
        $config    = config('Camiones');
        $tableTtas = $config->tableTtas ?? 'ttas';
        $colNombre = $config->columnTtasNombre ?? 'Transportista';
        $colId     = $config->columnTtasId ?? 'IdTta';
        $ttas      = $this->camionesDb->table($tableTtas)->select($colId . ', ' . $colNombre)->get()->getResultArray();
        $ourTrans  = $this->db->table('transportistas')->select('id_tta, transportista')->get()->getResultArray();
        $ourByNombre = [];
        foreach ($ourTrans as $t) {
            $n = $this->normalizarNombre($t['transportista'] ?? '');
            if ($n !== '') {
                $ourByNombre[$n] = (int) $t['id_tta'];
            }
        }
        $map = [];
        foreach ($ttas as $r) {
            $idCam = (int) ($r[$colId] ?? 0);
            $nombre = trim((string) ($r[$colNombre] ?? ''));
            if ($idCam === 0 || $nombre === '') {
                continue;
            }
            $n = $this->normalizarNombre($nombre);
            if (isset($ourByNombre[$n])) {
                $map[$idCam] = $ourByNombre[$n];
            }
        }
        return $map;
    }

    /**
     * Añadir equipos desde unidades (calibraciones) solo los que no existan (por patente_tractor + patente_semi_delantero).
     */
    private function addEquiposFromUnidades(): int
    {
        $rows = $this->oldDb->table('unidades')->get()->getResultArray();
        $groups = [];
        foreach ($rows as $r) {
            $tractor = trim((string) ($r['PatenteTractor'] ?? ''));
            $patente = trim((string) ($r['Patente'] ?? ''));
            if ($patente === '') {
                continue;
            }
            $key = (int) $r['IdTta'] . '|' . $this->normalizePatente($tractor);
            if (! isset($groups[$key])) {
                $groups[$key] = ['patentes' => [], 'rows' => []];
            }
            if (! in_array($patente, $groups[$key]['patentes'], true)) {
                $groups[$key]['patentes'][] = $patente;
                $groups[$key]['rows'][$patente] = $r;
            }
        }
        $bitrenMain = [];
        $bitrenSecondaryToMain = [];
        foreach ($groups as $g) {
            if (count($g['patentes']) < 2) {
                continue;
            }
            $main = $g['patentes'][0];
            $bitrenMain[$main] = $g['patentes'];
            for ($i = 1; $i < count($g['patentes']); $i++) {
                $bitrenSecondaryToMain[$g['patentes'][$i]] = $main;
            }
        }

        $existentes = $this->db->table('equipos')->select('id_equipo, patente_tractor, patente_semi_delantero')->get()->getResultArray();
        $existentesByKey = [];
        foreach ($existentes as $e) {
            $k = $this->normalizePatente($e['patente_tractor'] ?? '') . '|' . $this->normalizePatente($e['patente_semi_delantero'] ?? '');
            $existentesByKey[$k] = true;
        }

        $inserted = 0;
        $now = date('Y-m-d H:i:s');
        foreach ($rows as $r) {
            $patente = trim((string) ($r['Patente'] ?? ''));
            if (isset($bitrenSecondaryToMain[$patente])) {
                continue;
            }
            $idTta = (int) $r['IdTta'];
            $tractor = trim((string) ($r['PatenteTractor'] ?? ''));
            $keyExist = $this->normalizePatente($tractor) . '|' . $this->normalizePatente($patente);
            if (isset($existentesByKey[$keyExist])) {
                continue;
            }
            $bitren = 'NO';
            $patenteSemiTrasero = null;
            if (isset($bitrenMain[$patente])) {
                $bitren = 'SI';
                $patenteSemiTrasero = $bitrenMain[$patente][1] ?? null;
            }
            $row = [
                'patente_semi_delantero' => $patente,
                'id_tta' => $idTta,
                'patente_tractor' => $tractor ?: null,
                'bitren' => $bitren,
                'patente_semi_trasero' => $patenteSemiTrasero,
                'id_bandera' => isset($r['IdBandera']) ? (int) $r['IdBandera'] : null,
                'id_marca' => isset($r['IdMarca']) ? (int) $r['IdMarca'] : null,
                'semi_delantero_anio_modelo' => isset($r['AnioFab']) ? (int) $r['AnioFab'] : null,
                'semi_delantero_tara' => isset($r['Tara']) ? (float) $r['Tara'] : null,
                'observaciones' => $r['ComenUnidad'] ?? null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            if (! $this->dryRun) {
                $this->db->table('equipos')->insert($row);
                $existentesByKey[$keyExist] = true;
            }
            $inserted++;
        }
        $this->log("  Equipos añadidos desde unidades (solo faltantes): {$inserted}.", 'info');
        return $inserted;
    }

    /**
     * Construir mapa patente -> id_equipo desde tabla equipos (patente_semi_delantero y patente_semi_trasero).
     */
    private function buildPatenteToIdEquipo(): array
    {
        $rows = $this->db->table('equipos')->select('id_equipo, patente_semi_delantero, patente_semi_trasero')->get()->getResultArray();
        $map = [];
        foreach ($rows as $r) {
            $id = (int) $r['id_equipo'];
            $p1 = trim((string) ($r['patente_semi_delantero'] ?? ''));
            $p2 = trim((string) ($r['patente_semi_trasero'] ?? ''));
            if ($p1 !== '') {
                $map[$p1] = $id;
            }
            if ($p2 !== '') {
                $map[$p2] = $id;
            }
        }
        return $map;
    }

    /**
     * Unifica transportistas duplicados por nombre: conserva el menor id_tta, actualiza equipos y choferes, borra el duplicado.
     * Devuelve número de transportistas eliminados (duplicados unificados). Escribe auditoría en writable/logs/.
     */
    private function unificarTransportistasDuplicados(string $ts): int
    {
        $rows = $this->db->table('transportistas')->select('id_tta, transportista')->get()->getResultArray();
        $porNombre = [];
        foreach ($rows as $r) {
            $n = $this->normalizarNombre($r['transportista'] ?? '');
            if ($n === '') {
                continue;
            }
            if (! isset($porNombre[$n])) {
                $porNombre[$n] = [];
            }
            $porNombre[$n][] = ['id_tta' => (int) $r['id_tta'], 'transportista' => trim((string) ($r['transportista'] ?? ''))];
        }
        $audit = [];
        $eliminados = 0;
        foreach ($porNombre as $nombreNorm => $list) {
            if (count($list) < 2) {
                continue;
            }
            usort($list, static fn ($a, $b) => $a['id_tta'] <=> $b['id_tta']);
            $conservar = $list[0]['id_tta'];
            for ($i = 1; $i < count($list); $i++) {
                $otro = $list[$i]['id_tta'];
                $this->db->table('equipos')->where('id_tta', $otro)->update(['id_tta' => $conservar]);
                $this->db->table('choferes')->where('id_tta', $otro)->update(['id_tta' => $conservar]);
                $this->db->table('transportistas')->where('id_tta', $otro)->delete();
                $audit[] = "id_tta={$otro} → referencias pasadas a id_tta={$conservar} (\"{$nombreNorm}\"), fila eliminada.";
                $eliminados++;
            }
        }
        if ($audit !== []) {
            $path = $this->getReportDir() . DIRECTORY_SEPARATOR . 'migrate-full-2-to-1-unificados-transportistas-' . $ts . '.txt';
            $lines = array_merge(
                ['Unificación automática: transportistas duplicados por nombre (migrate:full-2-to-1)', 'Generado: ' . date('Y-m-d H:i:s'), 'Total filas eliminadas: ' . $eliminados, ''],
                $audit
            );
            file_put_contents($path, implode("\n", $lines));
        }
        return $eliminados;
    }

    /**
     * Unifica equipos duplicados por (patente_tractor, patente_semi_delantero): conserva el menor id_equipo, actualiza calibraciones, borra el duplicado.
     */
    private function unificarEquiposDuplicados(string $ts): int
    {
        $rows = $this->db->table('equipos')->select('id_equipo, patente_tractor, patente_semi_delantero')->get()->getResultArray();
        $porClave = [];
        foreach ($rows as $r) {
            $k = $this->normalizePatente($r['patente_tractor'] ?? '') . '|' . $this->normalizePatente($r['patente_semi_delantero'] ?? '');
            if ($k === '|') {
                continue;
            }
            if (! isset($porClave[$k])) {
                $porClave[$k] = [];
            }
            $porClave[$k][] = ['id_equipo' => (int) $r['id_equipo'], 'patente_tractor' => $r['patente_tractor'] ?? '', 'patente_semi_delantero' => $r['patente_semi_delantero'] ?? ''];
        }
        $audit = [];
        $eliminados = 0;
        foreach ($porClave as $clave => $list) {
            if (count($list) < 2) {
                continue;
            }
            usort($list, static fn ($a, $b) => $a['id_equipo'] <=> $b['id_equipo']);
            $conservar = $list[0]['id_equipo'];
            for ($i = 1; $i < count($list); $i++) {
                $otro = $list[$i]['id_equipo'];
                $this->db->table('calibraciones')->where('id_equipo', $otro)->update(['id_equipo' => $conservar]);
                $this->db->table('equipos')->where('id_equipo', $otro)->delete();
                $audit[] = "id_equipo={$otro} (clave {$clave}) → calibraciones pasadas a id_equipo={$conservar}, fila eliminada.";
                $eliminados++;
            }
        }
        if ($audit !== []) {
            $path = $this->getReportDir() . DIRECTORY_SEPARATOR . 'migrate-full-2-to-1-unificados-equipos-' . $ts . '.txt';
            $lines = array_merge(
                ['Unificación automática: equipos duplicados por patente (migrate:full-2-to-1)', 'Generado: ' . date('Y-m-d H:i:s'), 'Total filas eliminadas: ' . $eliminados, ''],
                $audit
            );
            file_put_contents($path, implode("\n", $lines));
        }
        return $eliminados;
    }

    /** Ruta al directorio de reportes de esta ejecución (writable/logs). */
    private function getReportDir(): string
    {
        $dir = WRITEPATH . 'logs';
        if (! is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $dir;
    }

    /**
     * Verificación post-migración: conteos, calibraciones sin id_equipo, duplicados restantes y huérfanos.
     * Guarda cada tipo de incidencia en un archivo en writable/logs/ para revisar y corregir IDs.
     */
    private function postVerification(string $ts): bool
    {
        $ok = true;
        $nTransportistas = $this->db->table('transportistas')->countAllResults();
        $nEquipos = $this->db->table('equipos')->countAllResults();
        $nCalib = $this->db->table('calibraciones')->countAllResults();
        $nSinEquipo = $this->db->table('calibraciones')->where('id_equipo', null)->orWhere('id_equipo', 0)->countAllResults();
        $this->log("  Transportistas: {$nTransportistas}. Equipos: {$nEquipos}. Calibraciones: {$nCalib}. Sin id_equipo: {$nSinEquipo}.", 'info');

        if ($nCalib > 0 && $nSinEquipo > 0) {
            $filas = $this->db->table('calibraciones')
                ->select('id_calibracion, patente, fecha_calib, vto_calib, id_calibrador')
                ->where('id_equipo', null)
                ->orWhere('id_equipo', 0)
                ->get()
                ->getResultArray();
            $reportPath = $this->guardarReporteCalibracionesSinEquipo($filas, $ts);
            $this->log("  Advertencia: hay calibraciones sin id_equipo asignado. Detalle: " . $reportPath, 'warning');
        }

        $this->reportarTransportistasDuplicados($ts);
        $this->reportarEquiposDuplicados($ts);
        $this->reportarEquiposConIdTtaInexistente($ts);
        $this->reportarCalibracionesConIdEquipoInexistente($ts);

        return $ok;
    }

    /**
     * Transportistas con el mismo nombre (normalizado) pero distinto id_tta.
     * Al unificar duplicados, reasignar referencias al id_tta que se conserve.
     */
    private function reportarTransportistasDuplicados(string $ts): void
    {
        $rows = $this->db->table('transportistas')->select('id_tta, transportista')->get()->getResultArray();
        $porNombre = [];
        foreach ($rows as $r) {
            $n = $this->normalizarNombre($r['transportista'] ?? '');
            if ($n === '') {
                continue;
            }
            if (! isset($porNombre[$n])) {
                $porNombre[$n] = [];
            }
            $porNombre[$n][] = ['id_tta' => $r['id_tta'], 'transportista' => trim((string) ($r['transportista'] ?? ''))];
        }
        $duplicados = [];
        foreach ($porNombre as $nombreNorm => $list) {
            if (count($list) > 1) {
                $duplicados[] = ['nombre_normalizado' => $nombreNorm, 'registros' => $list];
            }
        }
        if ($duplicados === []) {
            return;
        }
        $path = $this->getReportDir() . DIRECTORY_SEPARATOR . 'migrate-full-2-to-1-transportistas-duplicados-' . $ts . '.txt';
        $lines = [
            'Reporte: transportistas duplicados restantes (mismo nombre, distinto id_tta)',
            'El script ya unificó duplicados automáticamente. Si este archivo existe, revisar a mano: elegir id_tta a conservar y actualizar equipos/choferes.',
            'Generado: ' . date('Y-m-d H:i:s'),
            'Total grupos duplicados: ' . count($duplicados),
            '',
        ];
        foreach ($duplicados as $g) {
            $lines[] = 'Nombre: ' . $g['nombre_normalizado'];
            foreach ($g['registros'] as $r) {
                $lines[] = '  id_tta=' . $r['id_tta'] . ' | transportista="' . $r['transportista'] . '"';
            }
            $lines[] = '';
        }
        file_put_contents($path, implode("\n", $lines));
        $this->log("  Advertencia: transportistas duplicados por nombre. Detalle: " . $path, 'warning');
    }

    /**
     * Equipos con la misma clave (patente_tractor + patente_semi_delantero) pero distinto id_equipo.
     */
    private function reportarEquiposDuplicados(string $ts): void
    {
        $rows = $this->db->table('equipos')->select('id_equipo, patente_tractor, patente_semi_delantero, id_tta')->get()->getResultArray();
        $porClave = [];
        foreach ($rows as $r) {
            $k = $this->normalizePatente($r['patente_tractor'] ?? '') . '|' . $this->normalizePatente($r['patente_semi_delantero'] ?? '');
            if ($k === '|') {
                continue;
            }
            if (! isset($porClave[$k])) {
                $porClave[$k] = [];
            }
            $porClave[$k][] = [
                'id_equipo' => $r['id_equipo'],
                'patente_tractor' => $r['patente_tractor'] ?? '',
                'patente_semi_delantero' => $r['patente_semi_delantero'] ?? '',
                'id_tta' => $r['id_tta'] ?? null,
            ];
        }
        $duplicados = [];
        foreach ($porClave as $clave => $list) {
            if (count($list) > 1) {
                $duplicados[] = ['clave' => $clave, 'registros' => $list];
            }
        }
        if ($duplicados === []) {
            return;
        }
        $path = $this->getReportDir() . DIRECTORY_SEPARATOR . 'migrate-full-2-to-1-equipos-duplicados-' . $ts . '.txt';
        $lines = [
            'Reporte: equipos duplicados restantes (misma patente tractor + semi, distinto id_equipo)',
            'El script ya unificó duplicados automáticamente. Si este archivo existe, revisar a mano: elegir id_equipo a conservar y actualizar calibraciones.',
            'Generado: ' . date('Y-m-d H:i:s'),
            'Total grupos duplicados: ' . count($duplicados),
            '',
            'id_equipo | patente_tractor | patente_semi_delantero | id_tta',
            str_repeat('-', 70),
        ];
        foreach ($duplicados as $g) {
            $lines[] = 'Clave: ' . $g['clave'];
            foreach ($g['registros'] as $r) {
                $lines[] = sprintf('  %s | %s | %s | %s', $r['id_equipo'], $r['patente_tractor'], $r['patente_semi_delantero'], $r['id_tta']);
            }
            $lines[] = '';
        }
        file_put_contents($path, implode("\n", $lines));
        $this->log("  Advertencia: equipos duplicados por patente. Detalle: " . $path, 'warning');
    }

    /**
     * Equipos cuyo id_tta no existe en transportistas (huérfanos).
     */
    private function reportarEquiposConIdTtaInexistente(string $ts): void
    {
        $idsTta = $this->db->table('transportistas')->select('id_tta')->get()->getResultArray();
        $idsTta = array_column($idsTta, 'id_tta');
        $equipos = $this->db->table('equipos')->select('id_equipo, id_tta, patente_tractor, patente_semi_delantero')->get()->getResultArray();
        $huérfanos = [];
        foreach ($equipos as $e) {
            $idTta = (int) ($e['id_tta'] ?? 0);
            if ($idTta === 0) {
                continue;
            }
            if (! in_array($idTta, $idsTta, true)) {
                $huérfanos[] = $e;
            }
        }
        if ($huérfanos === []) {
            return;
        }
        $path = $this->getReportDir() . DIRECTORY_SEPARATOR . 'migrate-full-2-to-1-equipos-id_tta-inexistente-' . $ts . '.txt';
        $lines = [
            'Reporte: equipos con id_tta que no existe en transportistas',
            'Acción: dar de alta el transportista o reasignar equipos a un id_tta válido.',
            'Generado: ' . date('Y-m-d H:i:s'),
            'Total: ' . count($huérfanos),
            '',
            'id_equipo | id_tta | patente_tractor | patente_semi_delantero',
            str_repeat('-', 70),
        ];
        foreach ($huérfanos as $r) {
            $lines[] = sprintf('%s | %s | %s | %s', $r['id_equipo'], $r['id_tta'], $r['patente_tractor'] ?? '', $r['patente_semi_delantero'] ?? '');
        }
        file_put_contents($path, implode("\n", $lines));
        $this->log("  Advertencia: equipos con id_tta inexistente. Detalle: " . $path, 'warning');
    }

    /**
     * Calibraciones con id_equipo distinto de null/0 pero que no existe en equipos (huérfanos).
     */
    private function reportarCalibracionesConIdEquipoInexistente(string $ts): void
    {
        $idsEquipo = $this->db->table('equipos')->select('id_equipo')->get()->getResultArray();
        $idsEquipo = array_column($idsEquipo, 'id_equipo');
        $calib = $this->db->table('calibraciones')
            ->select('id_calibracion, patente, id_equipo, fecha_calib, vto_calib')
            ->where('id_equipo >', 0)
            ->get()
            ->getResultArray();
        $huérfanos = [];
        foreach ($calib as $c) {
            $idEq = (int) ($c['id_equipo'] ?? 0);
            if ($idEq > 0 && ! in_array($idEq, $idsEquipo, true)) {
                $huérfanos[] = $c;
            }
        }
        if ($huérfanos === []) {
            return;
        }
        $path = $this->getReportDir() . DIRECTORY_SEPARATOR . 'migrate-full-2-to-1-calibraciones-id_equipo-inexistente-' . $ts . '.txt';
        $lines = [
            'Reporte: calibraciones con id_equipo que no existe en equipos',
            'Acción: asignar un id_equipo válido o crear el equipo faltante.',
            'Generado: ' . date('Y-m-d H:i:s'),
            'Total: ' . count($huérfanos),
            '',
            'id_calibracion | patente | id_equipo | fecha_calib | vto_calib',
            str_repeat('-', 70),
        ];
        foreach ($huérfanos as $r) {
            $lines[] = sprintf('%s | %s | %s | %s | %s', $r['id_calibracion'], $r['patente'] ?? '', $r['id_equipo'], $r['fecha_calib'] ?? '', $r['vto_calib'] ?? '');
        }
        file_put_contents($path, implode("\n", $lines));
        $this->log("  Advertencia: calibraciones con id_equipo inexistente. Detalle: " . $path, 'warning');
    }

    /**
     * Guarda en writable/logs/ el listado de calibraciones sin id_equipo.
     * @param array<int, array<string, mixed>> $filas
     */
    private function guardarReporteCalibracionesSinEquipo(array $filas, string $ts): string
    {
        $path = $this->getReportDir() . DIRECTORY_SEPARATOR . 'migrate-full-2-to-1-calibraciones-sin-equipo-' . $ts . '.txt';
        $lines = [
            'Reporte: calibraciones sin id_equipo asignado (migrate:full-2-to-1)',
            'Acción: crear equipo para la patente o asignar id_equipo existente.',
            'Generado: ' . date('Y-m-d H:i:s'),
            'Total: ' . count($filas) . ' registros',
            '',
            'id_calibracion | patente | fecha_calib | vto_calib | id_calibrador',
            str_repeat('-', 70),
        ];
        foreach ($filas as $r) {
            $lines[] = sprintf(
                '%s | %s | %s | %s | %s',
                $r['id_calibracion'] ?? '',
                $r['patente'] ?? '',
                $r['fecha_calib'] ?? '',
                $r['vto_calib'] ?? '',
                $r['id_calibrador'] ?? ''
            );
        }
        file_put_contents($path, implode("\n", $lines));
        return $path;
    }

    public function run(array $params): int
    {
        $this->dryRun    = (bool) CLI::getOption('dry-run');
        $this->truncate  = (bool) CLI::getOption('truncate');
        $this->backupDir = CLI::getOption('backup-dir') !== null ? trim((string) CLI::getOption('backup-dir')) : null;
        $this->startTime = microtime(true);

        if ($this->dryRun) {
            CLI::write('Modo dry-run: no se escribirá en la BD.', 'yellow');
        }

        if (! $this->preFlight()) {
            return 1;
        }

        if ($this->dryRun) {
            $this->log('Dry-run: se ejecutarían truncate (si --truncate), transportistas, equipos, calibraciones, choferes.', 'info');
            return 0;
        }

        if ($this->truncate) {
            if ($this->backupDir !== null && $this->backupDir !== '') {
                $dbConfig = config('Database');
                $default = $dbConfig->default ?? [];
                $database = $default['database'] ?? 'montajes_campana';
                $file = rtrim($this->backupDir, '\\/') . DIRECTORY_SEPARATOR . 'backup_' . $database . '_' . date('Y-m-d_His') . '.sql';
                $cmd = sprintf('mysqldump -h %s -u %s %s %s > %s', $default['hostname'] ?? 'localhost', $default['username'] ?? 'root', ! empty($default['password']) ? '-p' . $default['password'] : '', $database, $file);
                @exec($cmd);
                $this->log("Backup solicitado en: {$file}", 'info');
            }
            $this->log('Truncando tablas...', 'info');
            $this->truncateTables();
        }

        $this->db->transStart();
        try {
            $this->log('Fase: Transportistas desde calibraciones.', 'info');
            $migrateOld = new MigrateOldDb($this->logger, $this->commands);
            $migrateOld->setConnections($this->oldDb, $this->db);
            $migrateOld->setTruncate(false);
            $migrateOld->setDryRun(false);
            $migrateOld->migrateTransportistas();

            $this->log('Fase: Sync transportistas desde camiones.', 'info');
            $this->syncTransportistasFromCamiones();

            $this->log('Fase: Equipos desde camiones.', 'info');
            $idTtaMap = $this->buildIdTtaCamionesToMontajes();
            $importCmd = new EquiposImportarDesdeCamiones($this->logger, $this->commands);
            $res = $importCmd->runWithMap($idTtaMap, false, true);
            $this->log("  Insertados: {$res['inserted']}. Actualizados: {$res['updated']}.", 'info');

            $this->log('Fase: Equipos faltantes desde unidades.', 'info');
            $this->addEquiposFromUnidades();

            $this->log('Fase: Calibraciones (y detalle, multiflecha).', 'info');
            $patenteToIdEquipo = $this->buildPatenteToIdEquipo();
            $migrateOld->setPatenteToIdUnidad($patenteToIdEquipo);
            $migrateOld->migrateCalibraciones();
            $migrateOld->migrateCalibracionDetalle();
            $migrateOld->migrateCalibracionMultiflecha();

            $this->log('Fase: Choferes desde camiones.', 'info');
            $choferesCmd = new ChoferesDesdeCamiones($this->logger, $this->commands);
            $choferesCmd->run([]);

            $this->log('Fase: Ítems censo desde camiones.', 'info');
            $itemsCensoCmd = new ItemsCensoDesdeCamiones($this->logger, $this->commands);
            $itemsCensoCmd->run([]);

            $ts = date('Y-m-d_His');
            $this->log('Fase: Unificar duplicados (transportistas por nombre, equipos por patente).', 'info');
            $nTta = $this->unificarTransportistasDuplicados($ts);
            $nEq  = $this->unificarEquiposDuplicados($ts);
            if ($nTta > 0 || $nEq > 0) {
                $this->log("  Unificados: {$nTta} transportistas, {$nEq} equipos; detalle en writable/logs/.", 'info');
            }

            $this->log('Verificación post-migración.', 'info');
            $this->postVerification($ts);

            $this->db->transComplete();
            if ($this->db->transStatus() === false) {
                $this->log('Transacción falló; se revirtieron los cambios.', 'error');
                return 1;
            }
        } catch (\Throwable $e) {
            $this->db->transRollback();
            $this->log('Error: ' . $e->getMessage(), 'error');
            CLI::write($e->getTraceAsString(), 'light_gray');
            return 1;
        }

        $elapsed = round(microtime(true) - $this->startTime, 2);
        $this->log("Migración finalizada en {$elapsed} s.", 'info');
        return 0;
    }
}
