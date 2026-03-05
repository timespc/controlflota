<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Notas del calibrador por calibración (texto libre, quién cargó, cuándo).
 * Solo uso interno: NUNCA imprimir en la tarjeta de calibración ni exponer en vista pública.
 */
class CalibracionNotas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_calibracion' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'notas' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Notas del calibrador (texto libre)',
            ],
            'id_usuario' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Usuario que cargó/actualizó las notas',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_calibracion', true);
        $this->forge->createTable('calibracion_notas', true);

        $tCalib = $this->db->prefixTable('calibraciones');
        $tNotas = $this->db->prefixTable('calibracion_notas');
        $this->db->query("ALTER TABLE `{$tNotas}` ADD CONSTRAINT `fk_calibracion_notas_calibracion` FOREIGN KEY (`id_calibracion`) REFERENCES `{$tCalib}`(`id_calibracion`) ON DELETE CASCADE ON UPDATE CASCADE");

        if ($this->db->tableExists('usuarios')) {
            $tUsu = $this->db->prefixTable('usuarios');
            $this->db->query("ALTER TABLE `{$tNotas}` ADD CONSTRAINT `fk_calibracion_notas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `{$tUsu}`(`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE");
        }
    }

    public function down()
    {
        $t = $this->db->prefixTable('calibracion_notas');
        try {
            $this->db->query("ALTER TABLE `{$t}` DROP FOREIGN KEY `fk_calibracion_notas_usuario`");
        } catch (\Throwable $e) {
        }
        try {
            $this->db->query("ALTER TABLE `{$t}` DROP FOREIGN KEY `fk_calibracion_notas_calibracion`");
        } catch (\Throwable $e) {
        }
        $this->forge->dropTable('calibracion_notas', true);
    }
}
