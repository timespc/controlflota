<?php

namespace App\Models;

use App\Models\BaseModel;

class BanderasModel extends BaseModel
{
    protected $table = 'banderas';
    protected $primaryKey = 'id_bandera';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = ['bandera'];

    protected $validationRules = [
        'bandera' => 'required|min_length[1]|max_length[255]'
    ];

    protected $validationMessages = [
        'bandera' => [
            'required' => 'El campo Bandera es obligatorio',
            'min_length' => 'El campo Bandera debe tener al menos 1 carácter',
            'max_length' => 'El campo Bandera no puede exceder 255 caracteres'
        ]
    ];

    /**
     * Listar todas las banderas para DataTable
     */
    public function listarTodos()
    {
        return $this->select('id_bandera, bandera, updated_at as ult_actualiz')
            ->orderBy('bandera', 'ASC')
            ->findAll();
    }

    /**
     * Obtener una bandera por ID
     */
    public function obtenerPorId($id)
    {
        return $this->select('id_bandera, bandera, created_at, updated_at')
            ->find($id);
    }

    /**
     * Guardar o actualizar una bandera
     */
    public function guardarBandera(array $data)
    {
        $id = $data['id_bandera'] ?? null;
        unset($data['id_bandera']);

        if ($id && (int) $id > 0) {
            return $this->update($id, $data);
        }
        return $this->insert($data);
    }

    /**
     * Eliminar una bandera por ID
     */
    public function eliminarBandera($id)
    {
        return $this->delete($id);
    }

    /**
     * Total de registros
     */
    public function obtenerTotal()
    {
        return $this->countAllResults();
    }
}
