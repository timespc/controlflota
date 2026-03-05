<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Calibraciones extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_calibracion' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'patente' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'comment' => 'Patente de la unidad calibrada'
            ],
            'id_unidad' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID de la unidad (opcional)'
            ],
            'fecha_calib' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Fecha de calibración'
            ],
            'vto_calib' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Fecha de vencimiento de la calibración'
            ],
            'id_calibrador' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID del calibrador'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addKey('id_calibracion', true);
        $this->forge->addKey('patente');
        $this->forge->addKey('vto_calib');
        $this->forge->addKey('id_calibrador');

        if ($this->db->tableExists('unidades')) {
            $this->forge->addForeignKey('id_unidad', 'unidades', 'id_unidad', 'SET NULL', 'CASCADE');
        }
        if ($this->db->tableExists('calibradores')) {
            $this->forge->addForeignKey('id_calibrador', 'calibradores', 'id_calibrador', 'SET NULL', 'CASCADE');
        }

        $this->forge->createTable('calibraciones', true);
    }

    public function down()
    {
        $this->forge->dropTable('calibraciones', true);
    }
}
