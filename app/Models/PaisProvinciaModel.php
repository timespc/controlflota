<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\BaseModel;

class PaisProvinciaModel extends BaseModel
{
    protected $table = 'paises_provincias';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    
    protected $allowedFields = ['nombre', 'pais_id'];

    /**
     * Obtener provincias por país
     */
    public function provinciasPorPaisId($pais_id)
    {
        return $this->where('pais_id', $pais_id)
                    ->orderBy('nombre', 'ASC')
                    ->findAll();
    }
}

