<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCuitToTransportistas extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('transportistas')) {
            return;
        }
        if (! $this->db->fieldExists('cuit', 'transportistas')) {
            $this->forge->addColumn('transportistas', [
                'cuit' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'comment'    => 'CUIT del transportista',
                ],
            ]);
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('transportistas')) {
            return;
        }
        if ($this->db->fieldExists('cuit', 'transportistas')) {
            $this->forge->dropColumn('transportistas', 'cuit');
        }
    }
}
