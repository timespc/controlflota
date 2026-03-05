<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ItemsCenso extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_item_censo' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'item' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'comment' => 'Descripción del ítem de censo'
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
        $this->forge->addKey('id_item_censo', true);
        $this->forge->createTable('items_censo');
    }

    public function down()
    {
        $this->forge->dropTable('items_censo', true);
    }
}
