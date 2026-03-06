<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\BaseModel;

class MarcasSensorModel extends BaseModel
{
    protected $table         = 'marcas_sensor';
    protected $primaryKey    = 'id_marca_sensor';
    protected $useAutoIncrement = true;
    protected $returnType    = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = ['marca'];

    protected $validationRules = [
        'marca' => 'required|min_length[1]|max_length[255]',
    ];

    protected $validationMessages = [
        'marca' => [
            'required'   => 'El campo Marca es obligatorio',
            'min_length' => 'El campo Marca debe tener al menos 1 carácter',
            'max_length' => 'El campo Marca no puede exceder 255 caracteres',
        ],
    ];

    /**
     * Listar todas las marcas de sensor (para DataTable y para selects).
     */
    public function listarTodos(): array
    {
        return $this->select('id_marca_sensor, marca, updated_at as ult_actualiz')
            ->orderBy('marca', 'ASC')
            ->findAll();
    }

    public function obtenerPorId($id): ?array
    {
        $r = $this->select('id_marca_sensor, marca, created_at, updated_at')->find($id);
        return $r ?: null;
    }

    public function guardarMarca(array $data)
    {
        return $this->store($data);
    }

    public function eliminarMarca($id)
    {
        return $this->delete($id);
    }

    public function obtenerTotal(): int
    {
        return (int) $this->countAllResults();
    }
}
