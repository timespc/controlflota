<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Cubiertas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_cubierta' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'medida' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'comment' => 'Medida de la cubierta (ej: 295/80 x 22.5, 1100X20)'
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
        $this->forge->addKey('id_cubierta', true);
        $this->forge->createTable('cubiertas');
    }

    public function down()
    {
        $this->forge->dropTable('cubiertas', true);
    }
}
