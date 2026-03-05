<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Unidades extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_unidad' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false
            ],
            'patente' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'comment' => 'Patente de la unidad - OBLIGATORIO'
            ],
            'id_tta' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID del transportista'
            ],
            'tipo_unidad' => [
                'type' => 'ENUM',
                'constraint' => ['CHASIS', 'ACOPLADO', 'SEMI'],
                'null' => false,
                'comment' => 'Tipo de unidad - OBLIGATORIO'
            ],
            'patente_tractor' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'comment' => 'Patente del tractor asociado'
            ],
            'id_bandera' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID de la bandera'
            ],
            'id_marca' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID de la marca'
            ],
            'ano_fabricacion' => [
                'type' => 'INT',
                'constraint' => 4,
                'null' => true,
                'comment' => 'Año de fabricación'
            ],
            'tara_unidad' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
                'comment' => 'Tara de la unidad en Kgs'
            ],
            // Cubiertas del tractor
            'cubierta_tractor_eje1' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID de cubierta del tractor eje 1'
            ],
            'cubierta_tractor_eje2' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID de cubierta del tractor eje 2'
            ],
            'cubierta_tractor_eje3' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID de cubierta del tractor eje 3'
            ],
            'ejes_tractor' => [
                'type' => 'INT',
                'constraint' => 2,
                'null' => true,
                'default' => 0,
                'comment' => 'Número de ejes del tractor'
            ],
            // Cubiertas de la unidad
            'cubierta_unidad_eje1' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID de cubierta de la unidad eje 1'
            ],
            'cubierta_unidad_eje2' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID de cubierta de la unidad eje 2'
            ],
            'cubierta_unidad_eje3' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID de cubierta de la unidad eje 3'
            ],
            'ejes_unidad' => [
                'type' => 'INT',
                'constraint' => 2,
                'null' => true,
                'default' => 0,
                'comment' => 'Número de ejes de la unidad'
            ],
            'cota_delantero' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'Cota delantera en mm'
            ],
            'cota_trasero' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'Cota trasera en mm'
            ],
            'observaciones' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Observaciones'
            ],
            'id_equipo' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'ID del equipo'
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
        
        $this->forge->addKey('id_unidad', true);
        $this->forge->addKey('id_tta');
        $this->forge->addKey('id_bandera');
        $this->forge->addKey('id_marca');
        $this->forge->addKey('id_equipo');
        $this->forge->addKey('patente');
        
        // Foreign keys (si las tablas existen)
        if ($this->db->tableExists('transportistas')) {
            $this->forge->addForeignKey('id_tta', 'transportistas', 'id_tta', 'SET NULL', 'CASCADE');
        }
        if ($this->db->tableExists('equipos')) {
            $this->forge->addForeignKey('id_equipo', 'equipos', 'id_equipo', 'SET NULL', 'CASCADE');
        }
        
        $this->forge->createTable('unidades', true);
    }

    public function down()
    {
        $this->forge->dropTable('unidades', true);
    }
}
