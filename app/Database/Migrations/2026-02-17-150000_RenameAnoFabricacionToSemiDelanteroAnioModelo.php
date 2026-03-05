<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Renombra ano_fabricacion a semi_delantero_anio_modelo en unidades (año del semi 1).
 */
class RenameAnoFabricacionToSemiDelanteroAnioModelo extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE unidades CHANGE COLUMN ano_fabricacion semi_delantero_anio_modelo INT(4) NULL DEFAULT NULL');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE unidades CHANGE COLUMN semi_delantero_anio_modelo ano_fabricacion INT(4) NULL DEFAULT NULL');
    }
}
