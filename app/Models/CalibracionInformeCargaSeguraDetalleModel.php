<?php
declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class CalibracionInformeCargaSeguraDetalleModel extends Model
{
    protected $table = 'calibracion_informe_carga_segura_detalle';
    protected $primaryKey = 'id_calibracion';
    protected $allowedFields = [
        'id_calibracion',
        'numero_cisterna',
        'volumen_lts',
        'vacio_requerido',
        'vacio_medido',
        'accion_tomada',
        'volumen_final_lts',
        'cumple_control',
        'marca_sensor',
        'numero_serie_sensor',
        'cumple_trazabilidad',
        'cumple_posicion',
        'observacion_posicion',
        'litros_sensor_rebalse',
    ];

    public function listarPorCalibracion(int $idCalibracion): array
    {
        return $this->where('id_calibracion', $idCalibracion)
            ->orderBy('numero_cisterna', 'ASC')
            ->findAll();
    }

    public function eliminarPorCalibracion(int $idCalibracion): bool
    {
        return $this->where('id_calibracion', $idCalibracion)->delete();
    }
}
