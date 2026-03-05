<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Elimina el campo tipo_unidad de la tabla unidades (equipos).
 * Ya no se usa en el módulo Equipos.
 */
class MakeTipoUnidadNullableUnidades extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE unidades DROP COLUMN tipo_unidad');
    }

    public function down()
    {
        $this->db->query("ALTER TABLE unidades ADD COLUMN tipo_unidad ENUM('CHASIS','ACOPLADO','SEMI') NULL DEFAULT NULL AFTER id_tta");
    }
}
