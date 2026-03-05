<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Paises extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'nombre' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
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
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('nombre');
        $this->forge->createTable('paises');
        
        // Insertar países de Sudamérica
        $paises = [
            ['nombre' => 'Argentina'],
            ['nombre' => 'Bolivia'],
            ['nombre' => 'Brasil'],
            ['nombre' => 'Chile'],
            ['nombre' => 'Colombia'],
            ['nombre' => 'Ecuador'],
            ['nombre' => 'Paraguay'],
            ['nombre' => 'Perú'],
            ['nombre' => 'Uruguay'],
            ['nombre' => 'Venezuela']
        ];
        
        $this->db->table('paises')->insertBatch($paises);
    }

    public function down()
    {
        $this->forge->dropTable('paises');
    }
}

