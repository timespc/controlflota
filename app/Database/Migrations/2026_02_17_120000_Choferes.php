<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabla choferes: documento, nombre, nacionalidad, transportista, comentarios.
 * No se incluyen vto_art ni vto_lnh.
 */
class Choferes extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'documento' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'DNI, Cédula, etc.',
            ],
            'nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'comment'    => 'Nombre del chofer',
            ],
            'id_nacion' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Nacionalidad (FK naciones)',
            ],
            'id_tta' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Transportista (FK transportistas)',
            ],
            'comentarios' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addKey('id', true);
        $this->forge->addKey('documento');
        $this->forge->addKey('id_nacion');
        $this->forge->addKey('id_tta');
        $this->forge->createTable('choferes');
    }

    public function down()
    {
        $this->forge->dropTable('choferes', true);
    }
}
