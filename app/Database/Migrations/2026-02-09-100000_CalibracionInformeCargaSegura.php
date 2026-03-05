<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CalibracionInformeCargaSegura extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('calibraciones')) {
            return;
        }

        // Cabecera del informe (1 fila por calibración)
        if (! $this->db->tableExists('calibracion_informe_carga_segura')) {
            $this->forge->addField([
                'id_calibracion' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'resultado_control_vacio' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 10,
                    'null'       => true,
                    'comment'    => 'SI/NO',
                ],
                'resultado_trazabilidad' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 10,
                    'null'       => true,
                    'comment'    => 'SI/NO',
                ],
                'resultado_posicion' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 10,
                    'null'       => true,
                    'comment'    => 'SI/NO',
                ],
                'responsable_nombre' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'responsable_cargo' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'cuit_transportista' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
                'fecha_emision' => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id_calibracion', true);
            $this->forge->addForeignKey('id_calibracion', 'calibraciones', 'id_calibracion', 'CASCADE', 'CASCADE');
            $this->forge->createTable('calibracion_informe_carga_segura');
        }

        // Detalle por cisterna (hasta 10 cisternas)
        if (! $this->db->tableExists('calibracion_informe_carga_segura_detalle')) {
            $this->forge->addField([
                'id_calibracion' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'numero_cisterna' => [
                    'type'       => 'INT',
                    'constraint' => 2,
                    'unsigned'   => true,
                    'null'       => false,
                    'comment'    => '1 a 10',
                ],
                'volumen_lts' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
                'vacio_requerido' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
                'vacio_medido' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
                'accion_tomada' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'volumen_final_lts' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => true],
                'cumple_control' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
                'marca_sensor' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'numero_serie_sensor' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'cumple_trazabilidad' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
                'cumple_posicion' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
                'observacion_posicion' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'litros_sensor_rebalse' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            ]);
            $this->forge->addPrimaryKey(['id_calibracion', 'numero_cisterna']);
            $this->forge->addForeignKey('id_calibracion', 'calibraciones', 'id_calibracion', 'CASCADE', 'CASCADE');
            $this->forge->createTable('calibracion_informe_carga_segura_detalle');
        }
    }

    public function down()
    {
        if ($this->db->tableExists('calibracion_informe_carga_segura_detalle')) {
            $this->forge->dropTable('calibracion_informe_carga_segura_detalle', true);
        }
        if ($this->db->tableExists('calibracion_informe_carga_segura')) {
            $this->forge->dropTable('calibracion_informe_carga_segura', true);
        }
    }
}
