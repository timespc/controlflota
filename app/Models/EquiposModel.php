<?php

namespace App\Models;

use App\Models\BaseModel;

class EquiposModel extends BaseModel
{
    protected $table = 'equipos';
    protected $primaryKey = 'id_equipo';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $allowedFields = [
        'patente_semi_delantero',
        'id_tta',
        'patente_tractor',
        'bitren',
        'patente_semi_trasero',
        'semi_trasero_anio_modelo',
        'semi_trasero_tara',
        'semi_trasero_pbt',
        'modo_carga',
        'pais_id',
        'nacion',
        'fecha_alta',
        'tractor_tara',
        'tractor_pbt',
        'tractor_anio_modelo',
        'tara_total',
        'peso_maximo',
        'cisterna_1_capacidad',
        'cisterna_2_capacidad',
        'cisterna_3_capacidad',
        'cisterna_4_capacidad',
        'cisterna_5_capacidad',
        'cisterna_6_capacidad',
        'cisterna_7_capacidad',
        'cisterna_8_capacidad',
        'cisterna_9_capacidad',
        'cisterna_10_capacidad',
        'capacidad_total',
        'id_bandera',
        'id_marca',
        'semi_delantero_anio_modelo',
        'semi_delantero_tara',
        'semi_delantera_pbt',
        'cubierta_tractor_eje1',
        'cubierta_tractor_eje2',
        'cubierta_tractor_eje3',
        'ejes_tractor',
        'cubierta_semi_delantero_eje1',
        'cubierta_semi_delantero_eje2',
        'cubierta_semi_delantero_eje3',
        'ejes_semi_delantero',
        'ejes_semi_trasero',
        'cubierta_semi_trasero_eje1',
        'cubierta_semi_trasero_eje2',
        'cubierta_semi_trasero_eje3',
        'cota_delantero',
        'cota_trasero',
        'observaciones',
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
    
    protected $validationRules = [
        'patente_semi_delantero' => 'required|max_length[20]',
        'patente_tractor' => 'required|max_length[20]',
    ];

    protected $validationMessages = [
        'patente_semi_delantero' => [
            'required' => 'La patente del semi delantero es obligatoria',
            'max_length' => 'La patente no puede exceder 20 caracteres'
        ],
        'patente_tractor' => [
            'required' => 'La patente del tractor es obligatoria',
            'max_length' => 'La patente del tractor no puede exceder 20 caracteres'
        ],
    ];
    
    /**
     * Lista todas las unidades para DataTable
     */
    public function listarTodos()
    {
        $db = \Config\Database::connect();
        
        // Construir campos SELECT
        $selectFields = ['equipos.*'];
        
        // JOIN con transportistas (si existe)
        if ($db->tableExists('transportistas')) {
            $selectFields[] = 'transportistas.transportista';
        } else {
            $selectFields[] = "NULL as transportista";
        }
        
        // JOIN con banderas (si existe)
        if ($db->tableExists('banderas')) {
            $selectFields[] = 'banderas.bandera as bandera_nombre';
        } else {
            $selectFields[] = "NULL as bandera_nombre";
        }
        
        // JOIN con marcas (si existe)
        if ($db->tableExists('marcas')) {
            $selectFields[] = 'marcas.marca as marca_nombre';
        } else {
            $selectFields[] = "NULL as marca_nombre";
        }
        
        $builder = $this->select($selectFields);
        
        // Hacer JOINs solo si las tablas existen
        if ($db->tableExists('transportistas')) {
            $builder->join('transportistas', 'transportistas.id_tta = equipos.id_tta', 'left');
        }
        if ($db->tableExists('banderas')) {
            $builder->join('banderas', 'banderas.id_bandera = equipos.id_bandera', 'left');
        }
        if ($db->tableExists('marcas')) {
            $builder->join('marcas', 'marcas.id_marca = equipos.id_marca', 'left');
        }
        
        return $builder->orderBy('equipos.id_equipo', 'DESC')->findAll();
    }

    /**
     * Lista unidades con filtros opcionales (transportista, patentes).
     * @param int|null $id_tta
     * @param string $patente_tractor
     * @param string $patente (semi 1)
     * @param string $patente_semi_trasero (semi 2)
     */
    public function listarConFiltros(?int $id_tta = null, string $patente_tractor = '', string $patente = '', string $patente_semi_trasero = ''): array
    {
        $db = \Config\Database::connect();
        $selectFields = ['equipos.*'];
        if ($db->tableExists('transportistas')) {
            $selectFields[] = 'transportistas.transportista';
        } else {
            $selectFields[] = "NULL as transportista";
        }
        if ($db->tableExists('banderas')) {
            $selectFields[] = 'banderas.bandera as bandera_nombre';
        } else {
            $selectFields[] = "NULL as bandera_nombre";
        }
        if ($db->tableExists('marcas')) {
            $selectFields[] = 'marcas.marca as marca_nombre';
        } else {
            $selectFields[] = "NULL as marca_nombre";
        }
        $builder = $this->select($selectFields);
        if ($db->tableExists('transportistas')) {
            $builder->join('transportistas', 'transportistas.id_tta = equipos.id_tta', 'left');
        }
        if ($db->tableExists('banderas')) {
            $builder->join('banderas', 'banderas.id_bandera = equipos.id_bandera', 'left');
        }
        if ($db->tableExists('marcas')) {
            $builder->join('marcas', 'marcas.id_marca = equipos.id_marca', 'left');
        }
        if ($id_tta !== null && $id_tta > 0) {
            $builder->where('equipos.id_tta', $id_tta);
        }
        $patente_tractor = trim($patente_tractor);
        if ($patente_tractor !== '') {
            $builder->like('equipos.patente_tractor', $patente_tractor, 'both');
        }
        $patente = trim($patente);
        if ($patente !== '') {
            $builder->like('equipos.patente_semi_delantero', $patente, 'both');
        }
        $patente_semi_trasero = trim($patente_semi_trasero);
        if ($patente_semi_trasero !== '') {
            $builder->like('equipos.patente_semi_trasero', $patente_semi_trasero, 'both');
        }
        return $builder->orderBy('equipos.id_equipo', 'DESC')->findAll();
    }
    
    /**
     * Obtiene una unidad por ID con relaciones
     */
    public function obtenerPorId($id)
    {
        $db = \Config\Database::connect();
        
        // Construir campos SELECT
        $selectFields = ['equipos.*'];
        
        // JOIN con transportistas (si existe)
        if ($db->tableExists('transportistas')) {
            $selectFields[] = 'transportistas.transportista';
        } else {
            $selectFields[] = "NULL as transportista";
        }
        
        // JOIN con banderas (si existe)
        if ($db->tableExists('banderas')) {
            $selectFields[] = 'banderas.bandera as bandera_nombre';
        } else {
            $selectFields[] = "NULL as bandera_nombre";
        }
        
        // JOIN con marcas (si existe)
        if ($db->tableExists('marcas')) {
            $selectFields[] = 'marcas.marca as marca_nombre';
        } else {
            $selectFields[] = "NULL as marca_nombre";
        }
        
        $builder = $this->select($selectFields);
        
        // Hacer JOINs solo si las tablas existen
        if ($db->tableExists('transportistas')) {
            $builder->join('transportistas', 'transportistas.id_tta = equipos.id_tta', 'left');
        }
        if ($db->tableExists('banderas')) {
            $builder->join('banderas', 'banderas.id_bandera = equipos.id_bandera', 'left');
        }
        if ($db->tableExists('marcas')) {
            $builder->join('marcas', 'marcas.id_marca = equipos.id_marca', 'left');
        }
        
        return $builder->where('equipos.id_equipo', $id)->first();
    }
    
    /**
     * Obtiene todas las unidades con relaciones (alias para compatibilidad)
     */
    public function getAllWithRelations()
    {
        return $this->listarTodos();
    }
    
    /**
     * Obtiene una unidad por ID con relaciones (alias para compatibilidad)
     */
    public function getWithRelations($id)
    {
        return $this->obtenerPorId($id);
    }

    /**
     * Lista patentes para autocompletado / selector.
     * Opcionalmente filtra por texto en patente.
     */
    public function listarPatentes(string $busqueda = ''): array
    {
        $builder = $this->select('id_equipo, patente_semi_delantero')
            ->orderBy('patente_semi_delantero', 'ASC');
        if ($busqueda !== '') {
            $builder->like('patente_semi_delantero', $busqueda, 'both');
        }
        return $builder->findAll();
    }

    /**
     * Lista unidades para reporte de flota (filtros opcionales por id_tta, bitren y nación).
     * Retorna array con patente, transportista, bitren, patente_tractor, patente_semi_trasero, tara_total, peso_maximo, capacidad_total, fecha_alta, modo_carga.
     */
    public function listarParaReporteFlota(?int $id_tta = null, ?string $bitren = null, ?string $nacion = null): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('equipos')
            ->select('equipos.patente_semi_delantero, equipos.patente_tractor, equipos.bitren, equipos.patente_semi_trasero, equipos.fecha_alta, equipos.modo_carga, equipos.nacion as nacion_unidad, equipos.tara_total, equipos.peso_maximo, equipos.capacidad_total')
            ->join('transportistas', 'transportistas.id_tta = equipos.id_tta', 'left')
            ->select('transportistas.transportista, transportistas.nacion as nacion_tta');
        if ($id_tta !== null) {
            $builder->where('equipos.id_tta', $id_tta);
        }
        if ($bitren !== null && ($bitren === 'SI' || $bitren === 'NO')) {
            $builder->where('equipos.bitren', $bitren);
        }
        if ($nacion !== null && $nacion !== '') {
            $esc = $db->escape($nacion);
            $builder->where("(COALESCE(NULLIF(TRIM(equipos.nacion),''), transportistas.nacion) = {$esc})", null, false);
        }
        $rows = $builder->orderBy('equipos.patente_semi_delantero', 'ASC')->get()->getResultArray();
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'patente'               => $r['patente_semi_delantero'] ?? '',
                'transportista'         => $r['transportista'] ?? '',
                'bitren'                => $r['bitren'] ?? '',
                'fecha_alta'            => $r['fecha_alta'] ?? '',
                'modo_carga'            => $r['modo_carga'] ?? '',
                'nacion'                => (($r['nacion_unidad'] ?? '') !== '' ? $r['nacion_unidad'] : ($r['nacion_tta'] ?? '')),
                'tractor_patente'       => $r['patente_tractor'] ?? '',
                'semi_delan_patente'    => $r['patente_semi_delantero'] ?? '',
                'semi_trasero_patente'  => $r['patente_semi_trasero'] ?? '',
                'tara_total'            => $r['tara_total'] ?? '',
                'peso_maximo'           => $r['peso_maximo'] ?? '',
                'capacidad_total'       => $r['capacidad_total'] ?? '',
            ];
        }
        return $out;
    }
}
