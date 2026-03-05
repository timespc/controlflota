<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Añade columna transporte a unidades (dato que puede venir del cruce con BD camiones).
 */
class AddTransporteToUnidades extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('transporte', 'unidades')) {
            $this->forge->addColumn('unidades', [
                'transporte' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'after'      => 'modo_carga',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('transporte', 'unidades')) {
            $this->forge->dropColumn('unidades', 'transporte');
        }
    }
}
