<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Añade ejes y cubiertas del semi trasero en unidades (simetría con Semi Delantera).
 */
class AddEjesCubiertasSemiTraseroUnidades extends Migration
{
    public function up()
    {
        $after = 'cubierta_unidad_eje3';
        if (! $this->db->fieldExists('ejes_semi_trasero', 'unidades')) {
            $this->forge->addColumn('unidades', [
                'ejes_semi_trasero' => [
                    'type'    => 'INT',
                    'constraint' => 2,
                    'null'    => true,
                    'default' => 0,
                    'after'   => $after,
                ],
            ]);
            $after = 'ejes_semi_trasero';
        }
        for ($i = 1; $i <= 3; $i++) {
            $col = 'cubierta_semi_trasero_eje' . $i;
            if (! $this->db->fieldExists($col, 'unidades')) {
                $this->forge->addColumn('unidades', [
                    $col => [
                        'type'       => 'INT',
                        'constraint' => 11,
                        'unsigned'   => true,
                        'null'       => true,
                        'after'      => $after,
                    ],
                ]);
                $after = $col;
            }
        }
    }

    public function down()
    {
        foreach (['cubierta_semi_trasero_eje3', 'cubierta_semi_trasero_eje2', 'cubierta_semi_trasero_eje1', 'ejes_semi_trasero'] as $col) {
            if ($this->db->fieldExists($col, 'unidades')) {
                $this->forge->dropColumn('unidades', $col);
            }
        }
    }
}
