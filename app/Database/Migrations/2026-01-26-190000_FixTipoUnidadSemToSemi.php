<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Corrige el valor del ENUM tipo_unidad de SEM a SEMI en la tabla unidades.
 * Ejecutar si la tabla ya existía con el typo (migración 2026-01-20-142654).
 */
class FixTipoUnidadSemToSemi extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('unidades')) {
            return;
        }

        $driver = $this->db->DBDriver;
        if (strtolower($driver) !== 'mysql') {
            return;
        }

        // Añadir SEMI al ENUM y actualizar registros SEM -> SEMI, luego dejar solo CHASIS, ACOPLADO, SEMI
        $this->db->query("ALTER TABLE unidades MODIFY tipo_unidad ENUM('CHASIS','ACOPLADO','SEM','SEMI') NOT NULL");
        $this->db->query("UPDATE unidades SET tipo_unidad = 'SEMI' WHERE tipo_unidad = 'SEM'");
        $this->db->query("ALTER TABLE unidades MODIFY tipo_unidad ENUM('CHASIS','ACOPLADO','SEMI') NOT NULL");
    }

    public function down()
    {
        if (! $this->db->tableExists('unidades')) {
            return;
        }

        $driver = $this->db->DBDriver;
        if (strtolower($driver) !== 'mysql') {
            return;
        }

        $this->db->query("ALTER TABLE unidades MODIFY tipo_unidad ENUM('CHASIS','ACOPLADO','SEM','SEMI') NOT NULL");
        $this->db->query("UPDATE unidades SET tipo_unidad = 'SEM' WHERE tipo_unidad = 'SEMI'");
        $this->db->query("ALTER TABLE unidades MODIFY tipo_unidad ENUM('CHASIS','ACOPLADO','SEM') NOT NULL");
    }
}
