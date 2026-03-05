<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Importa choferes desde la BD camiones a la tabla choferes de montajes-campana.
 * El mapeo tricky: id_tta de camiones no coincide con id_tta nuestra; se resuelve por
 * nombre del transportista (ttas en camiones vs transportistas en nuestra BD).
 * Nación se resuelve por nombre (naciones en nuestra BD).
 *
 * Requiere conexión 'camiones' en Config\Database.
 * Tabla y columnas en app/Config/Camiones.php (tableChoferes, columnMapChoferes, tableTtas, columnTtasNombre, columnTtasId).
 */
class ChoferesDesdeCamiones extends BaseCommand
{
    protected $group       = 'Choferes';
    protected $name        = 'choferes:importar-desde-camiones';
    protected $description = 'Importa choferes desde la BD camiones; mapea id transportista por nombre.';
    protected $usage       = 'choferes:importar-desde-camiones [options]';

    /** @var array<string, string> */
    protected $options = [
        '--dry-run'  => 'Solo mostrar qué se haría, sin insertar.',
        '--verbose'  => 'Listar cada chofer y el id_tta asignado (o "sin match").',
    ];

    private \CodeIgniter\Database\BaseConnection $db;
    private \CodeIgniter\Database\BaseConnection $camionesDb;
    private bool $dryRun = false;
    private bool $verbose = false;

    /** Mapeo old id_tta (camiones) -> new id_tta (nuestra BD) */
    private array $mapIdTta = [];

    /** Mapeo nombre nación (normalizado) -> id_nacion (nuestra BD) */
    private array $mapNacion = [];

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
            CLI::write('Modo dry-run: no se insertará en la BD.', 'yellow');
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
        $tableTtas   = $config->tableTtas ?? 'ttas';
        $colTtasId   = $config->columnTtasId ?? 'IdTta';
        $colTtasNombre = $config->columnTtasNombre ?? 'Transportista';
        $tableChoferes = $config->tableChoferes ?? 'choferes';
        $columnMapChoferes = $config->columnMapChoferes ?? [];

        // SELECT solo columnas definidas en columnMapChoferes (ej. DNI, Chofer, IdTta, IdNacChofer, ComenChofer)
        $selectChoferes = [];
        foreach ($columnMapChoferes as $ourCol => $camionesCol) {
            $selectChoferes[] = $camionesCol . ' as ' . $ourCol;
        }
        $selectChoferesStr = $selectChoferes !== [] ? implode(', ', $selectChoferes) : 'DNI as documento, Chofer as nombre, IdTta as id_tta, IdNacChofer as id_nacion, ComenChofer as comentarios';

        // 1) Cargar ttas de camiones: old id_tta -> nombre transportista
        if (! $this->camionesDb->tableExists($tableTtas)) {
            CLI::error("La tabla \"{$tableTtas}\" no existe en la BD camiones.");
            return 1;
        }
        CLI::write("Cargando ttas desde camiones (tabla {$tableTtas})...", 'cyan');
        $ttasCamiones = $this->camionesDb->table($tableTtas)
            ->select("{$colTtasId} as id_tta, {$colTtasNombre} as transportista")
            ->get()
            ->getResultArray();
        $oldTtaPorId = [];
        foreach ($ttasCamiones as $r) {
            $id = (int) ($r['id_tta'] ?? 0);
            $nombre = trim((string) ($r['transportista'] ?? ''));
            if ($id > 0 && $nombre !== '') {
                $oldTtaPorId[$id] = $nombre;
            }
        }
        CLI::write('  ' . count($oldTtaPorId) . ' ttas en camiones.', 'green');

        // 2) Cargar transportistas nuestra BD: nombre normalizado -> id_tta
        CLI::write('Cargando transportistas de montajes-campana...', 'cyan');
        $transportistas = $this->db->table('transportistas')->select('id_tta, transportista')->get()->getResultArray();
        $nuevoIdTtaPorNombre = [];
        foreach ($transportistas as $t) {
            $nombreNorm = $this->normalizarNombre($t['transportista'] ?? '');
            if ($nombreNorm !== '') {
                $nuevoIdTtaPorNombre[$nombreNorm] = (int) $t['id_tta'];
            }
        }
        CLI::write('  ' . count($transportistas) . ' transportistas.', 'green');

        // 3) Mapeo old id_tta -> new id_tta (por nombre)
        foreach ($oldTtaPorId as $oldId => $nombreTta) {
            $nombreNorm = $this->normalizarNombre($nombreTta);
            $this->mapIdTta[$oldId] = $nuevoIdTtaPorNombre[$nombreNorm] ?? null;
        }
        $conMatch = count(array_filter($this->mapIdTta));
        $sinMatch = count($this->mapIdTta) - $conMatch;
        CLI::write("Mapeo transportista: {$conMatch} con match, {$sinMatch} sin match en nuestra BD.", $sinMatch > 0 ? 'yellow' : 'green');
        if ($this->verbose && $sinMatch > 0) {
            foreach ($this->mapIdTta as $oldId => $newId) {
                if ($newId === null) {
                    CLI::write("  id_tta camiones={$oldId} \"{$oldTtaPorId[$oldId]}\" → sin match", 'light_gray');
                }
            }
        }

        // 4) Cargar naciones nuestra BD: nombre normalizado -> id_nacion
        CLI::write('Cargando naciones...', 'cyan');
        $naciones = $this->db->table('naciones')->select('id_nacion, nacion')->get()->getResultArray();
        foreach ($naciones as $n) {
            $nombreNorm = $this->normalizarNombre($n['nacion'] ?? '');
            if ($nombreNorm !== '') {
                $this->mapNacion[$nombreNorm] = (int) $n['id_nacion'];
            }
        }
        CLI::write('  ' . count($this->mapNacion) . ' naciones.', 'green');

        // 5) Cargar choferes de camiones
        if (! $this->camionesDb->tableExists($tableChoferes)) {
            CLI::error("La tabla \"{$tableChoferes}\" no existe en la BD camiones.");
            CLI::write('Configurá tableChoferes y columnMapChoferes en app/Config/Camiones.php si usan otro nombre.', 'light_gray');
            return 1;
        }
        CLI::write("Cargando choferes desde camiones (tabla {$tableChoferes})...", 'cyan');
        try {
            $resultChoferes = $this->camionesDb->table($tableChoferes)->select($selectChoferesStr)->get();
            if ($resultChoferes === false) {
                $err = $this->camionesDb->error();
                $msg = isset($err['message']) ? $err['message'] : 'Error desconocido al ejecutar la consulta';
                CLI::error('Error al leer choferes en camiones: ' . $msg);
                CLI::write('SELECT usado: ' . $selectChoferesStr, 'light_gray');
                CLI::write('Revisá que las columnas existan en la tabla choferes de camiones (DNI, Chofer, IdTta, IdNacChofer, ComenChofer). Ajustá columnMapChoferes en app/Config/Camiones.php.', 'light_gray');
                return 1;
            }
            $choferesCamiones = $resultChoferes->getResultArray();
        } catch (\Throwable $e) {
            CLI::error('Error al leer choferes en camiones: ' . $e->getMessage());
            CLI::write('SELECT usado: ' . $selectChoferesStr, 'light_gray');
            CLI::write('Revisá los nombres de columnas en app/Config/Camiones.php (columnMapChoferes) y compará con la tabla en la BD camiones.', 'light_gray');
            return 1;
        }
        CLI::write('  ' . count($choferesCamiones) . ' choferes en camiones.', 'green');

        // 6) Insertar en nuestra BD (o dry-run)
        $insertados = 0;
        $omitidosDuplicado = 0;
        $sinMatchTta = 0;

        foreach ($choferesCamiones as $c) {
            $documento = trim((string) ($c['documento'] ?? ''));
            $nombre    = trim((string) ($c['nombre'] ?? ''));
            $oldIdTta  = (int) ($c['id_tta'] ?? 0);
            $nacionNombre = trim((string) ($c['nacion'] ?? ''));
            $comentarios = trim((string) ($c['comentarios'] ?? ''));

            if ($documento === '' && $nombre === '') {
                continue;
            }

            $newIdTta = $oldIdTta > 0 ? ($this->mapIdTta[$oldIdTta] ?? null) : null;
            if ($oldIdTta > 0 && $newIdTta === null) {
                $sinMatchTta++;
            }

            $idNacion = null;
            if (isset($c['id_nacion']) && (is_numeric($c['id_nacion']) || $c['id_nacion'] !== '')) {
                $idNacion = (int) $c['id_nacion'];
            } elseif ($nacionNombre !== '') {
                $idNacion = $this->mapNacion[$this->normalizarNombre($nacionNombre)] ?? null;
            }

            // Evitar duplicado por (documento, nombre) si ya existe
            $existe = $this->db->table('choferes')
                ->where('documento', $documento)
                ->where('nombre', $nombre)
                ->countAllResults() > 0;
            if ($existe) {
                $omitidosDuplicado++;
                if ($this->verbose) {
                    CLI::write("  Ya existe: documento={$documento} nombre={$nombre}", 'light_gray');
                }
                continue;
            }

            $data = [
                'documento'   => $documento,
                'nombre'      => $nombre,
                'id_nacion'   => $idNacion,
                'id_tta'      => $newIdTta,
                'comentarios' => $comentarios !== '' ? $comentarios : null,
            ];

            if (! $this->dryRun) {
                $this->db->table('choferes')->insert($data);
            }
            $insertados++;
            if ($this->verbose) {
                $ttaInfo = $newIdTta !== null ? "id_tta={$newIdTta}" : 'sin TTA (camiones id_tta=' . $oldIdTta . ')';
                CLI::write("  " . ($this->dryRun ? 'Se insertaría: ' : 'Insertado: ') . "documento={$documento} nombre={$nombre} {$ttaInfo}", 'green');
            }
        }

        CLI::write('Insertados (o que se insertarían): ' . $insertados . '.', 'green');
        if ($sinMatchTta > 0) {
            CLI::write('Choferes con transportista sin match (insertados con id_tta null): ' . $sinMatchTta . '.', 'yellow');
        }
        if ($omitidosDuplicado > 0) {
            CLI::write('Omitidos por duplicado (documento+nombre): ' . $omitidosDuplicado . '.', 'light_gray');
        }
        if ($this->dryRun && $insertados > 0) {
            CLI::write('Ejecutá sin --dry-run para insertar en la BD.', 'yellow');
        }

        return 0;
    }
}
