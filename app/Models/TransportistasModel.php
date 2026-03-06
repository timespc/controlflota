<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\BaseModel;

class TransportistasModel extends BaseModel
{
    protected $table = 'transportistas';
    protected $primaryKey = 'id_tta';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $allowedFields = [
        'transportista',
        'cuit',
        'tipo',
        'codigo_axion',
        'direccion',
        'localidad',
        'codigo_postal',
        'provincia',
        'nacion',
        'pais_id',
        'provincia_id',
        'mail_contacto',
        'telefono',
        'comentarios'
    ];

    protected $validationRules = [
        'transportista' => 'required|min_length[3]|max_length[255]',
        'mail_contacto' => 'permit_empty|valid_email_multiple'
    ];

    protected $validationMessages = [
        'transportista' => [
            'required' => 'El campo Transportista es obligatorio',
            'min_length' => 'El campo Transportista debe tener al menos 3 caracteres',
            'max_length' => 'El campo Transportista no puede exceder 255 caracteres'
        ],
        'mail_contacto' => [
            'valid_email_multiple' => 'Uno o más emails no son válidos. Separe múltiples emails con ";"'
        ]
    ];

    /**
     * Obtener todos los transportistas para DataTable
     */
    public function listarTodos()
    {
        return $this->select('
            id_tta,
            transportista,
            tipo,
            codigo_axion,
            direccion,
            localidad,
            codigo_postal,
            provincia,
            nacion,
            telefono,
            mail_contacto,
            comentarios,
            updated_at as ult_actualiz
        ')
        ->orderBy('transportista', 'ASC')
        ->findAll();
    }

    /**
     * Obtener un transportista por ID
     */
    public function obtenerPorId($id)
    {
        $result = $this->select('
            id_tta,
            transportista,
            cuit,
            tipo,
            codigo_axion,
            direccion,
            localidad,
            codigo_postal,
            provincia,
            nacion,
            pais_id,
            provincia_id,
            mail_contacto,
            telefono,
            comentarios,
            DATE_FORMAT(updated_at, "%d/%m/%Y %H:%i:%s") as ult_actualiz
        ')
        ->where('id_tta', $id)
        ->first();

        return $result;
    }

    /**
     * Listar transportistas para reporte: id_tta, transportista, localidad, provincia, nacion, cant_equipos.
     * @param string|null $cantEquiposFiltro '' = todos, '5' = hasta 5, '5-10' = entre 5 y 10, '10-15' = entre 10 y 15, '15plus' = +15
     */
    public function listarParaReporte(?string $cantEquiposFiltro = null): array
    {
        $rows = $this->select('id_tta, transportista, direccion, localidad, provincia, nacion')
            ->orderBy('transportista', 'ASC')
            ->findAll();

        $db = \Config\Database::connect();
        $equiposCount = [];
        if ($db->tableExists('equipos')) {
            $u = $db->table('equipos')->select('id_tta')->selectCount('id_equipo', 'total')->groupBy('id_tta')->get()->getResultArray();
            foreach ($u as $r) {
                $equiposCount[(int) $r['id_tta']] = (int) $r['total'];
            }
        }

        $out = [];
        foreach ($rows as $r) {
            $id = (int) $r['id_tta'];
            $cant = $equiposCount[$id] ?? 0;
            if ($cantEquiposFiltro !== null && $cantEquiposFiltro !== '') {
                $ok = false;
                if ($cantEquiposFiltro === '5') {
                    $ok = $cant <= 5;
                } elseif ($cantEquiposFiltro === '5-10') {
                    $ok = $cant >= 5 && $cant <= 10;
                } elseif ($cantEquiposFiltro === '10-15') {
                    $ok = $cant >= 10 && $cant <= 15;
                } elseif ($cantEquiposFiltro === '15plus') {
                    $ok = $cant > 15;
                }
                if (! $ok) {
                    continue;
                }
            }
            $out[] = [
                'id_tta'        => $id,
                'transportista' => $r['transportista'] ?? '',
                'direccion'     => $r['direccion'] ?? '',
                'localidad'     => $r['localidad'] ?? '',
                'provincia'     => $r['provincia'] ?? '',
                'nacion'        => $r['nacion'] ?? '',
                'cant_equipos'  => $cant,
            ];
        }
        return $out;
    }

    /**
     * Listar solo transportistas que tienen al menos un equipo, con cantidad de equipos.
     * Para dropdown de filtro en Equipos.
     */
    public function listarSoloConEquipos(): array
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('equipos')) {
            return [];
        }
        return $db->table('transportistas t')
            ->select('t.id_tta, t.transportista, COUNT(e.id_equipo) AS cantidad_equipos')
            ->join('equipos e', 'e.id_tta = t.id_tta')
            ->groupBy('t.id_tta', 't.transportista')
            ->orderBy('t.transportista', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Buscar transportistas por nombre
     */
    public function buscarPorNombre($nombre)
    {
        return $this->like('transportista', $nombre)
                    ->orderBy('transportista', 'ASC')
                    ->findAll();
    }

    /**
     * Buscar transportistas por número de TTA
     */
    public function buscarPorNumero($numero)
    {
        return $this->where('id_tta', $numero)->first();
    }

    /**
     * Guardar o actualizar un transportista
     */
    public function guardarTransportista($data)
    {
        if (isset($data['id_tta']) && !empty($data['id_tta'])) {
            // Actualizar
            $id = $data['id_tta'];
            unset($data['id_tta']);
            unset($data['ult_actualiz']); // No actualizar este campo manualmente
            
            return $this->update($id, $data);
        } else {
            // Crear nuevo
            unset($data['id_tta']);
            unset($data['ult_actualiz']);
            
            return $this->insert($data);
        }
    }

    /**
     * Eliminar un transportista
     */
    public function eliminarTransportista($id)
    {
        return $this->delete($id);
    }

    /**
     * Obtener el total de transportistas
     */
    public function obtenerTotal()
    {
        return $this->countAllResults();
    }

    /**
     * Obtener el siguiente ID disponible
     */
    public function obtenerSiguienteId()
    {
        $query = $this->selectMax('id_tta')->first();
        return $query ? ($query['id_tta'] + 1) : 1;
    }
}

