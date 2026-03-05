<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Quita la columna transporte de unidades (origen/uso poco claro; se puede
 * volver a agregar más adelante si se define bien).
 */
class DropTransporteFromUnidades extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('transporte', 'unidades')) {
            $this->forge->dropColumn('unidades', 'transporte');
        }
    }

    public function down()
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
}
