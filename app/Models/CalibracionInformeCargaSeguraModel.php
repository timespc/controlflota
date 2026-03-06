<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\BaseModel;

class CalibracionInformeCargaSeguraModel extends BaseModel
{
    protected $table = 'calibracion_informe_carga_segura';
    protected $primaryKey = 'id_calibracion';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = [
        'id_calibracion',
        'resultado_control_vacio',
        'resultado_trazabilidad',
        'resultado_posicion',
        'responsable_nombre',
        'responsable_cargo',
        'cuit_transportista',
        'fecha_emision',
        'nota_posicion_sensores',
    ];

    public function porCalibracion(int $idCalibracion): ?array
    {
        return $this->where('id_calibracion', $idCalibracion)->first();
    }
}
