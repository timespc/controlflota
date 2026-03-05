<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Soporte para 2 semis en una calibración:
 * - calibracion_detalle: numero_semi (1 = primer semi, 2 = segundo semi). Default 1.
 * - calibraciones: multi_flecha_semi2 (SI/NO) para el 2do semi.
 * - calibracion_multiflecha: numero_semi (1 o 2). PK pasa a (id_calibracion, numero_semi, numero_linea, numero_multiflecha).
 */
class AddNumeroSemiDetalleYMultiflecha extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('calibracion_detalle') && ! $this->db->fieldExists('numero_semi', 'calibracion_detalle')) {
            $this->forge->addColumn('calibracion_detalle', [
                'numero_semi' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'null'       => false,
                    'default'    => 1,
                    'after'      => 'id_calibracion',
                    'comment'    => '1 = primer semi, 2 = segundo semi'
                ]
            ]);
        }

        if ($this->db->tableExists('calibraciones') && ! $this->db->fieldExists('multi_flecha_semi2', 'calibraciones')) {
            $this->forge->addColumn('calibraciones', [
                'multi_flecha_semi2' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 5,
                    'null'       => true,
                    'comment'    => 'SI/NO multiflecha para 2do semi'
                ]
            ]);
        }

        if (! $this->db->tableExists('calibracion_multiflecha')) {
            return;
        }
        if ($this->db->fieldExists('numero_semi', 'calibracion_multiflecha')) {
            return;
        }

        $tblMf = $this->db->prefixTable('calibracion_multiflecha');
        $fks = $this->db->getForeignKeyData($tblMf);
        foreach ($fks as $name => $obj) {
            $this->forge->dropForeignKey('calibracion_multiflecha', $name);
        }
        $this->forge->dropPrimaryKey('calibracion_multiflecha');
        $this->forge->addColumn('calibracion_multiflecha', [
            'numero_semi' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 1,
                'after'      => 'id_calibracion',
                'comment'    => '1 = primer semi, 2 = segundo semi'
            ]
        ]);
        $this->db->query("UPDATE {$tblMf} SET numero_semi = 1");
        $this->db->query("ALTER TABLE {$tblMf} ADD PRIMARY KEY (id_calibracion, numero_semi, numero_linea, numero_multiflecha)");
        $tblCal = $this->db->prefixTable('calibraciones');
        $this->db->query("ALTER TABLE {$tblMf} ADD CONSTRAINT {$tblMf}_ibfk_1 FOREIGN KEY (id_calibracion) REFERENCES {$tblCal}(id_calibracion) ON DELETE CASCADE ON UPDATE CASCADE");
    }

    public function down()
    {
        if ($this->db->tableExists('calibracion_detalle') && $this->db->fieldExists('numero_semi', 'calibracion_detalle')) {
            $this->forge->dropColumn('calibracion_detalle', 'numero_semi');
        }
        if ($this->db->tableExists('calibraciones') && $this->db->fieldExists('multi_flecha_semi2', 'calibraciones')) {
            $this->forge->dropColumn('calibraciones', 'multi_flecha_semi2');
        }
        if (! $this->db->tableExists('calibracion_multiflecha') || ! $this->db->fieldExists('numero_semi', 'calibracion_multiflecha')) {
            return;
        }
        $tblMf = $this->db->prefixTable('calibracion_multiflecha');
        $fks = $this->db->getForeignKeyData($tblMf);
        foreach ($fks as $name => $obj) {
            $this->forge->dropForeignKey('calibracion_multiflecha', $name);
        }
        $this->forge->dropPrimaryKey('calibracion_multiflecha');
        $this->forge->dropColumn('calibracion_multiflecha', 'numero_semi');
        $this->db->query("ALTER TABLE {$tblMf} ADD PRIMARY KEY (id_calibracion, numero_linea, numero_multiflecha)");
        $tblCal = $this->db->prefixTable('calibraciones');
        $this->db->query("ALTER TABLE {$tblMf} ADD CONSTRAINT {$tblMf}_ibfk_1 FOREIGN KEY (id_calibracion) REFERENCES {$tblCal}(id_calibracion) ON DELETE CASCADE ON UPDATE CASCADE");
    }
}
