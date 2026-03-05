<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RolesYUsuarios extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('roles')) {
            $tbl = $this->db->prefixTable('roles');
            $this->db->query("CREATE TABLE `{$tbl}` (
                `id_rol` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `nombre` VARCHAR(64) NOT NULL,
                `es_default` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = rol por defecto para nuevos usuarios (ej. login con Gmail)',
                `created_at` DATETIME NULL DEFAULT NULL,
                `updated_at` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id_rol`),
                UNIQUE KEY `nombre` (`nombre`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
            $this->db->query("INSERT INTO `{$tbl}` (`id_rol`, `nombre`, `es_default`, `created_at`, `updated_at`) VALUES
                (1, 'Admin', 0, NOW(), NOW()),
                (2, 'Carga de datos', 0, NOW(), NOW()),
                (3, 'Solo lectura', 1, NOW(), NOW())");
        }

        if (! $this->db->tableExists('usuarios')) {
            $tbl = $this->db->prefixTable('usuarios');
            $this->db->query("CREATE TABLE `{$tbl}` (
                `id_usuario` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `email` VARCHAR(255) NOT NULL,
                `nombre` VARCHAR(255) NULL DEFAULT NULL,
                `avatar_url` VARCHAR(512) NULL DEFAULT NULL,
                `google_id` VARCHAR(128) NULL DEFAULT NULL,
                `id_rol` INT(11) UNSIGNED NOT NULL DEFAULT 3,
                `last_login_at` DATETIME NULL DEFAULT NULL,
                `created_at` DATETIME NULL DEFAULT NULL,
                `updated_at` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id_usuario`),
                UNIQUE KEY `email` (`email`),
                UNIQUE KEY `google_id` (`google_id`),
                KEY `id_rol` (`id_rol`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        }
    }

    public function down()
    {
        if ($this->db->tableExists('usuarios')) {
            $this->forge->dropTable('usuarios', true);
        }
        if ($this->db->tableExists('roles')) {
            $this->forge->dropTable('roles', true);
        }
    }
}
