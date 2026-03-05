<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Importa ítems censo desde la tabla "items" de la BD camiones a items_censo de montajes-campana.
 * Requiere conexión 'camiones' en Config\Database.
 * Tabla y columna en app/Config/Camiones.php (tableItems, columnMapItems).
 */
class ItemsCensoDesdeCamiones extends BaseCommand
{
    protected $group       = 'ItemsCenso';
    protected $name        = 'items-censo:importar-desde-camiones';
    protected $description = 'Importa ítems censo desde la tabla items de la BD camiones.';
    protected $usage       = 'items-censo:importar-desde-camiones [options]';

    /** @var array<string, string> */
    protected $options = [
        '--dry-run'  => 'Solo mostrar qué se haría, sin insertar.',
        '--verbose'  => 'Listar cada ítem a importar.',
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
        $tableItems   = $config->tableItems ?? 'items';
        $columnMapItems = $config->columnMapItems ?? [];
        $colItem = $columnMapItems['item'] ?? 'Item';

        $selectStr = $colItem === 'item' ? 'item' : $colItem . ' as item';

        if (! $this->camionesDb->tableExists($tableItems)) {
            CLI::error("La tabla \"{$tableItems}\" no existe en la BD camiones.");
            CLI::write('Configurá tableItems en app/Config/Camiones.php si usás otro nombre.', 'light_gray');
            return 1;
        }

        CLI::write("Cargando ítems desde camiones (tabla {$tableItems})...", 'cyan');
        try {
            $result = $this->camionesDb->table($tableItems)->select($selectStr)->get();
            if ($result === false) {
                $err = $this->camionesDb->error();
                CLI::error('Error al leer items en camiones: ' . ($err['message'] ?? 'consulta fallida'));
                CLI::write('SELECT: ' . $selectStr . '. Revisá columnMapItems en app/Config/Camiones.php.', 'light_gray');
                return 1;
            }
            $filas = $result->getResultArray();
        } catch (\Throwable $e) {
            CLI::error('Error al leer items en camiones: ' . $e->getMessage());
            CLI::write('SELECT: ' . $selectStr, 'light_gray');
            CLI::write('Revisá columnMapItems en app/Config/Camiones.php (ej. item => Item o Descripcion).', 'light_gray');
            return 1;
        }

        CLI::write('  ' . count($filas) . ' ítems en camiones.', 'green');

        $insertados = 0;
        $omitidos   = 0;

        foreach ($filas as $r) {
            $nombre = trim((string) ($r['item'] ?? ''));
            if ($nombre === '') {
                continue;
            }

            $existe = $this->db->table('items_censo')
                ->where('item', $nombre)
                ->countAllResults() > 0;
            if ($existe) {
                $omitidos++;
                if ($this->verbose) {
                    CLI::write("  Ya existe: {$nombre}", 'light_gray');
                }
                continue;
            }

            if (! $this->dryRun) {
                $now = date('Y-m-d H:i:s');
                $this->db->table('items_censo')->insert([
                    'item'       => $nombre,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
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
