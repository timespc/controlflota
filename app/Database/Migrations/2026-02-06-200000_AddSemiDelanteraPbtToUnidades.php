<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Añade semi_delantera_pbt a unidades para simetría con Semi Trasero
 * (Patente, Año modelo, TARA, PBT en ambas semis).
 */
class AddSemiDelanteraPbtToUnidades extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('semi_delantera_pbt', 'unidades')) {
            $this->forge->addColumn('unidades', [
                'semi_delantera_pbt' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,3',
                    'null'       => true,
                    'after'      => 'tara_unidad',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('semi_delantera_pbt', 'unidades')) {
            $this->forge->dropColumn('unidades', 'semi_delantera_pbt');
        }
    }
}
