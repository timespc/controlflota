<?php

namespace App\Models;

use App\Models\BaseModel;

class ItemsCensoModel extends BaseModel
{
    protected $table = 'items_censo';
    protected $primaryKey = 'id_item_censo';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = ['item'];

    protected $validationRules = [
        'item' => 'required|min_length[1]|max_length[255]'
    ];

    protected $validationMessages = [
        'item' => [
            'required' => 'El campo Ítem es obligatorio',
            'min_length' => 'El campo Ítem debe tener al menos 1 carácter',
            'max_length' => 'El campo Ítem no puede exceder 255 caracteres'
        ]
    ];

    /**
     * Listar todos los ítems censo para DataTable
     */
    public function listarTodos()
    {
        return $this->select('id_item_censo, item, updated_at as ult_actualiz')
            ->orderBy('item', 'ASC')
            ->findAll();
    }

    /**
     * Obtener un ítem por ID
     */
    public function obtenerPorId($id)
    {
        return $this->select('id_item_censo, item, created_at, updated_at')
            ->find($id);
    }

    /**
     * Guardar o actualizar un ítem
     */
    public function guardarItemCenso(array $data)
    {
        $id = $data['id_item_censo'] ?? null;
        unset($data['id_item_censo']);

        if ($id && (int) $id > 0) {
            return $this->update($id, $data);
        }
        return $this->insert($data);
    }

    /**
     * Eliminar un ítem por ID
     */
    public function eliminarItemCenso($id)
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
