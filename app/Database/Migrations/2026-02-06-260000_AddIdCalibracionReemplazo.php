<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdCalibracionReemplazo extends Migration
{
    public function up()
    {
        $this->forge->addColumn('calibraciones', [
            'id_calibracion_reemplazo' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'vto_calib',
                'comment'    => 'ID de la calibración que reemplazó a esta (recalibrada); si no es null, no cuenta como vencida'
            ]
        ]);

        $t = $this->db->prefixTable('calibraciones');
        $this->db->query("ALTER TABLE `{$t}` ADD CONSTRAINT `fk_calibracion_reemplazo` FOREIGN KEY (`id_calibracion_reemplazo`) REFERENCES `{$t}`(`id_calibracion`) ON DELETE SET NULL ON UPDATE CASCADE");
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `' . $this->db->prefixTable('calibraciones') . '` DROP FOREIGN KEY `fk_calibracion_reemplazo`');
        $this->forge->dropColumn('calibraciones', 'id_calibracion_reemplazo');
    }
}
