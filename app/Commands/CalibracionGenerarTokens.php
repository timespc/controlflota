<?php

declare(strict_types=1);

namespace App\Commands;

use App\Models\CalibracionModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Asigna token_publico a todas las calibraciones que no lo tengan.
 * Útil después de migrar datos del sistema viejo: cada calibración tendrá
 * URL pública (calibracion/ver/{token}) y podrá generarse el QR.
 */
class CalibracionGenerarTokens extends BaseCommand
{
    protected $group       = 'Calibración';
    protected $name        = 'calibracion:generar-tokens';
    protected $description = 'Genera token_publico para calibraciones que no lo tienen (QR / URL pública).';
    protected $usage       = 'calibracion:generar-tokens [options]';

    /** @var array<string, string> */
    protected $arguments = [];

    /** @var array<string, string> */
    protected $options = [
        '--dry-run' => 'Solo mostrar cuántas calibraciones se actualizarían, sin escribir.',
    ];

    public function run(array $params): int
    {
        $dryRun = (bool) CLI::getOption('dry-run');
        if ($dryRun) {
            CLI::write('Modo dry-run: no se modificará la base de datos.', 'yellow');
        }

        $model = model(CalibracionModel::class);
        $db    = $model->db;
        $table = $db->prefixTable('calibraciones');

        $rows = $db->query("SELECT id_calibracion FROM `{$table}` WHERE (token_publico IS NULL OR token_publico = '')")
            ->getResultArray();

        $total = count($rows);
        if ($total === 0) {
            CLI::write('No hay calibraciones sin token. Nada que hacer.', 'green');
            return 0;
        }

        CLI::write("Calibraciones sin token: {$total}", 'cyan');

        if ($dryRun) {
            CLI::write("Se asignaría token a {$total} calibraciones (dry-run).", 'green');
            return 0;
        }

        $actualizadas = 0;
        foreach ($rows as $row) {
            $id    = (int) $row['id_calibracion'];
            $token = CalibracionModel::generarToken();
            // Asegurar unicidad (colisión muy improbable con 32 hex)
            while ($model->where('token_publico', $token)->countAllResults() > 0) {
                $token = CalibracionModel::generarToken();
            }
            $model->update($id, ['token_publico' => $token]);
            $actualizadas++;
        }

        CLI::write("Listo: se asignó token_publico a {$actualizadas} calibraciones.", 'green');
        return 0;
    }
}
