<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Añade días de aviso para vencimiento de calibraciones (configurable por admin).
 */
class AddDiasAvisoVencimiento extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('notificacion_config')) {
            return;
        }
        if ($this->db->fieldExists('dias_aviso_vencimiento', 'notificacion_config')) {
            return;
        }
        $this->forge->addColumn('notificacion_config', [
            'dias_aviso_vencimiento' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'      => true,
                'default'   => 30,
                'comment'   => 'Avisar cuando una calibración venza en los próximos X días (0 = no aviso)',
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->tableExists('notificacion_config') && $this->db->fieldExists('dias_aviso_vencimiento', 'notificacion_config')) {
            $this->forge->dropColumn('notificacion_config', 'dias_aviso_vencimiento');
        }
    }
}
