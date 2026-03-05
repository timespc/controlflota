<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TokenPublicoYAccesosCalibracion extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('calibraciones')) {
            if (! $this->db->fieldExists('token_publico', 'calibraciones')) {
                $this->forge->addColumn('calibraciones', [
                    'token_publico' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 64,
                        'null'       => true,
                        'comment'    => 'Token único para URL pública de solo lectura'
                    ]
                ]);
            }
            $this->forge->addKey('token_publico', false, true); // unique
        }

        if (! $this->db->tableExists('calibracion_accesos')) {
            $tblAccesos = $this->db->prefixTable('calibracion_accesos');
            $sql = "CREATE TABLE `{$tblAccesos}` (
                `id_acceso` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `id_calibracion` INT(11) UNSIGNED NOT NULL,
                `ip` VARCHAR(45) NULL DEFAULT NULL,
                `user_agent` TEXT NULL DEFAULT NULL,
                `referer_url` TEXT NULL DEFAULT NULL,
                `accedido_at` DATETIME NOT NULL,
                PRIMARY KEY (`id_acceso`),
                INDEX `accedido_at` (`accedido_at`),
                INDEX `id_calibracion` (`id_calibracion`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";
            $this->db->query($sql);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('calibracion_accesos')) {
            $this->forge->dropTable('calibracion_accesos', true);
        }
        if ($this->db->tableExists('calibraciones') && $this->db->fieldExists('token_publico', 'calibraciones')) {
            $this->forge->dropColumn('calibraciones', 'token_publico');
        }
    }
}
