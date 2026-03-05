<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Naciones extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_nacion' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'nacion' => [
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
        $this->forge->addKey('id_nacion', true);
        $this->forge->createTable('naciones');
    }

    public function down()
    {
        $this->forge->dropTable('naciones', true);
    }
}
