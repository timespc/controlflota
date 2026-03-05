<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdUsuarioCreacionReglas extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('reglas')) {
            return;
        }

        if (! $this->db->fieldExists('id_usuario_creacion', 'reglas')) {
            $this->forge->addColumn('reglas', [
                'id_usuario_creacion' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'comment'    => 'Usuario que creó la regla',
                ],
            ]);
            if ($this->db->tableExists('usuarios')) {
                $this->db->query('ALTER TABLE `' . $this->db->prefixTable('reglas') . '` ADD CONSTRAINT `fk_reglas_usuario_creacion` FOREIGN KEY (`id_usuario_creacion`) REFERENCES `' . $this->db->prefixTable('usuarios') . '`(`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE');
            }
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('reglas')) {
            return;
        }
        if ($this->db->fieldExists('id_usuario_creacion', 'reglas')) {
            $this->db->query('ALTER TABLE `' . $this->db->prefixTable('reglas') . '` DROP FOREIGN KEY `fk_reglas_usuario_creacion`');
            $this->forge->dropColumn('reglas', 'id_usuario_creacion');
        }
    }
}
