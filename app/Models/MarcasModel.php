<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\BaseModel;

class MarcasModel extends BaseModel
{
    protected $table = 'marcas';
    protected $primaryKey = 'id_marca';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = ['marca'];

    protected $validationRules = [
        'marca' => 'required|min_length[1]|max_length[255]'
    ];

    protected $validationMessages = [
        'marca' => [
            'required' => 'El campo Marca es obligatorio',
            'min_length' => 'El campo Marca debe tener al menos 1 carácter',
            'max_length' => 'El campo Marca no puede exceder 255 caracteres'
        ]
    ];

    /**
     * Listar todas las marcas para DataTable
     */
    public function listarTodos()
    {
        return $this->select('id_marca, marca, updated_at as ult_actualiz')
            ->orderBy('marca', 'ASC')
            ->findAll();
    }

    /**
     * Obtener una marca por ID
     */
    public function obtenerPorId($id)
    {
        return $this->select('id_marca, marca, created_at, updated_at')
            ->find($id);
    }

    /**
     * Guardar o actualizar una marca
     */
    public function guardarMarca(array $data)
    {
        return $this->store($data);
    }

    /**
     * Eliminar una marca por ID
     */
    public function eliminarMarca($id)
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
