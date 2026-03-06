<?php
declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class CalibracionReimpresionModel extends Model
{
    protected $table            = 'calibracion_reimpresiones';
    protected $primaryKey       = 'id_reimpresion';
    protected $useAutoIncrement  = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = ''; // La tabla solo tiene created_at, no updated_at
    protected $allowedFields    = [
        'id_calibracion',
        'id_usuario',
        'mensaje'
    ];
}
