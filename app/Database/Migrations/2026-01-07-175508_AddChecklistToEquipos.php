<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddChecklistToEquipos extends Migration
{
    private array $campos = [
        'checklist_asfalto',
        'checklist_alcohol',
        'checklist_biodiesel',
        'checklist_comb_liv',
        'checklist_comb_pes',
        'checklist_solvente',
        'checklist_coke',
        'checklist_lubes_gra',
        'checklist_lubes_env',
        'checklist_glp',
    ];

    public function up(): void
    {
        $fields = [];
        foreach ($this->campos as $campo) {
            $fields[$campo] = [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
            ];
        }
        $this->forge->addColumn('equipos', $fields);
    }

    public function down(): void
    {
        $this->forge->dropColumn('equipos', $this->campos);
    }
}
