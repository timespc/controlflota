<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Banderas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_bandera' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'bandera' => [
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
        $this->forge->addKey('id_bandera', true);
        $this->forge->createTable('banderas');
    }

    public function down()
    {
        $this->forge->dropTable('banderas', true);
    }
}
