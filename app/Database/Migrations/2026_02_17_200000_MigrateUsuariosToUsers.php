<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migra referencias de tabla usuarios a users (Ion Auth).
 * - Reasigna id_usuario_* a users.id (por email donde exista en usuarios).
 * - Cambia FKs de usuarios.id_usuario a users.id.
 * - Elimina la tabla usuarios.
 */
class MigrateUsuariosToUsers extends Migration
{
    public function up()
    {
        $prefix = $this->db->getPrefix();
        $tUsuarios = $prefix . 'usuarios';
        $tUsers   = $prefix . 'users';

        if (! $this->db->tableExists('users')) {
            return;
        }

        $this->db->disableForeignKeyChecks();

        // 1) Migrar datos: ids que vienen de usuarios -> users.id por email
        if ($this->db->tableExists('usuarios')) {
            // calibraciones.id_usuario_impresion
            if ($this->db->tableExists('calibraciones') && $this->db->fieldExists('id_usuario_impresion', 'calibraciones')) {
                $tCal = $prefix . 'calibraciones';
                $this->db->query("
                    UPDATE {$tCal} c
                    INNER JOIN {$tUsuarios} us ON us.id_usuario = c.id_usuario_impresion
                    INNER JOIN {$tUsers} u ON u.email = us.email
                    SET c.id_usuario_impresion = u.id
                ");
            }
            // calibracion_reimpresiones.id_usuario
            if ($this->db->tableExists('calibracion_reimpresiones')) {
                $tReimp = $prefix . 'calibracion_reimpresiones';
                $this->db->query("
                    UPDATE {$tReimp} r
                    INNER JOIN {$tUsuarios} us ON us.id_usuario = r.id_usuario
                    INNER JOIN {$tUsers} u ON u.email = us.email
                    SET r.id_usuario = u.id
                ");
            }
            // calibracion_notas.id_usuario
            if ($this->db->tableExists('calibracion_notas')) {
                $tNotas = $prefix . 'calibracion_notas';
                $this->db->query("
                    UPDATE {$tNotas} n
                    INNER JOIN {$tUsuarios} us ON us.id_usuario = n.id_usuario
                    INNER JOIN {$tUsers} u ON u.email = us.email
                    SET n.id_usuario = u.id
                ");
            }
            // reglas.id_usuario_creacion
            if ($this->db->tableExists('reglas') && $this->db->fieldExists('id_usuario_creacion', 'reglas')) {
                $tReglas = $prefix . 'reglas';
                $this->db->query("
                    UPDATE {$tReglas} r
                    INNER JOIN {$tUsuarios} us ON us.id_usuario = r.id_usuario_creacion
                    INNER JOIN {$tUsers} u ON u.email = us.email
                    SET r.id_usuario_creacion = u.id
                ");
            }
        }

        // 2) Quitar FKs que apuntan a usuarios
        try {
            if ($this->db->tableExists('calibraciones') && $this->db->fieldExists('id_usuario_impresion', 'calibraciones')) {
                $this->db->query('ALTER TABLE `' . $prefix . 'calibraciones` DROP FOREIGN KEY `fk_calibraciones_usuario_impresion`');
            }
        } catch (\Throwable $e) {
        }
        if ($this->db->tableExists('calibracion_reimpresiones')) {
            $fk = $this->db->query("
                SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = 'id_usuario' AND REFERENCED_TABLE_NAME IS NOT NULL
            ", [$this->db->getDatabase(), $prefix . 'calibracion_reimpresiones'])->getRow();
            if ($fk && ! empty($fk->CONSTRAINT_NAME)) {
                try {
                    $this->db->query('ALTER TABLE `' . $prefix . 'calibracion_reimpresiones` DROP FOREIGN KEY `' . $fk->CONSTRAINT_NAME . '`');
                } catch (\Throwable $e) {
                }
            }
        }
        try {
            if ($this->db->tableExists('calibracion_notas')) {
                $this->db->query('ALTER TABLE `' . $prefix . 'calibracion_notas` DROP FOREIGN KEY `fk_calibracion_notas_usuario`');
            }
        } catch (\Throwable $e) {
        }
        try {
            if ($this->db->tableExists('reglas') && $this->db->fieldExists('id_usuario_creacion', 'reglas')) {
                $this->db->query('ALTER TABLE `' . $prefix . 'reglas` DROP FOREIGN KEY `fk_reglas_usuario_creacion`');
            }
        } catch (\Throwable $e) {
        }

        // 3) Añadir FKs a users(id)
        if ($this->db->tableExists('calibraciones') && $this->db->fieldExists('id_usuario_impresion', 'calibraciones')) {
            $this->db->query('ALTER TABLE `' . $prefix . 'calibraciones` ADD CONSTRAINT `fk_calibraciones_usuario_impresion` FOREIGN KEY (`id_usuario_impresion`) REFERENCES `' . $tUsers . '`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        }
        if ($this->db->tableExists('calibracion_reimpresiones')) {
            $this->db->query('ALTER TABLE `' . $prefix . 'calibracion_reimpresiones` ADD CONSTRAINT `fk_calibracion_reimpresiones_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `' . $tUsers . '`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
        }
        if ($this->db->tableExists('calibracion_notas')) {
            $this->db->query('ALTER TABLE `' . $prefix . 'calibracion_notas` ADD CONSTRAINT `fk_calibracion_notas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `' . $tUsers . '`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        }
        if ($this->db->tableExists('reglas') && $this->db->fieldExists('id_usuario_creacion', 'reglas')) {
            $this->db->query('ALTER TABLE `' . $prefix . 'reglas` ADD CONSTRAINT `fk_reglas_usuario_creacion` FOREIGN KEY (`id_usuario_creacion`) REFERENCES `' . $tUsers . '`(`id`) ON DELETE SET NULL ON UPDATE CASCADE');
        }

        // 4) Eliminar tabla usuarios
        if ($this->db->tableExists('usuarios')) {
            $this->forge->dropTable('usuarios', true);
        }

        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        // No se revierte: la tabla usuarios ya no existe y no se recrea.
    }
}
