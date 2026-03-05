<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MarcasSensor extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_marca_sensor' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
                'null'           => false,
            ],
            'marca' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
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
        $this->forge->addKey('id_marca_sensor', true);
        $this->forge->createTable('marcas_sensor');
    }

    public function down()
    {
        $this->forge->dropTable('marcas_sensor', true);
    }
}
