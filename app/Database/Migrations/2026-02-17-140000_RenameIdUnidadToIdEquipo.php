<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Renombra id_unidad a id_equipo en unidades (PK) y en calibraciones (FK).
 * No se pierde información; solo cambia el nombre de la columna.
 */
class RenameIdUnidadToIdEquipo extends Migration
{
    public function up()
    {
        $db = $this->db;

        // 1. Obtener nombre de la FK en calibraciones que referencia unidades.id_unidad
        $fkName = null;
        $sql = "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'calibraciones'
                AND COLUMN_NAME = 'id_unidad'
                AND REFERENCED_TABLE_NAME = 'unidades'
                LIMIT 1";
        $row = $db->query($sql)->getRow();
        if ($row !== null && ! empty($row->CONSTRAINT_NAME)) {
            $fkName = $row->CONSTRAINT_NAME;
        }

        if ($fkName !== null) {
            $db->query('ALTER TABLE calibraciones DROP FOREIGN KEY ' . $db->escapeIdentifiers($fkName));
        }

        // 2. Renombrar PK en unidades
        $db->query('ALTER TABLE unidades CHANGE COLUMN id_unidad id_equipo INT(11) UNSIGNED NOT NULL AUTO_INCREMENT');

        // 3. Renombrar columna FK en calibraciones
        $db->query('ALTER TABLE calibraciones CHANGE COLUMN id_unidad id_equipo INT(11) UNSIGNED NULL DEFAULT NULL');

        // 4. Recrear FK
        if ($fkName !== null) {
            $db->query('ALTER TABLE calibraciones ADD CONSTRAINT calibraciones_id_equipo_foreign FOREIGN KEY (id_equipo) REFERENCES unidades(id_equipo) ON UPDATE SET NULL ON DELETE CASCADE');
        }
    }

    public function down()
    {
        $db = $this->db;

        $fkName = null;
        $sql = "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'calibraciones'
                AND COLUMN_NAME = 'id_equipo'
                AND REFERENCED_TABLE_NAME = 'unidades'
                LIMIT 1";
        $row = $db->query($sql)->getRow();
        if ($row !== null && ! empty($row->CONSTRAINT_NAME)) {
            $fkName = $row->CONSTRAINT_NAME;
        }

        if ($fkName !== null) {
            $db->query('ALTER TABLE calibraciones DROP FOREIGN KEY ' . $db->escapeIdentifiers($fkName));
        }

        $db->query('ALTER TABLE calibraciones CHANGE COLUMN id_equipo id_unidad INT(11) UNSIGNED NULL DEFAULT NULL');
        $db->query('ALTER TABLE unidades CHANGE COLUMN id_equipo id_unidad INT(11) UNSIGNED NOT NULL AUTO_INCREMENT');

        if ($fkName !== null) {
            $db->query('ALTER TABLE calibraciones ADD CONSTRAINT calibraciones_id_unidad_foreign FOREIGN KEY (id_unidad) REFERENCES unidades(id_unidad) ON UPDATE SET NULL ON DELETE CASCADE');
        }
    }
}
