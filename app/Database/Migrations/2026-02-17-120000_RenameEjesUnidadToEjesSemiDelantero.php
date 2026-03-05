<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Renombra ejes_unidad a ejes_semi_delantero en unidades (ejes del semi 1).
 */
class RenameEjesUnidadToEjesSemiDelantero extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE unidades CHANGE COLUMN ejes_unidad ejes_semi_delantero INT(2) NULL DEFAULT NULL');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE unidades CHANGE COLUMN ejes_semi_delantero ejes_unidad INT(2) NULL DEFAULT NULL');
    }
}
