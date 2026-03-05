<?php

namespace App\Models;

use App\Models\BaseModel;

class CalibradoresModel extends BaseModel
{
    protected $table = 'calibradores';
    protected $primaryKey = 'id_calibrador';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = ['calibrador'];

    protected $validationRules = [
        'calibrador' => 'required|min_length[1]|max_length[255]'
    ];

    protected $validationMessages = [
        'calibrador' => [
            'required' => 'El campo Calibrador es obligatorio',
            'min_length' => 'El campo Calibrador debe tener al menos 1 carácter',
            'max_length' => 'El campo Calibrador no puede exceder 255 caracteres'
        ]
    ];

    /**
     * Listar todos los calibradores para DataTable
     */
    public function listarTodos()
    {
        return $this->select('id_calibrador, calibrador, updated_at as ult_actualiz')
            ->orderBy('calibrador', 'ASC')
            ->findAll();
    }

    /**
     * Obtener un calibrador por ID
     */
    public function obtenerPorId($id)
    {
        return $this->select('id_calibrador, calibrador, created_at, updated_at')
            ->find($id);
    }

    /**
     * Guardar o actualizar un calibrador
     */
    public function guardarCalibrador(array $data)
    {
        $id = $data['id_calibrador'] ?? null;
        unset($data['id_calibrador']);

        if ($id && (int) $id > 0) {
            return $this->update($id, $data);
        }
        return $this->insert($data);
    }

    /**
     * Eliminar un calibrador por ID
     */
    public function eliminarCalibrador($id)
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
