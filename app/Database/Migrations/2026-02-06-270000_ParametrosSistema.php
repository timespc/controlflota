<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Parámetros globales del sistema (dashboard, KPI, etc.).
 */
class ParametrosSistema extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'clave' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => false,
            ],
            'valor' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('clave', true);
        $this->forge->createTable('parametros_sistema', true);

        $this->db->table('parametros_sistema')->insert([
            'clave' => 'meses_vencida_max_kpi',
            'valor' => '24',
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('parametros_sistema', true);
    }
}
