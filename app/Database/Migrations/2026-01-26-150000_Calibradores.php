<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Calibradores extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_calibrador' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'calibrador' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
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
        $this->forge->addKey('id_calibrador', true);
        $this->forge->createTable('calibradores');
    }

    public function down()
    {
        $this->forge->dropTable('calibradores', true);
    }
}
