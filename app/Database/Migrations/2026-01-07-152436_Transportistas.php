<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Transportistas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_tta' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'transportista' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'comment' => 'Nombre del transportista (OBLIGATORIO)'
            ],
            'direccion' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'localidad' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'codigo_postal' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true
            ],
            'provincia' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'nacion' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true
            ],
            'pais_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'provincia_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'mail_contacto' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Puede contener múltiples direcciones separadas por ";"'
            ],
            'telefono' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ],
            'comentarios' => [
                'type' => 'TEXT',
                'null' => true
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
        
        $this->forge->addKey('id_tta', true);
        $this->forge->addKey('transportista');
        $this->forge->addKey('localidad');
        $this->forge->addKey('provincia');
        $this->forge->createTable('transportistas');
    }

    public function down()
    {
        $this->forge->dropTable('transportistas');
    }
}

