<?php

namespace App\Models;

use App\Models\BaseModel;

class RolModel extends BaseModel
{
    protected $table = 'roles';
    protected $primaryKey = 'id_rol';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = ['nombre', 'es_default'];

    /**
     * ID del rol por defecto para nuevos usuarios (ej. login con Gmail).
     */
    public function getIdRolDefault(): int
    {
        $row = $this->where('es_default', 1)->first();
        return $row ? (int) $row['id_rol'] : 3;
    }

    /**
     * Lista de roles para selects (id_rol, nombre).
     */
    public function listarParaSelect(): array
    {
        $rows = $this->orderBy('id_rol', 'ASC')->findAll();
        $out = [];
        foreach ($rows as $r) {
            $out[] = ['id_rol' => (int) $r['id_rol'], 'nombre' => $r['nombre']];
        }
        return $out;
    }
}
