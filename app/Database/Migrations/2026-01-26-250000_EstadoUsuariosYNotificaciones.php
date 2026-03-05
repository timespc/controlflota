<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EstadoUsuariosYNotificaciones extends Migration
{
    public function up()
    {
        $prefix = $this->db->getPrefix();

        if ($this->db->tableExists('usuarios') && ! $this->db->fieldExists('estado', 'usuarios')) {
            $tbl = $prefix . 'usuarios';
            $this->db->query("ALTER TABLE `{$tbl}` ADD COLUMN `estado` VARCHAR(20) NOT NULL DEFAULT 'activo' COMMENT 'pendiente, activo, rechazado' AFTER `id_rol`");
            $this->db->query("UPDATE `{$tbl}` SET estado = 'activo' WHERE estado = '' OR estado IS NULL");
        }

        if (! $this->db->tableExists('notificaciones')) {
            $tbl = $prefix . 'notificaciones';
            $this->db->query("CREATE TABLE `{$tbl}` (
                `id_notificacion` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `tipo` VARCHAR(64) NOT NULL COMMENT 'ej. nuevo_usuario',
                `titulo` VARCHAR(255) NOT NULL,
                `mensaje` TEXT NULL,
                `datos_json` TEXT NULL COMMENT 'ej. {\"id_usuario\":123}',
                `created_at` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id_notificacion`),
                KEY `tipo` (`tipo`),
                KEY `created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        }

        if (! $this->db->tableExists('notificacion_leida')) {
            $tbl = $prefix . 'notificacion_leida';
            $notif = $prefix . 'notificaciones';
            $usuarios = $prefix . 'usuarios';
            $this->db->query("CREATE TABLE `{$tbl}` (
                `id_notificacion` INT(11) UNSIGNED NOT NULL,
                `id_usuario` INT(11) UNSIGNED NOT NULL,
                `leida_at` DATETIME NOT NULL,
                PRIMARY KEY (`id_notificacion`, `id_usuario`),
                KEY `id_usuario` (`id_usuario`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        }
    }

    public function down()
    {
        $prefix = $this->db->getPrefix();
        if ($this->db->tableExists('notificacion_leida')) {
            $this->forge->dropTable('notificacion_leida', true);
        }
        if ($this->db->tableExists('notificaciones')) {
            $this->forge->dropTable('notificaciones', true);
        }
        if ($this->db->tableExists('usuarios') && $this->db->fieldExists('estado', 'usuarios')) {
            $this->db->query('ALTER TABLE `' . $prefix . 'usuarios` DROP COLUMN `estado`');
        }
    }
}
