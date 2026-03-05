<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Documentación adjunta a Transportistas y Unidades (imágenes, PDFs, etc.).
 */
class DocumentosTransportistasUnidades extends Migration
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
            'tipo_entidad' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'null'       => false,
                'comment'    => 'transportista | unidad',
            ],
            'id_entidad' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'nombre_original' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'nombre_archivo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'comment'    => 'Nombre guardado en disco',
            ],
            'extension' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'mime_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'tamano' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['tipo_entidad', 'id_entidad']);
        $this->forge->createTable('documentos');
    }

    public function down()
    {
        $this->forge->dropTable('documentos', true);
    }
}
