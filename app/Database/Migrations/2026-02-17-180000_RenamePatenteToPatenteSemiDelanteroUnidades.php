<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Renombra patente a patente_semi_delantero en unidades (patente del semi 1).
 * La tabla calibraciones sigue con columna "patente"; el JOIN es unidades.patente_semi_delantero = calibraciones.patente.
 */
class RenamePatenteToPatenteSemiDelanteroUnidades extends Migration
{
    public function up()
    {
        $t = $this->db->prefixTable('unidades');
        // Eliminar índice sobre la columna antigua (nombre típico de Forge)
        $this->db->query("ALTER TABLE {$t} DROP INDEX patente");
        $this->db->query("ALTER TABLE {$t} CHANGE COLUMN patente patente_semi_delantero VARCHAR(20) NOT NULL COMMENT 'Patente del semi delantero'");
        $this->db->query("CREATE INDEX idx_patente_semi_delantero ON {$t} (patente_semi_delantero)");
    }

    public function down()
    {
        $t = $this->db->prefixTable('unidades');
        $this->db->query("ALTER TABLE {$t} DROP INDEX idx_patente_semi_delantero");
        $this->db->query("ALTER TABLE {$t} CHANGE COLUMN patente_semi_delantero patente VARCHAR(20) NOT NULL COMMENT 'Patente de la unidad - OBLIGATORIO'");
        $this->db->query("CREATE INDEX patente ON {$t} (patente)");
    }
}
