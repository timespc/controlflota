<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CalibracionReimpresiones extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_reimpresion' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
                'null'           => false
            ],
            'id_calibracion' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'Calibración reimpresa'
            ],
            'id_usuario' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'Usuario que realizó la reimpresión'
            ],
            'mensaje' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Motivo / por qué reimprimió'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Fecha y hora de la reimpresión'
            ]
        ]);

        $this->forge->addKey('id_reimpresion', true);
        $this->forge->addKey('id_calibracion');
        $this->forge->addKey('id_usuario');

        if ($this->db->tableExists('calibraciones')) {
            $this->forge->addForeignKey('id_calibracion', 'calibraciones', 'id_calibracion', 'CASCADE', 'CASCADE');
        }
        if ($this->db->tableExists('usuarios')) {
            $this->forge->addForeignKey('id_usuario', 'usuarios', 'id_usuario', 'CASCADE', 'CASCADE');
        }

        $this->forge->createTable('calibracion_reimpresiones', true);
    }

    public function down()
    {
        $this->forge->dropTable('calibracion_reimpresiones', true);
    }
}
