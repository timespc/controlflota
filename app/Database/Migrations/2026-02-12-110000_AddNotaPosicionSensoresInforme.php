<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNotaPosicionSensoresInforme extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('calibracion_informe_carga_segura')) {
            return;
        }
        if ($this->db->fieldExists('nota_posicion_sensores', 'calibracion_informe_carga_segura')) {
            return;
        }
        $this->forge->addColumn('calibracion_informe_carga_segura', [
            'nota_posicion_sensores' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'fecha_emision',
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->tableExists('calibracion_informe_carga_segura')
            && $this->db->fieldExists('nota_posicion_sensores', 'calibracion_informe_carga_segura')) {
            $this->forge->dropColumn('calibracion_informe_carga_segura', 'nota_posicion_sensores');
        }
    }
}
