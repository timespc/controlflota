<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipoCodigoAxionToTransportistas extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('transportistas')) {
            return;
        }
        if (! $this->db->fieldExists('tipo', 'transportistas')) {
            $this->forge->addColumn('transportistas', [
                'tipo' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'comment'    => 'Tipo desde BD camiones (ttas)',
                ],
            ]);
        }
        if (! $this->db->fieldExists('codigo_axion', 'transportistas')) {
            $this->forge->addColumn('transportistas', [
                'codigo_axion' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'comment'    => 'CodAxion desde BD camiones (ttas)',
                ],
            ]);
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('transportistas')) {
            return;
        }
        if ($this->db->fieldExists('tipo', 'transportistas')) {
            $this->forge->dropColumn('transportistas', 'tipo');
        }
        if ($this->db->fieldExists('codigo_axion', 'transportistas')) {
            $this->forge->dropColumn('transportistas', 'codigo_axion');
        }
    }
}
