<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Inspectores extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_inspector' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'inspector' => [
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
        $this->forge->addKey('id_inspector', true);
        $this->forge->createTable('inspectores');
    }

    public function down()
    {
        $this->forge->dropTable('inspectores', true);
    }
}
