<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Añade push_browser_activo: recibir notificaciones push del navegador (Notification API).
 */
class AddPushBrowserActivoToNotificacionConfig extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('notificacion_config')) {
            return;
        }
        if ($this->db->fieldExists('push_browser_activo', 'notificacion_config')) {
            return;
        }
        $this->forge->addColumn('notificacion_config', [
            'push_browser_activo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 1,
                'after'      => 'push_activo',
                'comment'    => '1 = mostrar notificación nativa del navegador, 0 = no',
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->tableExists('notificacion_config') && $this->db->fieldExists('push_browser_activo', 'notificacion_config')) {
            $this->forge->dropColumn('notificacion_config', 'push_browser_activo');
        }
    }
}
