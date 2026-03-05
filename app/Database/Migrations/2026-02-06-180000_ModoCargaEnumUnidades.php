<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Cambia modo_carga en unidades de VARCHAR a ENUM con los valores
 * de la BD equipos (camiones): BOTTOM, ENVASADO, GRANEL, TOP, FOB. Nullable.
 */
class ModoCargaEnumUnidades extends Migration
{
    private const ENUM_VALUES = "ENUM('BOTTOM','ENVASADO','GRANEL','TOP','FOB')";

    public function up()
    {
        $sql = "ALTER TABLE unidades MODIFY modo_carga " . self::ENUM_VALUES . " NULL DEFAULT NULL";
        $this->db->query($sql);
    }

    public function down()
    {
        $this->forge->modifyColumn('unidades', [
            'modo_carga' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
        ]);
    }
}
