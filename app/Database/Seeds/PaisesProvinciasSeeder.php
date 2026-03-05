<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PaisesProvinciasSeeder extends Seeder
{
    public function run()
    {
        // Obtener IDs de países
        $db = \Config\Database::connect();
        
        $argentina = $db->table('paises')->where('nombre', 'Argentina')->get()->getRowArray();
        $chile = $db->table('paises')->where('nombre', 'Chile')->get()->getRowArray();
        $brasil = $db->table('paises')->where('nombre', 'Brasil')->get()->getRowArray();
        $bolivia = $db->table('paises')->where('nombre', 'Bolivia')->get()->getRowArray();
        
        // Verificar si ya hay datos
        $count = $db->table('paises_provincias')->countAllResults();
        if ($count > 0) {
            echo "Ya existen provincias en la base de datos.\n";
            return;
        }
        
        // Provincias de Argentina
        if ($argentina && isset($argentina['id'])) {
            $provincias_argentina = [
                ['nombre' => 'Buenos Aires', 'pais_id' => $argentina['id']],
                ['nombre' => 'Catamarca', 'pais_id' => $argentina['id']],
                ['nombre' => 'Chaco', 'pais_id' => $argentina['id']],
                ['nombre' => 'Chubut', 'pais_id' => $argentina['id']],
                ['nombre' => 'Córdoba', 'pais_id' => $argentina['id']],
                ['nombre' => 'Corrientes', 'pais_id' => $argentina['id']],
                ['nombre' => 'Entre Ríos', 'pais_id' => $argentina['id']],
                ['nombre' => 'Formosa', 'pais_id' => $argentina['id']],
                ['nombre' => 'Jujuy', 'pais_id' => $argentina['id']],
                ['nombre' => 'La Pampa', 'pais_id' => $argentina['id']],
                ['nombre' => 'La Rioja', 'pais_id' => $argentina['id']],
                ['nombre' => 'Mendoza', 'pais_id' => $argentina['id']],
                ['nombre' => 'Misiones', 'pais_id' => $argentina['id']],
                ['nombre' => 'Neuquén', 'pais_id' => $argentina['id']],
                ['nombre' => 'Río Negro', 'pais_id' => $argentina['id']],
                ['nombre' => 'Salta', 'pais_id' => $argentina['id']],
                ['nombre' => 'San Juan', 'pais_id' => $argentina['id']],
                ['nombre' => 'San Luis', 'pais_id' => $argentina['id']],
                ['nombre' => 'Santa Cruz', 'pais_id' => $argentina['id']],
                ['nombre' => 'Santa Fe', 'pais_id' => $argentina['id']],
                ['nombre' => 'Santiago del Estero', 'pais_id' => $argentina['id']],
                ['nombre' => 'Tierra del Fuego', 'pais_id' => $argentina['id']],
                ['nombre' => 'Tucumán', 'pais_id' => $argentina['id']]
            ];
            $db->table('paises_provincias')->insertBatch($provincias_argentina);
            echo "Provincias de Argentina insertadas: " . count($provincias_argentina) . "\n";
        }
        
        // Regiones de Chile
        if ($chile && isset($chile['id'])) {
            $regiones_chile = [
                ['nombre' => 'Arica y Parinacota', 'pais_id' => $chile['id']],
                ['nombre' => 'Tarapacá', 'pais_id' => $chile['id']],
                ['nombre' => 'Antofagasta', 'pais_id' => $chile['id']],
                ['nombre' => 'Atacama', 'pais_id' => $chile['id']],
                ['nombre' => 'Coquimbo', 'pais_id' => $chile['id']],
                ['nombre' => 'Valparaíso', 'pais_id' => $chile['id']],
                ['nombre' => 'Metropolitana', 'pais_id' => $chile['id']],
                ['nombre' => 'O\'Higgins', 'pais_id' => $chile['id']],
                ['nombre' => 'Maule', 'pais_id' => $chile['id']],
                ['nombre' => 'Ñuble', 'pais_id' => $chile['id']],
                ['nombre' => 'Biobío', 'pais_id' => $chile['id']],
                ['nombre' => 'Araucanía', 'pais_id' => $chile['id']],
                ['nombre' => 'Los Ríos', 'pais_id' => $chile['id']],
                ['nombre' => 'Los Lagos', 'pais_id' => $chile['id']],
                ['nombre' => 'Aysén', 'pais_id' => $chile['id']],
                ['nombre' => 'Magallanes', 'pais_id' => $chile['id']]
            ];
            $db->table('paises_provincias')->insertBatch($regiones_chile);
            echo "Regiones de Chile insertadas: " . count($regiones_chile) . "\n";
        }
        
        // Estados de Brasil (principales)
        if ($brasil && isset($brasil['id'])) {
            $estados_brasil = [
                ['nombre' => 'Acre', 'pais_id' => $brasil['id']],
                ['nombre' => 'Alagoas', 'pais_id' => $brasil['id']],
                ['nombre' => 'Amapá', 'pais_id' => $brasil['id']],
                ['nombre' => 'Amazonas', 'pais_id' => $brasil['id']],
                ['nombre' => 'Bahía', 'pais_id' => $brasil['id']],
                ['nombre' => 'Ceará', 'pais_id' => $brasil['id']],
                ['nombre' => 'Distrito Federal', 'pais_id' => $brasil['id']],
                ['nombre' => 'Espírito Santo', 'pais_id' => $brasil['id']],
                ['nombre' => 'Goiás', 'pais_id' => $brasil['id']],
                ['nombre' => 'Maranhão', 'pais_id' => $brasil['id']],
                ['nombre' => 'Mato Grosso', 'pais_id' => $brasil['id']],
                ['nombre' => 'Mato Grosso do Sul', 'pais_id' => $brasil['id']],
                ['nombre' => 'Minas Gerais', 'pais_id' => $brasil['id']],
                ['nombre' => 'Pará', 'pais_id' => $brasil['id']],
                ['nombre' => 'Paraíba', 'pais_id' => $brasil['id']],
                ['nombre' => 'Paraná', 'pais_id' => $brasil['id']],
                ['nombre' => 'Pernambuco', 'pais_id' => $brasil['id']],
                ['nombre' => 'Piauí', 'pais_id' => $brasil['id']],
                ['nombre' => 'Río de Janeiro', 'pais_id' => $brasil['id']],
                ['nombre' => 'Río Grande do Norte', 'pais_id' => $brasil['id']],
                ['nombre' => 'Río Grande do Sul', 'pais_id' => $brasil['id']],
                ['nombre' => 'Rondônia', 'pais_id' => $brasil['id']],
                ['nombre' => 'Roraima', 'pais_id' => $brasil['id']],
                ['nombre' => 'Santa Catarina', 'pais_id' => $brasil['id']],
                ['nombre' => 'São Paulo', 'pais_id' => $brasil['id']],
                ['nombre' => 'Sergipe', 'pais_id' => $brasil['id']],
                ['nombre' => 'Tocantins', 'pais_id' => $brasil['id']]
            ];
            $db->table('paises_provincias')->insertBatch($estados_brasil);
            echo "Estados de Brasil insertados: " . count($estados_brasil) . "\n";
        }
        
        // Departamentos de Bolivia (principales)
        if ($bolivia && isset($bolivia['id'])) {
            $departamentos_bolivia = [
                ['nombre' => 'Chuquisaca', 'pais_id' => $bolivia['id']],
                ['nombre' => 'La Paz', 'pais_id' => $bolivia['id']],
                ['nombre' => 'Cochabamba', 'pais_id' => $bolivia['id']],
                ['nombre' => 'Oruro', 'pais_id' => $bolivia['id']],
                ['nombre' => 'Potosí', 'pais_id' => $bolivia['id']],
                ['nombre' => 'Tarija', 'pais_id' => $bolivia['id']],
                ['nombre' => 'Santa Cruz', 'pais_id' => $bolivia['id']],
                ['nombre' => 'Beni', 'pais_id' => $bolivia['id']],
                ['nombre' => 'Pando', 'pais_id' => $bolivia['id']]
            ];
            $db->table('paises_provincias')->insertBatch($departamentos_bolivia);
            echo "Departamentos de Bolivia insertados: " . count($departamentos_bolivia) . "\n";
        }
        
        echo "Seeder completado.\n";
    }
}

