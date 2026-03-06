<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\BaseModel;

class PaisModel extends BaseModel
{
    protected $table = 'paises';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    
    protected $allowedFields = ['nombre'];

    /**
     * Obtener todos los países ordenados por nombre
     */
    public function obtenerTodos()
    {
        return $this->orderBy('nombre', 'ASC')->findAll();
    }
}

