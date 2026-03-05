<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaisProvinciaToTransportistas extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('transportistas')) {
            return;
        }
        $fields = [
            'pais_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'nacion'
            ],
            'provincia_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'pais_id'
            ]
        ];
        if (! $this->db->fieldExists('pais_id', 'transportistas')) {
            $this->forge->addColumn('transportistas', ['pais_id' => $fields['pais_id']]);
        }
        if (! $this->db->fieldExists('provincia_id', 'transportistas')) {
            $this->forge->addColumn('transportistas', ['provincia_id' => $fields['provincia_id']]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('transportistas', ['pais_id', 'provincia_id']);
    }
}

