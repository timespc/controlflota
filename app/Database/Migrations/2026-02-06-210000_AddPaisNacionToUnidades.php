<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Añade pais_id y nacion a unidades (como en equipos) para poder elegir
 * y guardar la nación a nivel de unidad.
 */
class AddPaisNacionToUnidades extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('pais_id', 'unidades')) {
            $this->forge->addColumn('unidades', [
                'pais_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'modo_carga',
                ],
            ]);
        }
        if (! $this->db->fieldExists('nacion', 'unidades')) {
            $this->forge->addColumn('unidades', [
                'nacion' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'pais_id',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('nacion', 'unidades')) {
            $this->forge->dropColumn('unidades', 'nacion');
        }
        if ($this->db->fieldExists('pais_id', 'unidades')) {
            $this->forge->dropColumn('unidades', 'pais_id');
        }
    }
}
