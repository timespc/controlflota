<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\BaseModel;

class CubiertasModel extends BaseModel
{
    protected $table = 'cubiertas';
    protected $primaryKey = 'id_cubierta';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = ['medida'];

    protected $validationRules = [
        'medida' => 'required|min_length[1]|max_length[255]'
    ];

    protected $validationMessages = [
        'medida' => [
            'required' => 'El campo Medida es obligatorio',
            'min_length' => 'El campo Medida debe tener al menos 1 carácter',
            'max_length' => 'El campo Medida no puede exceder 255 caracteres'
        ]
    ];

    /**
     * Listar todas las cubiertas para DataTable
     */
    public function listarTodos()
    {
        return $this->select('id_cubierta, medida, updated_at as ult_actualiz')
            ->orderBy('medida', 'ASC')
            ->findAll();
    }

    /**
     * Obtener una cubierta por ID
     */
    public function obtenerPorId($id)
    {
        return $this->select('id_cubierta, medida, created_at, updated_at')
            ->find($id);
    }

    /**
     * Guardar o actualizar una cubierta
     */
    public function guardarCubierta(array $data)
    {
        return $this->store($data);
    }

    /**
     * Eliminar una cubierta por ID
     */
    public function eliminarCubierta($id)
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
