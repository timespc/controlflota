<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Renombra la tabla unidades a equipos.
 * Actualiza la FK en calibraciones para que apunte a equipos(id_equipo).
 */
class RenameTableUnidadesToEquipos extends Migration
{
    public function up()
    {
        $db = $this->db;
        $tUnidades = $db->prefixTable('unidades');
        $tEquipos  = $db->prefixTable('equipos');
        $tCalib    = $db->prefixTable('calibraciones');

        // 1. Obtener nombre de la FK en calibraciones que referencia unidades
        $fkName = null;
        $sql = "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = " . $db->escape($tCalib) . "
                AND COLUMN_NAME = 'id_equipo'
                AND REFERENCED_TABLE_NAME = " . $db->escape($tUnidades) . "
                LIMIT 1";
        $row = $db->query($sql)->getRow();
        if ($row !== null && ! empty($row->CONSTRAINT_NAME)) {
            $fkName = $row->CONSTRAINT_NAME;
        }

        if ($fkName !== null) {
            $db->query('ALTER TABLE ' . $tCalib . ' DROP FOREIGN KEY ' . $db->escapeIdentifiers($fkName));
        }

        // 2. Renombrar tabla
        $db->query('RENAME TABLE ' . $tUnidades . ' TO ' . $tEquipos);

        // 3. Recrear FK hacia equipos
        $db->query('ALTER TABLE ' . $tCalib . ' ADD CONSTRAINT calibraciones_id_equipo_foreign FOREIGN KEY (id_equipo) REFERENCES ' . $tEquipos . '(id_equipo) ON UPDATE SET NULL ON DELETE CASCADE');
    }

    public function down()
    {
        $db = $this->db;
        $tUnidades = $db->prefixTable('unidades');
        $tEquipos  = $db->prefixTable('equipos');
        $tCalib    = $db->prefixTable('calibraciones');

        $fkName = null;
        $sql = "SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = " . $db->escape($tCalib) . "
                AND COLUMN_NAME = 'id_equipo'
                AND REFERENCED_TABLE_NAME = " . $db->escape($tEquipos) . "
                LIMIT 1";
        $row = $db->query($sql)->getRow();
        if ($row !== null && ! empty($row->CONSTRAINT_NAME)) {
            $fkName = $row->CONSTRAINT_NAME;
        }

        if ($fkName !== null) {
            $db->query('ALTER TABLE ' . $tCalib . ' DROP FOREIGN KEY ' . $db->escapeIdentifiers($fkName));
        }

        $db->query('RENAME TABLE ' . $tEquipos . ' TO ' . $tUnidades);

        $db->query('ALTER TABLE ' . $tCalib . ' ADD CONSTRAINT calibraciones_id_equipo_foreign FOREIGN KEY (id_equipo) REFERENCES ' . $tUnidades . '(id_equipo) ON UPDATE SET NULL ON DELETE CASCADE');
    }
}
