<?php
declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Documentos adjuntos a Transportistas o Unidades.
 */
class DocumentoModel extends Model
{
    protected $table            = 'documentos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = '';
    protected $allowedFields    = [
        'tipo_entidad',
        'id_entidad',
        'nombre_original',
        'nombre_archivo',
        'extension',
        'mime_type',
        'tamano',
    ];

    public const TIPO_TRANSPORTISTA = 'transportista';
    public const TIPO_UNIDAD       = 'unidad';

    /**
     * Lista documentos de una entidad (transportista o unidad).
     *
     * @param string $tipo      'transportista' | 'unidad'
     * @param int    $idEntidad ID del transportista o unidad
     * @return array
     */
    public function listarPorEntidad(string $tipo, int $idEntidad): array
    {
        return $this->where('tipo_entidad', $tipo)
            ->where('id_entidad', $idEntidad)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Obtiene un documento por ID (para servir el archivo).
     */
    public function getById(int $id): ?array
    {
        $row = $this->find($id);
        return $row ?: null;
    }
}
