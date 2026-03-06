<?php
declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ParametroSistemaModel extends Model
{
    protected $table = 'parametros_sistema';
    protected $primaryKey = 'clave';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $allowedFields = ['clave', 'valor'];

    /**
     * Obtiene el valor de un parámetro. Retorna null si no existe.
     */
    public function getValor(string $clave): ?string
    {
        $row = $this->find($clave);
        return $row ? ($row['valor'] ?? null) : null;
    }

    /**
     * Guarda o actualiza un parámetro.
     */
    public function setValor(string $clave, ?string $valor): bool
    {
        $row = $this->find($clave);
        if ($row) {
            return $this->update($clave, ['valor' => $valor]);
        }
        return (bool) $this->insert(['clave' => $clave, 'valor' => $valor]);
    }
}
