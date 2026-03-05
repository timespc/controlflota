<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Añade columna must_change_password a users (Ion Auth).
 * 1 = obligar a cambiar contraseña en el próximo acceso.
 */
class AddMustChangePasswordToUsers extends Migration
{
    public function up()
    {
        $config = config('IonAuth');
        $tables = $config->tables;
        $table = $this->db->getPrefix() . $tables['users'];
        if ($this->db->tableExists($tables['users']) && ! $this->db->fieldExists('must_change_password', $tables['users'])) {
            $this->db->query("ALTER TABLE `{$table}` ADD COLUMN `must_change_password` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1 = obligar cambio en próximo login'");
        }
    }

    public function down()
    {
        $config = config('IonAuth');
        $tables = $config->tables;
        $table = $this->db->getPrefix() . $tables['users'];
        if ($this->db->fieldExists('must_change_password', $tables['users'])) {
            $this->db->query("ALTER TABLE `{$table}` DROP COLUMN `must_change_password`");
        }
    }
}
