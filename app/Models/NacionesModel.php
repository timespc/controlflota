<?php

namespace App\Models;

use App\Models\BaseModel;

class NacionesModel extends BaseModel
{
    protected $table = 'naciones';
    protected $primaryKey = 'id_nacion';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = ['nacion'];

    protected $validationRules = [
        'nacion' => 'required|min_length[1]|max_length[255]'
    ];

    protected $validationMessages = [
        'nacion' => [
            'required' => 'El campo Nación es obligatorio',
            'min_length' => 'El campo Nación debe tener al menos 1 carácter',
            'max_length' => 'El campo Nación no puede exceder 255 caracteres'
        ]
    ];

    /**
     * Listar todas las naciones para DataTable
     */
    public function listarTodos()
    {
        return $this->select('id_nacion, nacion, updated_at as ult_actualiz')
            ->orderBy('nacion', 'ASC')
            ->findAll();
    }

    /**
     * Obtener una nación por ID
     */
    public function obtenerPorId($id)
    {
        return $this->select('id_nacion, nacion, created_at, updated_at')
            ->find($id);
    }

    /**
     * Guardar o actualizar una nación
     */
    public function guardarNacion(array $data)
    {
        $id = $data['id_nacion'] ?? null;
        unset($data['id_nacion']);

        if ($id && (int) $id > 0) {
            return $this->update($id, $data);
        }
        return $this->insert($data);
    }

    /**
     * Eliminar una nación por ID
     */
    public function eliminarNacion($id)
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
