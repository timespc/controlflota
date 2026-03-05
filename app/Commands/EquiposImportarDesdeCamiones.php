<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Importa equipos desde la BD camiones a montajes_campana.
 * Resuelve id_tta por nombre (ttas en camiones vs transportistas en montajes).
 * Inserta los que no existan (por patente_tractor + patente_semi_delantero);
 * opcionalmente actualiza los existentes.
 *
 * Requiere que los transportistas ya existan en montajes_campana (por nombre).
 * Usar después de migrate:olddb --only=transportistas y transportistas:sync-tipo-codigo-axion-desde-camiones.
 */
class EquiposImportarDesdeCamiones extends BaseCommand
{
    protected $group       = 'Equipos';
    protected $name        = 'equipos:importar-desde-camiones';
    protected $description = 'Importa equipos desde la BD camiones a montajes_campana (inserta faltantes, opcional update).';
    protected $usage       = 'equipos:importar-desde-camiones [options]';

    /** @var array<string, string> */
    protected $options = [
        '--dry-run'   => 'Solo mostrar qué se haría, sin escribir.',
        '--update'    => 'Actualizar también los equipos que ya existan (por defecto solo inserta faltantes).',
    ];

    private \CodeIgniter\Database\BaseConnection $db;
    private \CodeIgniter\Database\BaseConnection $camionesDb;
    private bool $dryRun = false;
    private bool $updateExisting = false;

    /** id_tta camiones -> id_tta montajes (por nombre). Si null, se construye en run(). */
    private ?array $idTtaCamionesToMontajes = null;

    private function normalizePatente(?string $s): string
    {
        if ($s === null || $s === '') {
            return '';
        }
        return strtoupper(str_replace(' ', '', trim($s)));
    }

    private function normalizarNombre(?string $s): string
    {
        if ($s === null || $s === '') {
            return '';
        }
        $s = trim($s);
        $s = mb_strtoupper($s, 'UTF-8');
        $s = (string) preg_replace('/\s+/u', ' ', $s);
        return $s;
    }

    /**
     * Construye el mapa id_tta camiones -> id_tta montajes por nombre.
     * Requiere que ttas (camiones) tenga IdTta y nombre; transportistas (montajes) id_tta y transportista.
     */
    public function buildIdTtaMap(): array
    {
        $config   = config('Camiones');
        $tableTtas = $config->tableTtas ?? 'ttas';
        $colNombre = $config->columnTtasNombre ?? 'Transportista';
        $colId     = $config->columnTtasId ?? 'IdTta';

        $ttas = $this->camionesDb->table($tableTtas)
            ->select($colId . ', ' . $colNombre)
            ->get()
            ->getResultArray();

        $ourTrans = $this->db->table('transportistas')->select('id_tta, transportista')->get()->getResultArray();
        $ourByNombre = [];
        foreach ($ourTrans as $t) {
            $nombreNorm = $this->normalizarNombre($t['transportista'] ?? '');
            if ($nombreNorm !== '') {
                $ourByNombre[$nombreNorm] = (int) $t['id_tta'];
            }
        }

        $map = [];
        foreach ($ttas as $r) {
            $idCamiones = (int) ($r[$colId] ?? 0);
            $nombre     = trim((string) ($r[$colNombre] ?? ''));
            if ($idCamiones === 0 || $nombre === '') {
                continue;
            }
            $nombreNorm = $this->normalizarNombre($nombre);
            $idMontajes = $ourByNombre[$nombreNorm] ?? null;
            if ($idMontajes !== null) {
                $map[$idCamiones] = $idMontajes;
            }
        }
        return $map;
    }

    /**
     * Convierte una fila de equipos (camiones) a array para insert en equipos (montajes).
     * Incluye id_tta ya mapeado.
     */
    private function equipoRowToData(array $equipo, int $idTtaMontajes, array $excludeColumns): array
    {
        $data = [
            'id_tta'                 => $idTtaMontajes,
            'patente_tractor'        => trim((string) ($equipo['tractor_patente'] ?? $equipo['patente_tractor'] ?? '')) ?: null,
            'patente_semi_delantero'  => trim((string) ($equipo['semi_delan_patente'] ?? $equipo['pat_semi'] ?? $equipo['PatSemi'] ?? '')),
            'bitren'                 => ! empty($equipo['patente_semi_trasero']) ? 'SI' : 'NO',
            'patente_semi_trasero'    => isset($equipo['patente_semi_trasero']) && (string) $equipo['patente_semi_trasero'] !== '' ? trim((string) $equipo['patente_semi_trasero']) : null,
            'fecha_alta'              => $equipo['fecha_alta'] ?? null,
            'modo_carga'             => $equipo['modo_carga'] ?? null,
            'tara_total'             => isset($equipo['tara_total']) ? (float) $equipo['tara_total'] : null,
            'peso_maximo'            => isset($equipo['peso_maximo']) ? (float) $equipo['peso_maximo'] : null,
            'tractor_tara'           => isset($equipo['tractor_tara']) ? (float) $equipo['tractor_tara'] : null,
            'tractor_pbt'            => isset($equipo['tractor_pbt']) ? (float) $equipo['tractor_pbt'] : null,
            'semi_delantero_tara'     => isset($equipo['semi_delantero_tara']) ? (float) $equipo['semi_delantero_tara'] : null,
            'semi_delantera_pbt'     => isset($equipo['semi_delantera_pbt']) ? (float) $equipo['semi_delantera_pbt'] : null,
            'semi_trasero_tara'      => isset($equipo['semi_trasero_tara']) ? (float) $equipo['semi_trasero_tara'] : null,
            'semi_trasero_pbt'       => isset($equipo['semi_trasero_pbt']) ? (float) $equipo['semi_trasero_pbt'] : null,
        ];
        for ($i = 1; $i <= 10; $i++) {
            $key = 'cisterna_' . $i . '_capacidad';
            if (! in_array($key, $excludeColumns, true)) {
                $data[$key] = isset($equipo[$key]) ? (float) $equipo[$key] : null;
            }
        }
        if (! in_array('capacidad_total', $excludeColumns, true)) {
            $data['capacidad_total'] = isset($equipo['capacidad_total']) ? (float) $equipo['capacidad_total'] : null;
        }
        $checklistCampos = [
            'checklist_asfalto', 'checklist_alcohol', 'checklist_biodiesel',
            'checklist_comb_liv', 'checklist_comb_pes', 'checklist_solvente',
            'checklist_coke', 'checklist_lubes_gra', 'checklist_lubes_env', 'checklist_glp',
        ];
        foreach ($checklistCampos as $campo) {
            if (! in_array($campo, $excludeColumns, true)) {
                $data[$campo] = isset($equipo[$campo]) ? (int) $equipo[$campo] : 0;
            }
        }
        $now = date('Y-m-d H:i:s');
        $data['created_at'] = $now;
        $data['updated_at'] = $now;
        return $data;
    }

    public function run(array $params): int
    {
        $this->dryRun         = (bool) CLI::getOption('dry-run');
        $this->updateExisting = (bool) CLI::getOption('update');

        if ($this->dryRun) {
            CLI::write('Modo dry-run: no se escribirá en la BD.', 'yellow');
        }

        try {
            $this->db         = \Config\Database::connect();
            $this->camionesDb = \Config\Database::connect('camiones');
        } catch (\Throwable $e) {
            CLI::error('Error conectando a las bases de datos: ' . $e->getMessage());
            return 1;
        }

        $config         = config('Camiones');
        $tableEquipos   = $config->tableEquipos ?? 'equipos';
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
            'tractor_tara' => 'tractor_tara',
            'tractor_pbt' => 'tractor_pbt',
            'semi_delantero_tara' => 'semi_delantero_tara',
            'semi_delantera_pbt' => 'semi_delantera_pbt',
            'patente_semi_trasero' => 'patente_semi_trasero',
            'semi_trasero_tara' => 'semi_trasero_tara',
            'semi_trasero_pbt' => 'semi_trasero_pbt',
            'capacidad_total' => 'capacidad_total',
        ];
        for ($i = 1; $i <= 10; $i++) {
            $cols['cisterna_' . $i . '_capacidad'] = 'cisterna_' . $i . '_capacidad';
        }
        $checklistCampos = [
            'checklist_asfalto', 'checklist_alcohol', 'checklist_biodiesel',
            'checklist_comb_liv', 'checklist_comb_pes', 'checklist_solvente',
            'checklist_coke', 'checklist_lubes_gra', 'checklist_lubes_env', 'checklist_glp',
        ];
        foreach ($checklistCampos as $c) {
            $cols[$c] = $c;
        }
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

        CLI::write('Construyendo mapa id_tta (camiones -> montajes por nombre)...', 'cyan');
        $this->idTtaCamionesToMontajes = $this->buildIdTtaMap();
        CLI::write('  ' . count($this->idTtaCamionesToMontajes) . ' transportistas emparejados.', 'green');

        CLI::write('Cargando equipos desde camiones...', 'cyan');
        $equiposRows = $this->camionesDb->table($tableEquipos)->select($selectStr)->get()->getResultArray();
        CLI::write('  ' . count($equiposRows) . ' filas.', 'green');

        $existentes = $this->db->table('equipos')
            ->select('id_equipo, patente_tractor, patente_semi_delantero')
            ->get()
            ->getResultArray();
        $existentesByKey = [];
        foreach ($existentes as $e) {
            $k = $this->normalizePatente($e['patente_tractor'] ?? '') . '|' . $this->normalizePatente($e['patente_semi_delantero'] ?? '');
            $existentesByKey[$k] = (int) $e['id_equipo'];
        }

        $inserted = 0;
        $updated  = 0;
        $skipped  = 0;

        foreach ($equiposRows as $r) {
            $idTtaCam = (int) ($r['id_tta'] ?? 0);
            $tractor   = $this->normalizePatente($r['tractor_patente'] ?? $r['patente_tractor'] ?? '');
            $semi      = $this->normalizePatente($r['semi_delan_patente'] ?? $r['pat_semi'] ?? $r['PatSemi'] ?? '');
            if ($semi === '' || $tractor === '') {
                $skipped++;
                continue;
            }
            $idTtaMontajes = $this->idTtaCamionesToMontajes[$idTtaCam] ?? null;
            if ($idTtaMontajes === null) {
                $skipped++;
                continue;
            }
            $key = $tractor . '|' . $semi;
            $idEquipoExistente = $existentesByKey[$key] ?? null;

            $data = $this->equipoRowToData($r, $idTtaMontajes, $excludeColumns);

            if ($idEquipoExistente === null) {
                if (! $this->dryRun) {
                    $this->db->table('equipos')->insert($data);
                    $existentesByKey[$key] = (int) $this->db->insertID();
                }
                $inserted++;
            } elseif ($this->updateExisting) {
                if (! $this->dryRun) {
                    $this->db->table('equipos')->where('id_equipo', $idEquipoExistente)->update($data);
                }
                $updated++;
            }
        }

        CLI::write('Insertados: ' . $inserted . '. Actualizados: ' . $updated . '. Omitidos (sin tta o ya existente): ' . $skipped . '.', 'green');
        return 0;
    }

    /**
     * Para uso desde MigrateFullTwoBasesToOne: ejecuta la importación con un mapa ya construido.
     * Si se pasa $idTtaMap, no se construye desde BD.
     */
    public function runWithMap(array $idTtaCamionesToMontajes, bool $dryRun, bool $updateExisting): array
    {
        $this->db         = \Config\Database::connect();
        $this->camionesDb = \Config\Database::connect('camiones');
        $this->idTtaCamionesToMontajes = $idTtaCamionesToMontajes;
        $this->dryRun = $dryRun;
        $this->updateExisting = $updateExisting;

        $config         = config('Camiones');
        $tableEquipos   = $config->tableEquipos ?? 'equipos';
        $columnMap     = $config->columnMap ?? [];
        $excludeColumns = $config->excludeColumns ?? [];

        $cols = [
            'id_tta' => 'id_tta', 'tractor_patente' => 'tractor_patente', 'semi_delan_patente' => 'semi_delan_patente',
            'fecha_alta' => 'fecha_alta', 'modo_carga' => 'modo_carga', 'tara_total' => 'tara_total', 'peso_maximo' => 'peso_maximo',
            'tractor_tara' => 'tractor_tara', 'tractor_pbt' => 'tractor_pbt', 'semi_delantero_tara' => 'semi_delantero_tara',
            'semi_delantera_pbt' => 'semi_delantera_pbt', 'patente_semi_trasero' => 'patente_semi_trasero',
            'semi_trasero_tara' => 'semi_trasero_tara', 'semi_trasero_pbt' => 'semi_trasero_pbt', 'capacidad_total' => 'capacidad_total',
        ];
        for ($i = 1; $i <= 10; $i++) {
            $cols['cisterna_' . $i . '_capacidad'] = 'cisterna_' . $i . '_capacidad';
        }
        foreach (['checklist_asfalto', 'checklist_alcohol', 'checklist_biodiesel', 'checklist_comb_liv', 'checklist_comb_pes', 'checklist_solvente', 'checklist_coke', 'checklist_lubes_gra', 'checklist_lubes_env', 'checklist_glp'] as $c) {
            $cols[$c] = $c;
        }
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

        $equiposRows = $this->camionesDb->table($tableEquipos)->select($selectStr)->get()->getResultArray();
        $existentes = $this->db->table('equipos')->select('id_equipo, patente_tractor, patente_semi_delantero')->get()->getResultArray();
        $existentesByKey = [];
        foreach ($existentes as $e) {
            $k = $this->normalizePatente($e['patente_tractor'] ?? '') . '|' . $this->normalizePatente($e['patente_semi_delantero'] ?? '');
            $existentesByKey[$k] = (int) $e['id_equipo'];
        }

        $inserted = 0;
        $updated  = 0;
        foreach ($equiposRows as $r) {
            $idTtaCam = (int) ($r['id_tta'] ?? 0);
            $tractor   = $this->normalizePatente($r['tractor_patente'] ?? $r['patente_tractor'] ?? '');
            $semi      = $this->normalizePatente($r['semi_delan_patente'] ?? $r['pat_semi'] ?? $r['PatSemi'] ?? '');
            if ($semi === '' || $tractor === '') {
                continue;
            }
            $idTtaMontajes = $this->idTtaCamionesToMontajes[$idTtaCam] ?? null;
            if ($idTtaMontajes === null) {
                continue;
            }
            $key = $tractor . '|' . $semi;
            $idExistente = $existentesByKey[$key] ?? null;
            $data = $this->equipoRowToData($r, $idTtaMontajes, $excludeColumns);
            if ($idExistente === null) {
                if (! $this->dryRun) {
                    $this->db->table('equipos')->insert($data);
                    $existentesByKey[$key] = (int) $this->db->insertID();
                }
                $inserted++;
            } elseif ($this->updateExisting && ! $this->dryRun) {
                $this->db->table('equipos')->where('id_equipo', $idExistente)->update($data);
                $updated++;
            }
        }
        return ['inserted' => $inserted, 'updated' => $updated];
    }
}
