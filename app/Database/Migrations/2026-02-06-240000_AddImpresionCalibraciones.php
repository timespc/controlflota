<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImpresionCalibraciones extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('calibraciones')) {
            return;
        }

        if (! $this->db->fieldExists('fecha_impresion', 'calibraciones')) {
            $this->forge->addColumn('calibraciones', [
                'fecha_impresion' => [
                    'type'    => 'DATETIME',
                    'null'    => true,
                    'comment' => 'Fecha y hora de la primera impresión (original)'
                ]
            ]);
        }

        if (! $this->db->fieldExists('id_usuario_impresion', 'calibraciones')) {
            $this->forge->addColumn('calibraciones', [
                'id_usuario_impresion' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'comment'    => 'Usuario que realizó la primera impresión'
                ]
            ]);
            if ($this->db->tableExists('usuarios')) {
                $this->db->query('ALTER TABLE `' . $this->db->prefixTable('calibraciones') . '` ADD CONSTRAINT `fk_calibraciones_usuario_impresion` FOREIGN KEY (`id_usuario_impresion`) REFERENCES `' . $this->db->prefixTable('usuarios') . '`(`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE');
            }
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('calibraciones')) {
            return;
        }
        if ($this->db->fieldExists('id_usuario_impresion', 'calibraciones')) {
            $this->db->query('ALTER TABLE `' . $this->db->prefixTable('calibraciones') . '` DROP FOREIGN KEY `fk_calibraciones_usuario_impresion`');
            $this->forge->dropColumn('calibraciones', 'id_usuario_impresion');
        }
        if ($this->db->fieldExists('fecha_impresion', 'calibraciones')) {
            $this->forge->dropColumn('calibraciones', 'fecha_impresion');
        }
    }
}
