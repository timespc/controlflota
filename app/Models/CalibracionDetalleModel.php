<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\BaseModel;

class CalibracionDetalleModel extends BaseModel
{
    protected $table = 'calibracion_detalle';
    protected $primaryKey = 'id_detalle';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = [
        'id_calibracion',
        'numero_semi',
        'numero_linea',
        'mflec',
        'capacidad',
        'enrase',
        'referen',
        'vacio_calc',
        'vacio_lts',
        'precinto_campana',
        'precinto_soporte',
        'precinto_hombre',
        'precinto_ultima'
    ];

    /**
     * Obtener detalle por id_calibracion ordenado por numero_linea.
     * Si $numeroSemi es null, devuelve todos; si es 1 o 2, filtra por ese semi.
     */
    public function listarPorCalibracion(int $idCalibracion, ?int $numeroSemi = null): array
    {
        $builder = $this->where('id_calibracion', $idCalibracion);
        if ($numeroSemi !== null) {
            $builder->where('numero_semi', $numeroSemi);
        }
        return $builder->orderBy('numero_semi', 'ASC')
            ->orderBy('numero_linea', 'ASC')
            ->findAll();
    }

    /**
     * Eliminar todas las líneas de una calibración.
     */
    public function eliminarPorCalibracion(int $idCalibracion): bool
    {
        return $this->where('id_calibracion', $idCalibracion)->delete();
    }
}
