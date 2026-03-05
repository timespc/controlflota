<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Añade a unidades los campos que estaban en equipos:
 * bitren, patente_semi_trasero (2ª patente de semi), modo_carga, capacidades cisterna, etc.
 * Para tener una sola tabla con toda la info (unidad = tractor + 1 o 2 semis).
 */
class UnidadesBitrenYCamposEquipo extends Migration
{
    public function up()
    {
        $this->forge->addColumn('unidades', [
            'bitren' => [
                'type'       => 'ENUM',
                'constraint' => ['SI', 'NO'],
                'default'    => 'NO',
                'null'       => true,
                'after'      => 'patente_tractor',
            ],
            'patente_semi_trasero' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'bitren',
            ],
            'semi_trasero_anio_modelo' => [
                'type'       => 'INT',
                'constraint' => 4,
                'null'       => true,
                'after'      => 'patente_semi_trasero',
            ],
            'semi_trasero_tara' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
                'null'       => true,
                'after'      => 'semi_trasero_anio_modelo',
            ],
            'semi_trasero_pbt' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
                'null'       => true,
                'after'      => 'semi_trasero_tara',
            ],
            'modo_carga' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'semi_trasero_pbt',
            ],
            'fecha_alta' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'modo_carga',
            ],
            'tractor_tara' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
                'null'       => true,
                'after'      => 'fecha_alta',
            ],
            'tractor_pbt' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
                'null'       => true,
                'after'      => 'tractor_tara',
            ],
            'tara_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
                'null'       => true,
                'after'      => 'tractor_pbt',
            ],
            'peso_maximo' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
                'null'       => true,
                'after'      => 'tara_total',
            ],
            'cisterna_1_capacidad'  => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0, 'after' => 'peso_maximo'],
            'cisterna_2_capacidad'  => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0, 'after' => 'cisterna_1_capacidad'],
            'cisterna_3_capacidad'  => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0, 'after' => 'cisterna_2_capacidad'],
            'cisterna_4_capacidad'  => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0, 'after' => 'cisterna_3_capacidad'],
            'cisterna_5_capacidad'  => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0, 'after' => 'cisterna_4_capacidad'],
            'cisterna_6_capacidad'  => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0, 'after' => 'cisterna_5_capacidad'],
            'cisterna_7_capacidad'  => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0, 'after' => 'cisterna_6_capacidad'],
            'cisterna_8_capacidad'  => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0, 'after' => 'cisterna_7_capacidad'],
            'cisterna_9_capacidad'  => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0, 'after' => 'cisterna_8_capacidad'],
            'cisterna_10_capacidad' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true, 'default' => 0, 'after' => 'cisterna_9_capacidad'],
            'capacidad_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => 0,
                'after'      => 'cisterna_10_capacidad',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('unidades', [
            'bitren',
            'patente_semi_trasero',
            'semi_trasero_anio_modelo',
            'semi_trasero_tara',
            'semi_trasero_pbt',
            'modo_carga',
            'fecha_alta',
            'tractor_tara',
            'tractor_pbt',
            'tara_total',
            'peso_maximo',
            'cisterna_1_capacidad',
            'cisterna_2_capacidad',
            'cisterna_3_capacidad',
            'cisterna_4_capacidad',
            'cisterna_5_capacidad',
            'cisterna_6_capacidad',
            'cisterna_7_capacidad',
            'cisterna_8_capacidad',
            'cisterna_9_capacidad',
            'cisterna_10_capacidad',
            'capacidad_total',
        ]);
    }
}
