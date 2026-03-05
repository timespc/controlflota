<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabla equivalente a cisternas_multi del sistema viejo:
 * detalle de multiflecha por cisterna (compartimientos internos de la cisterna N° X).
 * Una fila por (id_calibracion, numero_linea, numero_multiflecha).
 */
class CalibracionMultiflecha extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('calibracion_multiflecha')) {
            return;
        }

        $this->forge->addField([
            'id_calibracion' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false
            ],
            'numero_linea' => [
                'type'       => 'INT',
                'constraint' => 3,
                'null'       => false,
                'comment'    => 'Cisterna N° (1, 2, 3...)'
            ],
            'numero_multiflecha' => [
                'type'       => 'INT',
                'constraint' => 3,
                'null'       => false,
                'comment'    => 'N° de compartimiento multiflecha dentro de la cisterna'
            ],
            'capacidad' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
                'default'    => 0
            ],
            'enrase' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
                'default'    => 0
            ],
            'referen' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
                'default'    => 0
            ],
            'vacio_calc' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true
            ],
            'vacio_lts' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true
            ],
            'precinto_campana' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true
            ],
            'precinto_soporte' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true
            ],
            'precinto_hombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true]
        ]);

        $this->forge->addPrimaryKey(['id_calibracion', 'numero_linea', 'numero_multiflecha']);
        $this->forge->addKey('id_calibracion');
        $this->forge->addForeignKey('id_calibracion', 'calibraciones', 'id_calibracion', 'CASCADE', 'CASCADE');
        $this->forge->createTable('calibracion_multiflecha', true);
    }

    public function down()
    {
        $this->forge->dropTable('calibracion_multiflecha', true);
    }
}
