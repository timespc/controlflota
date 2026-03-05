<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Choferes: documento, nombre, nacionalidad (naciones), transportista (transportistas), comentarios.
 * No incluye vto_art ni vto_lnh.
 */
class ChoferesModel extends BaseModel
{
    protected $table         = 'choferes';
    protected $primaryKey    = 'id';
    protected $useAutoIncrement = true;
    protected $returnType    = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = ['documento', 'nombre', 'id_nacion', 'id_tta', 'comentarios'];

    /**
     * Lista choferes con nombre de nación y transportista para DataTable.
     */
    public function listarTodos(): array
    {
        $naciones = $this->db->prefixTable('naciones');
        $transportistas = $this->db->prefixTable('transportistas');
        $choferes = $this->db->prefixTable('choferes');
        $rows = $this->db->table("{$choferes} c")
            ->select("c.id, c.documento, c.nombre, c.id_nacion, c.id_tta, c.comentarios, c.updated_at,
                n.nacion,
                t.transportista")
            ->join("{$naciones} n", 'n.id_nacion = c.id_nacion', 'left')
            ->join("{$transportistas} t", 't.id_tta = c.id_tta', 'left')
            ->orderBy('c.nombre', 'ASC')
            ->get()
            ->getResultArray();
        foreach ($rows as &$r) {
            $r['ult_actualiz'] = $r['updated_at']
                ? date('d/m/Y H:i:s', strtotime($r['updated_at']))
                : '';
            unset($r['updated_at']);
        }
        return $rows;
    }

    /**
     * Obtiene un chofer por ID con nación y transportista.
     */
    public function obtenerPorId(int $id): ?array
    {
        $naciones = $this->db->prefixTable('naciones');
        $transportistas = $this->db->prefixTable('transportistas');
        $choferes = $this->db->prefixTable('choferes');
        $row = $this->db->table("{$choferes} c")
            ->select("c.id, c.documento, c.nombre, c.id_nacion, c.id_tta, c.comentarios, c.created_at, c.updated_at,
                n.nacion,
                t.transportista")
            ->join("{$naciones} n", 'n.id_nacion = c.id_nacion', 'left')
            ->join("{$transportistas} t", 't.id_tta = c.id_tta', 'left')
            ->where('c.id', $id)
            ->get()
            ->getRowArray();
        return $row ?: null;
    }

    /**
     * Guardar (alta o actualización).
     */
    public function guardarChofer(array $data): int
    {
        $id = isset($data['id']) ? (int) $data['id'] : 0;
        unset($data['id']);
        $payload = [
            'documento'   => $data['documento'] ?? '',
            'nombre'      => $data['nombre'] ?? '',
            'id_nacion'   => ! empty($data['id_nacion']) ? (int) $data['id_nacion'] : null,
            'id_tta'      => ! empty($data['id_tta']) ? (int) $data['id_tta'] : null,
            'comentarios' => $data['comentarios'] ?? null,
        ];
        if ($id > 0) {
            $this->update($id, $payload);
            return $id;
        }
        $this->insert($payload);
        return (int) $this->getInsertID();
    }

    /**
     * Eliminar por ID.
     */
    public function eliminarChofer(int $id): bool
    {
        return $this->delete($id);
    }

    public function obtenerTotal(): int
    {
        return (int) $this->countAllResults();
    }
}
