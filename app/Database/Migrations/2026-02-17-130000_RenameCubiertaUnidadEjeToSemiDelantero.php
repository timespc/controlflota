<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Renombra cubierta_unidad_eje* a cubierta_semi_delantero_eje* en unidades (cubiertas del semi 1).
 */
class RenameCubiertaUnidadEjeToSemiDelantero extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE unidades CHANGE COLUMN cubierta_unidad_eje1 cubierta_semi_delantero_eje1 INT(11) UNSIGNED NULL DEFAULT NULL');
        $this->db->query('ALTER TABLE unidades CHANGE COLUMN cubierta_unidad_eje2 cubierta_semi_delantero_eje2 INT(11) UNSIGNED NULL DEFAULT NULL');
        $this->db->query('ALTER TABLE unidades CHANGE COLUMN cubierta_unidad_eje3 cubierta_semi_delantero_eje3 INT(11) UNSIGNED NULL DEFAULT NULL');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE unidades CHANGE COLUMN cubierta_semi_delantero_eje1 cubierta_unidad_eje1 INT(11) UNSIGNED NULL DEFAULT NULL');
        $this->db->query('ALTER TABLE unidades CHANGE COLUMN cubierta_semi_delantero_eje2 cubierta_unidad_eje2 INT(11) UNSIGNED NULL DEFAULT NULL');
        $this->db->query('ALTER TABLE unidades CHANGE COLUMN cubierta_semi_delantero_eje3 cubierta_unidad_eje3 INT(11) UNSIGNED NULL DEFAULT NULL');
    }
}
