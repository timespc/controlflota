<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Completa equipos (tabla unidades) con datos de la BD camiones (tabla equipos).
 * Por defecto empareja por patente tractor + patente semi (sin exigir id_tta).
 * Copia: fecha_alta, modo_carga, tara_total, peso_maximo, taras/PBT tractor y semis,
 * cisternas C1–C10, capacidad_total. Compara nación (equipo vs transportista).
 *
 * Requiere conexión 'camiones' en Config\Database. Mapeo de columnas en app/Config/Camiones.php.
 */
class CompletarEquiposDesdeCamiones extends BaseCommand
{
    protected $group       = 'Equipos';
    protected $name        = 'equipos:completar-desde-camiones';
    protected $description = 'Completa equipos (unidades) con datos de la BD camiones y compara nación.';
    protected $usage       = 'equipos:completar-desde-camiones [options]';

    /** @var array<string, string> */
    protected $options = [
        '--dry-run'  => 'Solo mostrar qué se haría, sin actualizar.',
        '--verbose'  => 'Mostrar muestras de claves (equipos vs unidades) para diagnosticar por qué no hay match.',
        '--con-tta'  => 'Exigir también id_tta para el match (por defecto se empareja solo por patente tractor + semi).',
    ];

    private \CodeIgniter\Database\BaseConnection $db;
    private \CodeIgniter\Database\BaseConnection $camionesDb;
    private bool $dryRun = false;
    private bool $matchSinTta = false;
    private bool $verbose = false;

    private function normalizePatente(?string $s): string
    {
        if ($s === null || $s === '') {
            return '';
        }
        return strtoupper(str_replace(' ', '', trim($s)));
    }

    private function normalizeNacion(?string $s): string
    {
        if ($s === null || $s === '') {
            return '';
        }
        return mb_strtolower(trim($s), 'UTF-8');
    }

    private function nacionDiferente(string $a, string $b): bool
    {
        if ($a === '' || $b === '') {
            return false;
        }
        return $this->normalizeNacion($a) !== $this->normalizeNacion($b);
    }

    public function run(array $params): int
    {
        $this->dryRun      = (bool) CLI::getOption('dry-run');
        $this->verbose     = (bool) CLI::getOption('verbose');
        $this->matchSinTta  = ! (bool) CLI::getOption('con-tta');

        if ($this->dryRun) {
            CLI::write('Modo dry-run: no se actualizará la BD.', 'yellow');
        }

        try {
            $this->db         = \Config\Database::connect();
            $this->camionesDb = \Config\Database::connect('camiones');
        } catch (\Throwable $e) {
            CLI::error('Error conectando a las bases de datos: ' . $e->getMessage());
            CLI::write('Verificá que la conexión "camiones" exista en app/Config/Database.php.', 'light_gray');
            return 1;
        }

        $config        = config('Camiones');
        $tableEquipos  = $config->tableEquipos ?? 'equipos';
        $columnMap     = $config->columnMap ?? [];
        $excludeColumns = $config->excludeColumns ?? [];
        $cols = [
            'id_tta' => 'id_tta',
            'tractor_patente' => 'tractor_patente',
            'semi_delan_patente' => 'semi_delan_patente',
            'fecha_alta' => 'fecha_alta',
            'modo_carga' => 'modo_carga',
            'tara_total' => 'tara_total',
            'peso_maximo' => 'peso_maximo',
            'nacion' => 'nacion',
            'tractor_tara' => 'tractor_tara',
            'tractor_pbt' => 'tractor_pbt',
            'semi_delantero_tara' => 'semi_delantero_tara',
            'semi_delantera_pbt' => 'semi_delantera_pbt',
            'patente_semi_trasero' => 'patente_semi_trasero',
            'semi_trasero_tara' => 'semi_trasero_tara',
            'semi_trasero_pbt' => 'semi_trasero_pbt',
            'cisterna_1_capacidad' => 'cisterna_1_capacidad',
            'cisterna_2_capacidad' => 'cisterna_2_capacidad',
            'cisterna_3_capacidad' => 'cisterna_3_capacidad',
            'cisterna_4_capacidad' => 'cisterna_4_capacidad',
            'cisterna_5_capacidad' => 'cisterna_5_capacidad',
            'cisterna_6_capacidad' => 'cisterna_6_capacidad',
            'cisterna_7_capacidad' => 'cisterna_7_capacidad',
            'cisterna_8_capacidad' => 'cisterna_8_capacidad',
            'cisterna_9_capacidad' => 'cisterna_9_capacidad',
            'cisterna_10_capacidad' => 'cisterna_10_capacidad',
            'capacidad_total' => 'capacidad_total',
        ];
        foreach ($columnMap as $alias => $real) {
            $cols[$alias] = $real;
        }
        $selectParts = [];
        foreach ($cols as $alias => $real) {
            if (in_array($alias, $excludeColumns, true)) {
                continue;
            }
            $selectParts[] = ($real === $alias) ? $real : $real . ' as ' . $alias;
        }
        $selectStr = implode(', ', $selectParts);

        if (! $this->camionesDb->tableExists($tableEquipos)) {
            CLI::error("La tabla \"{$tableEquipos}\" no existe en la BD camiones.");
            return 1;
        }

        CLI::write('Cargando equipos desde camiones...', 'cyan');
        $resultEquipos = $this->camionesDb->table($tableEquipos)
            ->select($selectStr)
            ->get();
        if ($resultEquipos === false) {
            $error = $this->camionesDb->error();
            CLI::error('Error al leer la tabla de equipos en camiones: ' . ($error['message'] ?? 'consulta fallida'));
            CLI::write('Si las columnas tienen otros nombres (ej. IdTta, PatTractor, PatSemi), configurá app/Config/Camiones.php con columnMap.', 'light_gray');
            return 1;
        }
        $equiposRows = $resultEquipos->getResultArray();

        $equiposByKey = [];
        foreach ($equiposRows as $r) {
            $idTta   = (int) ($r['id_tta'] ?? 0);
            $tractor = $this->normalizePatente($r['tractor_patente'] ?? $r['patente_tractor'] ?? '');
            $semi    = $this->normalizePatente($r['semi_delan_patente'] ?? $r['pat_semi'] ?? $r['PatSemi'] ?? '');
            if ($semi === '' || $tractor === '') {
                continue;
            }
            if ($this->matchSinTta) {
                $key = $tractor . '|' . $semi;
            } else {
                if ($idTta === 0) {
                    continue;
                }
                $key = $idTta . '|' . $tractor . '|' . $semi;
            }
            if (! isset($equiposByKey[$key])) {
                $equiposByKey[$key] = $r;
            }
        }
        CLI::write('  ' . count($equiposByKey) . ' equipos indexados por ' . ($this->matchSinTta ? '(tractor, semi)' : '(id_tta, tractor, semi)') . '.', 'green');
        if ($this->verbose && count($equiposByKey) > 0) {
            $samples = array_slice(array_keys($equiposByKey), 0, 10);
            CLI::write('  Muestra de claves en camiones: ' . implode(' ; ', $samples), 'light_gray');
        }

        CLI::write('Cargando unidades y transportistas...', 'cyan');
        $resultUnidades = $this->db->table('equipos')
            ->select('equipos.id_equipo, equipos.id_tta, equipos.patente_semi_delantero, equipos.patente_tractor, equipos.fecha_alta, equipos.modo_carga, equipos.tara_total, equipos.peso_maximo')
            ->join('transportistas', 'transportistas.id_tta = equipos.id_tta', 'left')
            ->select('transportistas.nacion as nacion_tta')
            ->get();
        if ($resultUnidades === false) {
            $error = $this->db->error();
            CLI::error('Error al leer unidades: ' . ($error['message'] ?? 'consulta fallida'));
            return 1;
        }
        $unidades = $resultUnidades->getResultArray();
        CLI::write('  ' . count($unidades) . ' unidades cargadas.', 'green');
        if ($this->verbose && count($unidades) > 0) {
            $sampleKeys = [];
            $n = 0;
            foreach ($unidades as $u) {
                $idTta   = (int) $u['id_tta'];
                $tractor = $this->normalizePatente($u['patente_tractor'] ?? '');
                $semi    = $this->normalizePatente($u['patente_semi_delantero'] ?? '');
                if ($semi === '' || $tractor === '') {
                    continue;
                }
                $sampleKeys[] = $this->matchSinTta ? ($tractor . '|' . $semi) : ($idTta . '|' . $tractor . '|' . $semi);
                if (++$n >= 10) {
                    break;
                }
            }
            CLI::write('  Muestra de claves en nuestras unidades: ' . implode(' ; ', $sampleKeys), 'light_gray');
        }

        $updated = 0;
        $nacionDiferencias = 0;

        foreach ($unidades as $u) {
            $idTta   = (int) $u['id_tta'];
            $tractor = $this->normalizePatente($u['patente_tractor'] ?? '');
            $semi    = $this->normalizePatente($u['patente_semi_delantero'] ?? '');
            if ($semi === '' || $tractor === '') {
                continue;
            }
            if ($this->matchSinTta) {
                $key = $tractor . '|' . $semi;
            } else {
                if ($idTta === 0) {
                    continue;
                }
                $key = $idTta . '|' . $tractor . '|' . $semi;
            }
            $equipo = $equiposByKey[$key] ?? null;

            if ($equipo === null) {
                continue;
            }

            if (! in_array('nacion', $excludeColumns, true) && $this->nacionDiferente((string) ($equipo['nacion'] ?? ''), (string) ($u['nacion_tta'] ?? ''))) {
                $nacionDiferencias++;
                CLI::write(sprintf(
                    '  Nación distinta: id_equipo=%s patente=%s | Camiones="%s" | Nuestra (transportista)="%s"',
                    $u['id_equipo'],
                    $u['patente_semi_delantero'],
                    $equipo['nacion'] ?? '',
                    $u['nacion_tta'] ?? ''
                ), 'yellow');
            }

            $data = [];
            if (! in_array('fecha_alta', $excludeColumns, true)) {
                $data['fecha_alta'] = $equipo['fecha_alta'] ?? null;
            }
            if (! in_array('modo_carga', $excludeColumns, true)) {
                $data['modo_carga'] = $equipo['modo_carga'] ?? null;
            }
            if (! in_array('tara_total', $excludeColumns, true)) {
                $data['tara_total'] = isset($equipo['tara_total']) ? (float) $equipo['tara_total'] : null;
            }
            if (! in_array('peso_maximo', $excludeColumns, true)) {
                $data['peso_maximo'] = isset($equipo['peso_maximo']) ? (float) $equipo['peso_maximo'] : null;
            }
            if (! in_array('tractor_tara', $excludeColumns, true) && isset($equipo['tractor_tara'])) {
                $data['tractor_tara'] = (float) $equipo['tractor_tara'];
            }
            if (! in_array('tractor_pbt', $excludeColumns, true) && isset($equipo['tractor_pbt'])) {
                $data['tractor_pbt'] = (float) $equipo['tractor_pbt'];
            }
            if (! in_array('semi_delantero_tara', $excludeColumns, true) && isset($equipo['semi_delantero_tara'])) {
                $data['semi_delantero_tara'] = (float) $equipo['semi_delantero_tara'];
            }
            if (! in_array('semi_delantera_pbt', $excludeColumns, true) && isset($equipo['semi_delantera_pbt'])) {
                $data['semi_delantera_pbt'] = (float) $equipo['semi_delantera_pbt'];
            }
            if (! in_array('patente_semi_trasero', $excludeColumns, true) && isset($equipo['patente_semi_trasero']) && (string) $equipo['patente_semi_trasero'] !== '') {
                $data['patente_semi_trasero'] = trim((string) $equipo['patente_semi_trasero']);
            }
            if (! in_array('semi_trasero_tara', $excludeColumns, true) && isset($equipo['semi_trasero_tara'])) {
                $data['semi_trasero_tara'] = (float) $equipo['semi_trasero_tara'];
            }
            if (! in_array('semi_trasero_pbt', $excludeColumns, true) && isset($equipo['semi_trasero_pbt'])) {
                $data['semi_trasero_pbt'] = (float) $equipo['semi_trasero_pbt'];
            }
            for ($i = 1; $i <= 10; $i++) {
                $key = 'cisterna_' . $i . '_capacidad';
                if (! in_array($key, $excludeColumns, true) && array_key_exists($key, $equipo)) {
                    $data[$key] = isset($equipo[$key]) ? (float) $equipo[$key] : null;
                }
            }
            if (! in_array('capacidad_total', $excludeColumns, true) && isset($equipo['capacidad_total'])) {
                $data['capacidad_total'] = (float) $equipo['capacidad_total'];
            }
            $checklistCampos = [
                'checklist_asfalto',
                'checklist_alcohol',
                'checklist_biodiesel',
                'checklist_comb_liv',
                'checklist_comb_pes',
                'checklist_solvente',
                'checklist_coke',
                'checklist_lubes_gra',
                'checklist_lubes_env',
                'checklist_glp',
            ];
            foreach ($checklistCampos as $campo) {
                if (! in_array($campo, $excludeColumns, true) && array_key_exists($campo, $equipo)) {
                    $data[$campo] = (int) $equipo[$campo];
                }
            }

            if (empty($data)) {
                continue;
            }
            if (! $this->dryRun) {
                $this->db->table('equipos')->where('id_equipo', $u['id_equipo'])->update($data);
            }
            $updated++;
        }

        CLI::write('Equipos actualizados (o que se actualizarían): ' . $updated . '.', 'green');
        if ($updated === 0 && count($unidades) > 0 && count($equiposByKey) > 0) {
            CLI::write('No hubo coincidencias. Probá con --verbose para ver muestras de claves de ambos lados.', 'yellow');
            CLI::write('Por defecto se empareja por patente tractor + semi. Si querés exigir también id_tta, usá --con-tta.', 'yellow');
        }
        if ($nacionDiferencias > 0) {
            CLI::write('Diferencias de nación (camiones vs nuestro transportista): ' . $nacionDiferencias . '. Se mantiene la nación del transportista en el reporte.', 'yellow');
        }

        return 0;
    }
}
