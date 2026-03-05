<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Configuración de notificaciones por admin: email, WhatsApp, recordatorio, tipos activos.
 */
class ConfigNotificacionesAdmin extends Migration
{
    public function up()
    {
        $prefix = $this->db->getPrefix();

        if (! $this->db->tableExists('notificacion_config')) {
            $tbl = $prefix . 'notificacion_config';
            $usuarios = $prefix . 'usuarios';
            $this->db->query("CREATE TABLE `{$tbl}` (
                `id_config` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_usuario` INT(11) UNSIGNED NOT NULL,
                `email_activo` TINYINT(1) NOT NULL DEFAULT 1,
                `email_destino` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Si NULL se usa el email del usuario',
                `whatsapp_activo` TINYINT(1) NOT NULL DEFAULT 0,
                `whatsapp_numero` VARCHAR(50) NULL DEFAULT NULL,
                `recordatorio_minutos` INT(11) NOT NULL DEFAULT 0 COMMENT '0 = sin recordatorio',
                `created_at` DATETIME NULL DEFAULT NULL,
                `updated_at` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id_config`),
                UNIQUE KEY `id_usuario` (`id_usuario`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        }

        if (! $this->db->tableExists('notificacion_config_tipo')) {
            $tbl = $prefix . 'notificacion_config_tipo';
            $this->db->query("CREATE TABLE `{$tbl}` (
                `id_usuario` INT(11) UNSIGNED NOT NULL,
                `tipo_notificacion` VARCHAR(64) NOT NULL,
                `activo` TINYINT(1) NOT NULL DEFAULT 1,
                `created_at` DATETIME NULL DEFAULT NULL,
                `updated_at` DATETIME NULL DEFAULT NULL,
                PRIMARY KEY (`id_usuario`, `tipo_notificacion`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        }

        if (! $this->db->tableExists('notificacion_recordatorio')) {
            $tbl = $prefix . 'notificacion_recordatorio';
            $this->db->query("CREATE TABLE `{$tbl}` (
                `id_notificacion` INT(11) UNSIGNED NOT NULL,
                `id_usuario` INT(11) UNSIGNED NOT NULL COMMENT 'Admin que recibe el recordatorio',
                `enviado_at` DATETIME NOT NULL,
                PRIMARY KEY (`id_notificacion`, `id_usuario`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci");
        }
    }

    public function down()
    {
        $prefix = $this->db->getPrefix();
        if ($this->db->tableExists('notificacion_recordatorio')) {
            $this->forge->dropTable('notificacion_recordatorio', true);
        }
        if ($this->db->tableExists('notificacion_config_tipo')) {
            $this->forge->dropTable('notificacion_config_tipo', true);
        }
        if ($this->db->tableExists('notificacion_config')) {
            $this->forge->dropTable('notificacion_config', true);
        }
    }
}
