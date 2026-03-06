<?php
declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Notas del calibrador por calibración.
 * Uso exclusivamente interno: NUNCA incluir en la tarjeta de calibración (imprimir) ni en la vista pública.
 */
class CalibracionNotasModel extends Model
{
    protected $table = 'calibracion_notas';
    protected $primaryKey = 'id_calibracion';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $allowedFields = ['id_calibracion', 'notas', 'id_usuario'];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Obtiene las notas de una calibración (y quién/cuándo las cargó).
     */
    public function getPorCalibracion(int $idCalibracion): ?array
    {
        $row = $this->find($idCalibracion);
        return $row ?: null;
    }

    /**
     * Guarda o actualiza las notas de una calibración.
     */
    public function guardarNotas(int $idCalibracion, string $notas, ?int $idUsuario): bool
    {
        $row = $this->find($idCalibracion);
        $data = [
            'id_calibracion' => $idCalibracion,
            'notas'          => $notas,
            'id_usuario'      => $idUsuario,
        ];
        if ($row) {
            return $this->update($idCalibracion, $data);
        }
        return (bool) $this->insert($data);
    }
}
