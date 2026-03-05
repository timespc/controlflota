<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Añade columna deleted_at a users (Ion Auth) para borrado lógico.
 * Cuando deleted_at IS NOT NULL, el usuario está eliminado (no se lista ni puede iniciar sesión).
 */
class AddDeletedAtToUsers extends Migration
{
    public function up()
    {
        $config = config('IonAuth');
        $tables = $config->tables;
        $table = $this->db->getPrefix() . $tables['users'];
        if ($this->db->tableExists($tables['users']) && ! $this->db->fieldExists('deleted_at', $tables['users'])) {
            $this->db->query("ALTER TABLE `{$table}` ADD COLUMN `deleted_at` DATETIME NULL DEFAULT NULL COMMENT 'Borrado lógico' AFTER `active`");
        }
    }

    public function down()
    {
        $config = config('IonAuth');
        $tables = $config->tables;
        $table = $this->db->getPrefix() . $tables['users'];
        if ($this->db->fieldExists('deleted_at', $tables['users'])) {
            $this->db->query("ALTER TABLE `{$table}` DROP COLUMN `deleted_at`");
        }
    }
}
