<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Elimina columnas whatsapp_activo y whatsapp_numero de notificacion_config (WhatsApp no implementado).
 */
class DropWhatsAppFromNotificacionConfig extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('notificacion_config')) {
            return;
        }
        if ($this->db->fieldExists('whatsapp_activo', 'notificacion_config')) {
            $this->forge->dropColumn('notificacion_config', 'whatsapp_activo');
        }
        if ($this->db->fieldExists('whatsapp_numero', 'notificacion_config')) {
            $this->forge->dropColumn('notificacion_config', 'whatsapp_numero');
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('notificacion_config')) {
            return;
        }
        $fields = [];
        if (! $this->db->fieldExists('whatsapp_activo', 'notificacion_config')) {
            $fields['whatsapp_activo'] = [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
                'default' => 0,
            ];
        }
        if (! $this->db->fieldExists('whatsapp_numero', 'notificacion_config')) {
            $fields['whatsapp_numero'] = [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ];
        }
        if ($fields !== []) {
            $this->forge->addColumn('notificacion_config', $fields);
        }
    }
}
