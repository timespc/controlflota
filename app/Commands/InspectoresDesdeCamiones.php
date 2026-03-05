<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Importa inspectores desde la BD camiones a la tabla inspectores de montajes-campana.
 * Requiere conexión 'camiones' en Config\Database.
 * Tabla y columna en app/Config/Camiones.php (tableInspectores, columnMapInspectores).
 */
class InspectoresDesdeCamiones extends BaseCommand
{
    protected $group       = 'Inspectores';
    protected $name        = 'inspectores:importar-desde-camiones';
    protected $description = 'Importa inspectores desde la BD camiones.';
    protected $usage       = 'inspectores:importar-desde-camiones [options]';

    /** @var array<string, string> */
    protected $options = [
        '--dry-run'  => 'Solo mostrar qué se haría, sin insertar.',
        '--verbose'  => 'Listar cada inspector a importar.',
    ];

    private \CodeIgniter\Database\BaseConnection $db;
    private \CodeIgniter\Database\BaseConnection $camionesDb;
    private bool $dryRun = false;
    private bool $verbose = false;

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
        $tableInspectores   = $config->tableInspectores ?? 'inspectores';
        $columnMapInspectores = $config->columnMapInspectores ?? [];
        $colInspector = $columnMapInspectores['inspector'] ?? 'inspector';

        $selectStr = $colInspector === 'inspector' ? 'inspector' : $colInspector . ' as inspector';

        if (! $this->camionesDb->tableExists($tableInspectores)) {
            CLI::error("La tabla \"{$tableInspectores}\" no existe en la BD camiones.");
            CLI::write('Configurá tableInspectores en app/Config/Camiones.php si usás otro nombre.', 'light_gray');
            return 1;
        }

        CLI::write("Cargando inspectores desde camiones (tabla {$tableInspectores})...", 'cyan');
        try {
            $result = $this->camionesDb->table($tableInspectores)->select($selectStr)->get();
            $filas  = $result->getResultArray();
        } catch (\Throwable $e) {
            CLI::error('Error al leer inspectores en camiones: ' . $e->getMessage());
            CLI::write('SELECT usado: ' . $selectStr, 'light_gray');
            CLI::write('Revisá columnMapInspectores en app/Config/Camiones.php.', 'light_gray');
            return 1;
        }

        CLI::write('  ' . count($filas) . ' inspectores en camiones.', 'green');

        $insertados = 0;
        $omitidos   = 0;

        foreach ($filas as $r) {
            $nombre = trim((string) ($r['inspector'] ?? ''));
            if ($nombre === '') {
                continue;
            }

            $existe = $this->db->table('inspectores')
                ->where('inspector', $nombre)
                ->countAllResults() > 0;
            if ($existe) {
                $omitidos++;
                if ($this->verbose) {
                    CLI::write("  Ya existe: {$nombre}", 'light_gray');
                }
                continue;
            }

            if (! $this->dryRun) {
                $this->db->table('inspectores')->insert(['inspector' => $nombre]);
            }
            $insertados++;
            if ($this->verbose) {
                CLI::write('  ' . ($this->dryRun ? 'Se insertaría: ' : 'Insertado: ') . $nombre, 'green');
            }
        }

        CLI::write('Insertados (o que se insertarían): ' . $insertados . '.', 'green');
        if ($omitidos > 0) {
            CLI::write('Omitidos por duplicado: ' . $omitidos . '.', 'light_gray');
        }
        if ($this->dryRun && $insertados > 0) {
            CLI::write('Ejecutá sin --dry-run para insertar en la BD.', 'yellow');
        }

        return 0;
    }
}
