<?php

declare(strict_types=1);

namespace App\Commands;

use App\Models\NotificacionModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Elimina todas las notificaciones de tipo "Calibraciones por vencer" (vencimiento).
 * Útil para limpiar notificaciones antiguas de vencimiento cuando se necesite.
 */
class NotificacionesEliminarVencimientos extends BaseCommand
{
    protected $group       = 'Notificaciones';
    protected $name        = 'notificaciones:eliminar-vencimientos';
    protected $description = 'Elimina todas las notificaciones de tipo calibraciones por vencer.';
    protected $usage       = 'notificaciones:eliminar-vencimientos [--dry-run]';

    /** @var array<string, string> */
    protected $arguments = [];

    /** @var array<string, string> */
    protected $options = [
        '--dry-run' => 'Solo mostrar cuántas se eliminarían, sin borrar.',
    ];

    public function run(array $params): int
    {
        $dryRun = (bool) CLI::getOption('dry-run');
        if ($dryRun) {
            CLI::write('Modo dry-run: no se modificará la base de datos.', 'yellow');
        }

        $model = model(NotificacionModel::class);
        $db    = $model->db;
        $prefix = $db->getPrefix();
        $tblNotif = $prefix . 'notificaciones';
        $tblLeida = $prefix . 'notificacion_leida';
        $tblRecordatorio = $prefix . 'notificacion_recordatorio';

        $ids = $db->table('notificaciones')
            ->select('id_notificacion')
            ->where('tipo', NotificacionModel::TIPO_CALIBRACION_POR_VENCER)
            ->get()
            ->getResultArray();

        $idsNotif = array_column($ids, 'id_notificacion');
        $total = count($idsNotif);

        if ($total === 0) {
            CLI::write('No hay notificaciones de vencimiento para eliminar.', 'green');
            return 0;
        }

        if ($dryRun) {
            CLI::write("Se eliminarían {$total} notificación(es) de calibraciones por vencer (dry-run).", 'green');
            return 0;
        }

        $placeholders = implode(',', array_fill(0, $total, '?'));
        if ($db->tableExists('notificacion_leida')) {
            $db->query("DELETE FROM `{$tblLeida}` WHERE id_notificacion IN ({$placeholders})", $idsNotif);
        }
        if ($db->tableExists('notificacion_recordatorio')) {
            $db->query("DELETE FROM `{$tblRecordatorio}` WHERE id_notificacion IN ({$placeholders})", $idsNotif);
        }
        $db->query("DELETE FROM `{$tblNotif}` WHERE tipo = ?", [NotificacionModel::TIPO_CALIBRACION_POR_VENCER]);

        CLI::write("Listo: se eliminaron {$total} notificación(es) de calibraciones por vencer.", 'green');
        return 0;
    }
}
