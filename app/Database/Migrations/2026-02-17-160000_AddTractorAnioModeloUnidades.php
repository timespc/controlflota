<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Añade tractor_anio_modelo en unidades (año modelo del tractor).
 */
class AddTractorAnioModeloUnidades extends Migration
{
    public function up()
    {
        $this->forge->addColumn('unidades', [
            'tractor_anio_modelo' => [
                'type'       => 'INT',
                'constraint' => 4,
                'null'       => true,
                'after'      => 'tractor_pbt',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('unidades', 'tractor_anio_modelo');
    }
}
