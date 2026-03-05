<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Contrasta la tabla ttas de la BD camiones con transportistas de montajes_campana.
 * - Si el transportista existe (por nombre normalizado): actualiza tipo y codigo_axion.
 * - Si no existe: lo crea trayendo todos los datos de ttas según columnMapTtasToTransportistas (y resuelve nacion/pais_id).
 *
 * Requiere conexión 'camiones' en Config\Database.
 * Columnas y mapeo en app/Config/Camiones.php: tableTtas, columnMapTtasToTransportistas.
 */
class TransportistasSyncTipoCodigoAxionDesdeCamiones extends BaseCommand
{
    protected $group       = 'Transportistas';
    protected $name        = 'transportistas:sync-tipo-codigo-axion-desde-camiones';
    protected $description = 'Sincroniza tipo y codigo_axion desde ttas (camiones); crea transportistas faltantes.';
    protected $usage       = 'transportistas:sync-tipo-codigo-axion-desde-camiones [options]';

    /** @var array<string, string> */
    protected $options = [
        '--dry-run'  => 'Solo mostrar qué se haría, sin modificar la BD.',
        '--verbose'  => 'Listar cada registro actualizado o creado.',
    ];

    private \CodeIgniter\Database\BaseConnection $db;
    private \CodeIgniter\Database\BaseConnection $camionesDb;
    private bool $dryRun = false;
    private bool $verbose = false;

    /** Mapeo nombre nación (normalizado) -> id_nacion (nuestra BD). Para columnas que vienen como nombre. */
    private array $mapNacion = [];

    /** Mapeo id_nacion -> nacion (texto) para completar campo nacion al crear con IdNacTta. */
    private array $mapNacionIdToNombre = [];

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

    public function run(array $params): int
    {
        $this->dryRun  = (bool) CLI::getOption('dry-run');
        $this->verbose = (bool) CLI::getOption('verbose');

        if ($this->dryRun) {
            CLI::write('Modo dry-run: no se modificará la BD.', 'yellow');
        }

        try {
            $this->db         = \Config\Database::connect();
            $this->camionesDb = \Config\Database::connect('camiones');
        } catch (\Throwable $e) {
            CLI::error('Error conectando a las bases de datos: ' . $e->getMessage());
            CLI::write('Verificá que la conexión "camiones" exista en app/Config/Database.php.', 'light_gray');
            return 1;
        }

        $config = config('Camiones');
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
            $selectParts = [
                $colNombre . ' as transportista',
                $colTipo . ' as tipo',
                $colCodAxion . ' as codigo_axion',
            ];
            if ($colNacion !== '') {
                $selectParts[] = $colNacion . ' as pais_id';
            }
        }
        $selectTtas = implode(', ', $selectParts);

        if (! $this->camionesDb->tableExists($tableTtas)) {
            CLI::error("La tabla \"{$tableTtas}\" no existe en la BD camiones.");
            return 1;
        }

        CLI::write("Cargando ttas desde camiones (tabla {$tableTtas})...", 'cyan');
        try {
            $resultTtas = $this->camionesDb->table($tableTtas)->select($selectTtas)->get();
            if ($resultTtas === false) {
                $err = $this->camionesDb->error();
                $msg = isset($err['message']) ? $err['message'] : 'Error desconocido al ejecutar la consulta';
                CLI::error('Error al leer ttas en camiones: ' . $msg);
                CLI::write('SELECT: ' . $selectTtas, 'light_gray');
                CLI::write('Revisá que las columnas en columnMapTtasToTransportistas existan en ttas. Ajustá app/Config/Camiones.php.', 'light_gray');
                return 1;
            }
            $rowsTtas = $resultTtas->getResultArray();
        } catch (\Throwable $e) {
            CLI::error('Error al leer ttas en camiones: ' . $e->getMessage());
            CLI::write('SELECT: ' . $selectTtas, 'light_gray');
            CLI::write('Revisá columnTtasTipo, columnTtasCodAxion (y columnTtasNacion) en app/Config/Camiones.php.', 'light_gray');
            return 1;
        }
        CLI::write('  ' . count($rowsTtas) . ' filas en ttas.', 'green');

        CLI::write('Cargando transportistas de montajes_campana...', 'cyan');
        $ourTrans = $this->db->table('transportistas')->select('id_tta, transportista')->get()->getResultArray();
        $ourByNombre = [];
        foreach ($ourTrans as $t) {
            $nombreNorm = $this->normalizarNombre($t['transportista'] ?? '');
            if ($nombreNorm !== '') {
                $ourByNombre[$nombreNorm] = (int) $t['id_tta'];
            }
        }
        CLI::write('  ' . count($ourByNombre) . ' transportistas indexados por nombre.', 'green');

        if ($colNacion !== '' || isset($mapTtas['pais_id'])) {
            CLI::write('Cargando naciones (para pais_id y nombre al crear)...', 'cyan');
            $naciones = $this->db->table('naciones')->select('id_nacion, nacion')->get()->getResultArray();
            foreach ($naciones as $n) {
                $id = (int) $n['id_nacion'];
                $nombre = trim((string) ($n['nacion'] ?? ''));
                $this->mapNacionIdToNombre[$id] = $nombre;
                $nombreNorm = $this->normalizarNombre($nombre);
                if ($nombreNorm !== '') {
                    $this->mapNacion[$nombreNorm] = $id;
                }
            }
            CLI::write('  ' . count($this->mapNacionIdToNombre) . ' naciones.', 'green');
        }

        $actualizados = 0;
        $creados      = 0;

        $allowedInsert = ['transportista', 'cuit', 'tipo', 'codigo_axion', 'direccion', 'localidad', 'codigo_postal', 'provincia', 'nacion', 'pais_id', 'mail_contacto', 'telefono', 'comentarios'];

        foreach ($rowsTtas as $r) {
            $nombre = trim((string) ($r['transportista'] ?? ''));
            $tipo   = trim((string) ($r['tipo'] ?? ''));
            $codigoAxion = trim((string) ($r['codigo_axion'] ?? ''));

            if ($nombre === '') {
                continue;
            }

            $nombreNorm = $this->normalizarNombre($nombre);
            $ourId = $ourByNombre[$nombreNorm] ?? null;

            if ($ourId !== null) {
                $dataUpdate = [
                    'tipo'         => $tipo !== '' ? $tipo : null,
                    'codigo_axion' => $codigoAxion !== '' ? $codigoAxion : null,
                ];
                if (! $this->dryRun) {
                    $this->db->table('transportistas')->where('id_tta', $ourId)->update($dataUpdate);
                }
                $actualizados++;
                if ($this->verbose) {
                    CLI::write('  Actualizado id_tta=' . $ourId . ' "' . $nombre . '": tipo=' . ($tipo ?: '—') . ', codigo_axion=' . ($codigoAxion ?: '—'), 'green');
                }
                continue;
            }

            $dataInsert = [];
            foreach ($allowedInsert as $key) {
                if ($key === 'nacion') {
                    continue;
                }
                if (! array_key_exists($key, $r)) {
                    continue;
                }
                $val = $r[$key];
                if ($key === 'pais_id') {
                    $idPais = is_numeric($val) ? (int) $val : null;
                    $dataInsert['pais_id'] = $idPais;
                    $dataInsert['nacion'] = $idPais !== null ? ($this->mapNacionIdToNombre[$idPais] ?? null) : null;
                } elseif (is_string($val)) {
                    $val = trim($val);
                    $dataInsert[$key] = $val !== '' ? $val : null;
                } else {
                    $dataInsert[$key] = $val;
                }
            }
            if (! isset($dataInsert['transportista'])) {
                $dataInsert['transportista'] = $nombre;
            }
            if (! isset($dataInsert['tipo'])) {
                $dataInsert['tipo'] = $tipo !== '' ? $tipo : null;
            }
            if (! isset($dataInsert['codigo_axion'])) {
                $dataInsert['codigo_axion'] = $codigoAxion !== '' ? $codigoAxion : null;
            }
            if (isset($dataInsert['pais_id']) && ! array_key_exists('nacion', $dataInsert)) {
                $dataInsert['nacion'] = $this->mapNacionIdToNombre[(int) $dataInsert['pais_id']] ?? null;
            }

            if (! $this->dryRun) {
                $this->db->table('transportistas')->insert($dataInsert);
                $ourByNombre[$nombreNorm] = (int) $this->db->insertID();
            }
            $creados++;
            if ($this->verbose) {
                $extra = isset($dataInsert['cuit']) && $dataInsert['cuit'] ? ' cuit=' . $dataInsert['cuit'] : '';
                CLI::write('  ' . ($this->dryRun ? 'Se crearía: ' : 'Creado: ') . '"' . $nombre . '" tipo=' . $tipo . ' codigo_axion=' . $codigoAxion . $extra, 'cyan');
            }
        }

        CLI::write('Actualizados (tipo/codigo_axion): ' . $actualizados . '.', 'green');
        CLI::write('Creados (nuevos): ' . $creados . '.', 'green');
        if ($this->dryRun && ($actualizados > 0 || $creados > 0)) {
            CLI::write('Ejecutá sin --dry-run para aplicar cambios.', 'yellow');
        }

        return 0;
    }
}
