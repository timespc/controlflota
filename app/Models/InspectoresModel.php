<?php

namespace App\Models;

use App\Models\BaseModel;

class InspectoresModel extends BaseModel
{
    protected $table = 'inspectores';
    protected $primaryKey = 'id_inspector';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = ['inspector'];

    protected $validationRules = [
        'inspector' => 'required|min_length[1]|max_length[255]'
    ];

    protected $validationMessages = [
        'inspector' => [
            'required' => 'El campo Inspector es obligatorio',
            'min_length' => 'El campo Inspector debe tener al menos 1 carácter',
            'max_length' => 'El campo Inspector no puede exceder 255 caracteres'
        ]
    ];

    /**
     * Listar todos los inspectores para DataTable
     */
    public function listarTodos()
    {
        return $this->select('id_inspector, inspector, updated_at as ult_actualiz')
            ->orderBy('inspector', 'ASC')
            ->findAll();
    }

    /**
     * Obtener un inspector por ID
     */
    public function obtenerPorId($id)
    {
        return $this->select('id_inspector, inspector, created_at, updated_at')
            ->find($id);
    }

    /**
     * Guardar o actualizar un inspector
     */
    public function guardarInspector(array $data)
    {
        $id = $data['id_inspector'] ?? null;
        unset($data['id_inspector']);

        if ($id && (int) $id > 0) {
            return $this->update($id, $data);
        }
        return $this->insert($data);
    }

    /**
     * Eliminar un inspector por ID
     */
    public function eliminarInspector($id)
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
