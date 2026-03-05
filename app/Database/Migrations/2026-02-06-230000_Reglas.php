<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Reglas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_regla' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
                'null'           => false,
            ],
            'numero_regla' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'comment'    => 'Número de serie de la varilla de medición',
            ],
            'habilitada' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 1,
                'comment'    => '1 = habilitada (en uso), 0 = deshabilitada',
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
        $this->forge->addKey('id_regla', true);
        $this->forge->addKey('habilitada');
        $this->forge->createTable('reglas');
    }

    public function down()
    {
        $this->forge->dropTable('reglas', true);
    }
}
