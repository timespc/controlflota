<?php

namespace App\Models;

use App\Models\BaseModel;

class ReglasModel extends BaseModel
{
    protected $table = 'reglas';
    protected $primaryKey = 'id_regla';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = ['numero_regla', 'habilitada', 'id_usuario_creacion'];

    protected $validationRules = [
        'numero_regla' => 'required|min_length[1]|max_length[100]',
        'habilitada'   => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'numero_regla' => [
            'required'   => 'El número de regla es obligatorio',
            'min_length' => 'El número de regla debe tener al menos 1 carácter',
            'max_length' => 'El número de regla no puede exceder 100 caracteres',
        ],
        'habilitada' => [
            'required'  => 'El estado habilitada es obligatorio',
            'in_list'   => 'Habilitada debe ser Sí o No',
        ],
    ];

    /**
     * Lista todas las reglas para DataTable (orden: habilitada primero, luego por id desc).
     * Incluye nombre/email del usuario que creó la regla (usuario_creacion_nombre).
     */
    public function listarTodos()
    {
        $db = $this->db;
        $reglas = $this->select('reglas.id_regla, reglas.numero_regla, reglas.habilitada, reglas.id_usuario_creacion, reglas.created_at, reglas.updated_at')
            ->orderBy('reglas.habilitada', 'DESC')
            ->orderBy('reglas.id_regla', 'DESC')
            ->findAll();

        if (! $db->tableExists('users') || empty($reglas)) {
            foreach ($reglas as &$r) {
                $r['usuario_creacion_nombre'] = null;
            }
            return $reglas;
        }

        $ids = array_filter(array_unique(array_column($reglas, 'id_usuario_creacion')));
        if (empty($ids)) {
            foreach ($reglas as &$r) {
                $r['usuario_creacion_nombre'] = null;
            }
            return $reglas;
        }

        $users = $db->table('users')
            ->select('id, first_name, last_name, email')
            ->whereIn('id', $ids)
            ->get()
            ->getResultArray();
        $map = [];
        foreach ($users as $u) {
            $nombre = trim(trim($u['first_name'] ?? '') . ' ' . trim($u['last_name'] ?? ''));
            $map[(int) $u['id']] = $nombre !== '' ? $nombre : ($u['email'] ?? '');
        }
        foreach ($reglas as &$r) {
            $r['usuario_creacion_nombre'] = $map[(int) ($r['id_usuario_creacion'] ?? 0)] ?? null;
        }
        return $reglas;
    }

    /**
     * Obtiene la regla actualmente habilitada (en uso). Solo hay una a la vez.
     */
    public function obtenerHabilitada()
    {
        return $this->where('habilitada', 1)->first();
    }

    /**
     * Obtiene una regla por ID.
     */
    public function obtenerPorId($id)
    {
        return $this->select('id_regla, numero_regla, habilitada, id_usuario_creacion, created_at, updated_at')
            ->find($id);
    }

    /**
     * Guarda o actualiza una regla. Si se marca como habilitada, deshabilita las demás.
     */
    public function guardarRegla(array $data)
    {
        $id = isset($data['id_regla']) ? (int) $data['id_regla'] : 0;
        unset($data['id_regla']);

        $habilitada = isset($data['habilitada']) ? (int) $data['habilitada'] : 0;
        if ($habilitada === 1) {
            $builder = $this->where('habilitada', 1);
            if ($id > 0) {
                $builder->where('id_regla !=', $id);
            }
            $builder->set(['habilitada' => 0])->update();
        }

        if ($id > 0) {
            return $this->update($id, $data);
        }
        return $this->insert($data);
    }

    /**
     * Elimina una regla por ID.
     */
    public function eliminarRegla($id)
    {
        return $this->delete($id);
    }

    /**
     * Total de registros.
     */
    public function obtenerTotal()
    {
        return $this->countAllResults();
    }
}
