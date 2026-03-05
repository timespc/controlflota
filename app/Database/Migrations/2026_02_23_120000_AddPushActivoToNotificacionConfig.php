<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Añade push_activo a notificacion_config: checkbox para recibir notificaciones en el sistema (campana, toast).
 */
class AddPushActivoToNotificacionConfig extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('notificacion_config')) {
            return;
        }
        if ($this->db->fieldExists('push_activo', 'notificacion_config')) {
            return;
        }
        $this->forge->addColumn('notificacion_config', [
            'push_activo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 1,
                'after'      => 'email_destino',
                'comment'    => '1 = mostrar badge/toast en el sistema, 0 = no',
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->tableExists('notificacion_config') && $this->db->fieldExists('push_activo', 'notificacion_config')) {
            $this->forge->dropColumn('notificacion_config', 'push_activo');
        }
    }
}
