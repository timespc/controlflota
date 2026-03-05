<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCamposCalibracionesYDetalle extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('calibraciones')) {
            return;
        }

        $campos = [
            'temp_agua' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'comment' => 'Temperatura del agua'
            ],
            'valvulas' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'comment' => 'ABIERTAS o CERRADAS'
            ],
            'observaciones' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'tipo_unidad' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'comment' => 'CHASIS, ACOPLADO, SEMI'
            ],
            'multi_flecha' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => true,
                'comment' => 'SI o NO'
            ],
            'n_regla' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true
            ]
        ];

        foreach ($campos as $nombre => $def) {
            if (! $this->db->fieldExists($nombre, 'calibraciones')) {
                $this->forge->addColumn('calibraciones', [$nombre => $def]);
            }
        }

        // Tabla calibracion_detalle (una fila por línea de la tabla del formulario)
        if (! $this->db->tableExists('calibracion_detalle')) {
            $this->forge->addField([
                'id_detalle' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                    'null' => false
                ],
                'id_calibracion' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => false
                ],
                'numero_linea' => [
                    'type' => 'INT',
                    'constraint' => 3,
                    'null' => true,
                    'comment' => 'Cis. Nº (1, 2, 3...)'
                ],
                'mflec' => [
                    'type' => 'VARCHAR',
                    'constraint' => 5,
                    'null' => true,
                    'comment' => 'SI/NO multiflecha'
                ],
                'capacidad' => [
                    'type' => 'DECIMAL',
                    'constraint' => '12,2',
                    'null' => true,
                    'default' => 0
                ],
                'enrase' => [
                    'type' => 'DECIMAL',
                    'constraint' => '12,2',
                    'null' => true,
                    'default' => 0
                ],
                'referen' => [
                    'type' => 'DECIMAL',
                    'constraint' => '12,2',
                    'null' => true,
                    'default' => 0
                ],
                'vacio_calc' => [
                    'type' => 'DECIMAL',
                    'constraint' => '12,2',
                    'null' => true
                ],
                'vacio_lts' => [
                    'type' => 'DECIMAL',
                    'constraint' => '12,2',
                    'null' => true
                ],
                'precinto_campana' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true
                ],
                'precinto_soporte' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true
                ],
                'precinto_hombre' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true
                ],
                'precinto_ultima' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true
                ],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true]
            ]);
            $this->forge->addKey('id_detalle', true);
            $this->forge->addKey('id_calibracion');
            $this->forge->addForeignKey('id_calibracion', 'calibraciones', 'id_calibracion', 'CASCADE', 'CASCADE');
            $this->forge->createTable('calibracion_detalle', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('calibracion_detalle')) {
            $this->forge->dropTable('calibracion_detalle', true);
        }
        $campos = ['temp_agua', 'valvulas', 'observaciones', 'tipo_unidad', 'multi_flecha', 'n_regla'];
        foreach ($campos as $nombre) {
            if ($this->db->fieldExists($nombre, 'calibraciones')) {
                $this->forge->dropColumn('calibraciones', $nombre);
            }
        }
    }
}
