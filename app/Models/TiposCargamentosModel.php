<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Tipos de cargamentos (solo consulta e impresión).
 */
class TiposCargamentosModel extends BaseModel
{
    protected $table         = 'tipos_cargamentos';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;
    protected $returnType    = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = ['tipo', 'tipo_carga_abreviado'];

    /**
     * Lista todos los tipos para DataTable / impresión.
     * Devuelve: id, tipo, tipo_carga_abreviado, ult_actualiz (formato d/m/Y H:i:s).
     */
    public function listarTodos(): array
    {
        $rows = $this->select('id, tipo, tipo_carga_abreviado, updated_at')
            ->orderBy('tipo', 'ASC')
            ->findAll();
        foreach ($rows as &$r) {
            $r['ult_actualiz'] = $r['updated_at']
                ? date('d/m/Y H:i:s', strtotime($r['updated_at']))
                : '';
            unset($r['updated_at']);
        }
        return $rows;
    }
}
