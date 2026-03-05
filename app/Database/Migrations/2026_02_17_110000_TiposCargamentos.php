<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabla tipos_cargamentos (solo consulta e impresión).
 * Campos: id, tipo, tipo_carga_abreviado, created_at, updated_at.
 */
class TiposCargamentos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tipo' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'tipo_carga_abreviado' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tipos_cargamentos');

        // Datos iniciales (ejemplos del sistema anterior)
        $this->db->table('tipos_cargamentos')->insertBatch([
            ['tipo' => 'ASFALTO', 'tipo_carga_abreviado' => 'ASF', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['tipo' => 'ALCOHOL', 'tipo_carga_abreviado' => 'ALC', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['tipo' => 'GLP', 'tipo_carga_abreviado' => 'GLP', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['tipo' => 'NAFTA', 'tipo_carga_abreviado' => 'NAF', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['tipo' => 'GAS OIL', 'tipo_carga_abreviado' => 'GO', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['tipo' => 'KEROSENE', 'tipo_carga_abreviado' => 'KER', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['tipo' => 'DIESEL', 'tipo_carga_abreviado' => 'DIE', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['tipo' => 'BIOCOMBUSTIBLE', 'tipo_carga_abreviado' => 'BIO', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['tipo' => 'OTROS', 'tipo_carga_abreviado' => 'OTR', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('tipos_cargamentos', true);
    }
}
