<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Equipos extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_equipo' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
                'comment' => 'ID del equipo (puede ser alfanumérico)'
            ],
            'id_tta' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
                'comment' => 'ID del transportista'
            ],
            'bitren' => [
                'type' => 'ENUM',
                'constraint' => ['SI', 'NO'],
                'default' => 'NO',
                'null' => true
            ],
            'transporte' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true
            ],
            'fecha_alta' => [
                'type' => 'DATE',
                'null' => true
            ],
            'modo_carga' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
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
            // Tractor Chasis
            'tractor_patente' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true
            ],
            'tractor_anio_modelo' => [
                'type' => 'INT',
                'constraint' => 4,
                'null' => true
            ],
            'tractor_tara' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
                'comment' => 'Tara en kilogramos'
            ],
            'tractor_pbt' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true,
                'comment' => 'PBT en kilogramos'
            ],
            'tractor_ejes' => [
                'type' => 'INT',
                'constraint' => 2,
                'null' => true,
                'default' => 0
            ],
            // Semi Delan/Acop
            'semi_delan_patente' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true
            ],
            'semi_delan_anio_modelo' => [
                'type' => 'INT',
                'constraint' => 4,
                'null' => true
            ],
            'semi_delan_tara' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true
            ],
            'semi_delan_pbt' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true
            ],
            'semi_delan_ejes' => [
                'type' => 'INT',
                'constraint' => 2,
                'null' => true,
                'default' => 0
            ],
            // Semi Trasero
            'semi_trasero_patente' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true
            ],
            'semi_trasero_anio_modelo' => [
                'type' => 'INT',
                'constraint' => 4,
                'null' => true
            ],
            'semi_trasero_tara' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true
            ],
            'semi_trasero_pbt' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true
            ],
            'semi_trasero_ejes' => [
                'type' => 'INT',
                'constraint' => 2,
                'null' => true,
                'default' => 0
            ],
            // Totales
            'tara_total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true
            ],
            'peso_maximo' => [
                'type' => 'DECIMAL',
                'constraint' => '10,3',
                'null' => true
            ],
            // Cisternas (10 cisternas)
            'cisterna_1_capacidad' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0],
            'cisterna_2_capacidad' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0],
            'cisterna_3_capacidad' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0],
            'cisterna_4_capacidad' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0],
            'cisterna_5_capacidad' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0],
            'cisterna_6_capacidad' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0],
            'cisterna_7_capacidad' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0],
            'cisterna_8_capacidad' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0],
            'cisterna_9_capacidad' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0],
            'cisterna_10_capacidad' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0],
            'capacidad_total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => 0
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
        
        $this->forge->addKey('id_equipo', true);
        $this->forge->addKey('id_tta');
        $this->forge->addKey('tractor_patente');
        $this->forge->addKey('semi_delan_patente');
        $this->forge->addKey('semi_trasero_patente');
        $this->forge->addForeignKey('id_tta', 'transportistas', 'id_tta', 'RESTRICT', 'RESTRICT');
        $this->forge->addForeignKey('pais_id', 'paises', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('equipos');
    }

    public function down()
    {
        $this->forge->dropTable('equipos');
    }
}

