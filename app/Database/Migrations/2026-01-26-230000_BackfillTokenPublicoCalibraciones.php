<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Rellena token_publico en calibraciones que no lo tienen (p. ej. después de migrar datos del sistema viejo).
 * Así todas las calibraciones tendrán URL pública y QR válido.
 */
class BackfillTokenPublicoCalibraciones extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('calibraciones') || ! $this->db->fieldExists('token_publico', 'calibraciones')) {
            return;
        }

        $table = $this->db->prefixTable('calibraciones');
        $rows  = $this->db->query("SELECT id_calibracion FROM `{$table}` WHERE (token_publico IS NULL OR token_publico = '')")->getResultArray();

        foreach ($rows as $row) {
            $id = (int) $row['id_calibracion'];
            $token = bin2hex(random_bytes(16));
            $this->db->table('calibraciones')->where('id_calibracion', $id)->update(['token_publico' => $token]);
        }
    }

    public function down()
    {
        // No revertir: dejar los tokens generados
    }
}
