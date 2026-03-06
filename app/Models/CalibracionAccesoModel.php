<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\BaseModel;

class CalibracionAccesoModel extends BaseModel
{
    protected $table = 'calibracion_accesos';
    protected $primaryKey = 'id_acceso';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = false;

    protected $allowedFields = [
        'id_calibracion',
        'ip',
        'user_agent',
        'referer_url',
        'accedido_at'
    ];

    /**
     * Registra un acceso público a una calibración (IP, user_agent, referer, timestamp).
     */
    public function registrarAcceso(int $idCalibracion, ?string $ip, ?string $userAgent, ?string $referer): void
    {
        $this->insert([
            'id_calibracion' => $idCalibracion,
            'ip'             => $ip !== null && strlen($ip) <= 45 ? $ip : null,
            'user_agent'     => $userAgent,
            'referer_url'    => $referer,
            'accedido_at'    => date('Y-m-d H:i:s')
        ]);
    }
}
