<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Elimina la tabla equipos y la columna id_equipo de unidades.
 * Toda la info queda en la tabla unidades (bitren, patente_semi_trasero, etc.).
 */
class DropEquiposYIdEquipoEnUnidades extends Migration
{
    public function up()
    {
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        if ($this->db->tableExists('unidades') && $this->db->fieldExists('id_equipo', 'unidades')) {
            $this->forge->dropColumn('unidades', 'id_equipo');
        }
        if ($this->db->tableExists('equipos')) {
            $this->forge->dropTable('equipos', true);
        }

        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function down()
    {
        // No restauramos equipos ni id_equipo; sería necesario tener un backup
    }
}
