<?php

declare(strict_types=1);

namespace App\Commands;

use App\Models\CalibracionModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Marca como recalibradas las calibraciones vencidas que ya tienen una calibración
 * posterior vigente para la misma patente (id_calibracion_reemplazo).
 * Útil para backfill después de añadir el campo o migrar datos.
 */
class CalibracionMarcarRecalibradas extends BaseCommand
{
    protected $group       = 'Calibración';
    protected $name        = 'calibracion:marcar-recalibradas';
    protected $description = 'Marca vencidas como recalibradas si existe una calibración posterior vigente para la misma patente.';
    protected $usage       = 'calibracion:marcar-recalibradas [--dry-run]';

    /** @var array<string, string> */
    protected $arguments = [];

    /** @var array<string, string> */
    protected $options = [
        '--dry-run' => 'Solo mostrar cuántas se marcarían, sin escribir.',
    ];

    public function run(array $params): int
    {
        $dryRun = (bool) CLI::getOption('dry-run');
        if ($dryRun) {
            CLI::write('Modo dry-run: no se modificará la base de datos.', 'yellow');
        }

        $model = model(CalibracionModel::class);
        $hoy   = date('Y-m-d');

        $vencidas = $model->where('vto_calib <', $hoy)
            ->where('id_calibracion_reemplazo', null)
            ->orderBy('id_calibracion', 'ASC')
            ->findAll();

        $marcadas = 0;
        foreach ($vencidas as $cal) {
            $idVencida = (int) $cal['id_calibracion'];
            $patente   = $cal['patente'] ?? '';
            if ($patente === '') {
                continue;
            }
            $siguiente = $model->where('patente', $patente)
                ->where('id_calibracion >', $idVencida)
                ->where('vto_calib >=', $hoy)
                ->orderBy('id_calibracion', 'ASC')
                ->first();
            if ($siguiente === null) {
                continue;
            }
            $idReemplazo = (int) $siguiente['id_calibracion'];
            if (! $dryRun) {
                $model->update($idVencida, ['id_calibracion_reemplazo' => $idReemplazo]);
            }
            $marcadas++;
        }

        if ($dryRun) {
            CLI::write("Se marcarían como recalibradas {$marcadas} calibraciones (dry-run).", 'green');
        } else {
            CLI::write("Listo: se marcaron como recalibradas {$marcadas} calibraciones.", 'green');
        }
        return 0;
    }
}
