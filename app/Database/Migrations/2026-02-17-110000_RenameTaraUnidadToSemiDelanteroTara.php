<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Renombra tara_unidad a semi_delantero_tara en unidades (tara del semi 1).
 */
class RenameTaraUnidadToSemiDelanteroTara extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE unidades CHANGE COLUMN tara_unidad semi_delantero_tara DECIMAL(10,3) NULL DEFAULT NULL');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE unidades CHANGE COLUMN semi_delantero_tara tara_unidad DECIMAL(10,3) NULL DEFAULT NULL');
    }
}
