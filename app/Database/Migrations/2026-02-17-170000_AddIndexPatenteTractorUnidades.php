<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Añade índice sobre patente_tractor en unidades (mismo criterio que el índice de patente: mejorar consultas por patente del tractor).
 * Ninguna tabla usa patente ni patente_tractor como FK; ambos índices son solo para rendimiento.
 */
class AddIndexPatenteTractorUnidades extends Migration
{
    public function up()
    {
        $this->db->query('CREATE INDEX idx_patente_tractor ON ' . $this->db->prefixTable('unidades') . ' (patente_tractor)');
    }

    public function down()
    {
        $this->db->query('DROP INDEX idx_patente_tractor ON ' . $this->db->prefixTable('unidades'));
    }
}
