<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Detalle multiflecha por cisterna (compartimientos internos).
 * Equivalente a cisternas_multi del sistema viejo.
 */
class CalibracionMultiflechaModel extends BaseModel
{
    protected $table = 'calibracion_multiflecha';
    /** Clave compuesta (id_calibracion, numero_linea, numero_multiflecha); no usar find() por ID único */
    protected $primaryKey = 'id_calibracion';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /** @var list<string> */
    protected $allowedFields = [
        'id_calibracion',
        'numero_semi',
        'numero_linea',
        'numero_multiflecha',
        'capacidad',
        'enrase',
        'referen',
        'vacio_calc',
        'vacio_lts',
        'precinto_campana',
        'precinto_soporte',
        'precinto_hombre'
    ];

    /**
     * Obtener todas las filas de multiflecha de una calibración (para impresión tarjeta derecha).
     * Si $numeroSemi es null, devuelve todos; si es 1 o 2, filtra por ese semi.
     *
     * @return list<array<string, mixed>>
     */
    public function listarPorCalibracion(int $idCalibracion, ?int $numeroSemi = null): array
    {
        $builder = $this->where('id_calibracion', $idCalibracion);
        if ($numeroSemi !== null) {
            $builder->where('numero_semi', $numeroSemi);
        }
        return $builder->orderBy('numero_linea', 'ASC')
            ->orderBy('numero_multiflecha', 'ASC')
            ->findAll();
    }

    /**
     * Obtener filas de multiflecha para una calibración y cisterna dada.
     *
     * @return list<array<string, mixed>>
     */
    public function listarPorCalibracionYCisterna(int $idCalibracion, int $numeroLinea, int $numeroSemi = 1): array
    {
        return $this->where('id_calibracion', $idCalibracion)
            ->where('numero_semi', $numeroSemi)
            ->where('numero_linea', $numeroLinea)
            ->orderBy('numero_multiflecha', 'ASC')
            ->findAll();
    }

    /**
     * Eliminar todas las filas de multiflecha de una cisterna (y semi dado).
     */
    public function eliminarPorCalibracionYCisterna(int $idCalibracion, int $numeroLinea, int $numeroSemi = 1): bool
    {
        return $this->where('id_calibracion', $idCalibracion)
            ->where('numero_semi', $numeroSemi)
            ->where('numero_linea', $numeroLinea)
            ->delete();
    }

    /**
     * Eliminar todas las filas de multiflecha de una calibración.
     */
    public function eliminarPorCalibracion(int $idCalibracion): bool
    {
        return $this->where('id_calibracion', $idCalibracion)->delete();
    }
}
