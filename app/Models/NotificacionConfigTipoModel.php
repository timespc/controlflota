<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\BaseModel;

class NotificacionConfigTipoModel extends BaseModel
{
    protected $table = 'notificacion_config_tipo';
    protected $primaryKey = 'id_usuario';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = ['id_usuario', 'tipo_notificacion', 'activo'];

    /**
     * Indica si el usuario (admin) recibe notificaciones de tipo $tipo.
     * Si no hay fila, se considera activo (recibe).
     */
    public function recibeTipo(int $idUsuario, string $tipo): bool
    {
        $row = $this->where('id_usuario', $idUsuario)->where('tipo_notificacion', $tipo)->first();
        if (! $row) {
            return true;
        }
        return ! empty($row['activo']);
    }

    /**
     * Obtiene los tipos activos/inactivos para un usuario. Devuelve [tipo => activo].
     */
    public function getTiposPorUsuario(int $idUsuario, array $tiposDisponibles): array
    {
        $rows = $this->where('id_usuario', $idUsuario)->findAll();
        $map = [];
        foreach ($rows as $r) {
            $map[$r['tipo_notificacion']] = ! empty($r['activo']);
        }
        $out = [];
        foreach ($tiposDisponibles as $tipo => $label) {
            $out[$tipo] = array_key_exists($tipo, $map) ? $map[$tipo] : true;
        }
        return $out;
    }

    /**
     * Guarda los tipos activos para un usuario.
     */
    public function guardarParaUsuario(int $idUsuario, array $tiposActivos): void
    {
        foreach ($tiposActivos as $tipo => $activo) {
            $valor = $activo ? 1 : 0;
            $row = $this->where('id_usuario', $idUsuario)->where('tipo_notificacion', $tipo)->first();
            if ($row) {
                $this->db->table($this->table)->where('id_usuario', $idUsuario)->where('tipo_notificacion', $tipo)->update(['activo' => $valor, 'updated_at' => date('Y-m-d H:i:s')]);
            } else {
                $this->insert([
                    'id_usuario' => $idUsuario,
                    'tipo_notificacion' => $tipo,
                    'activo' => $valor
                ]);
            }
        }
    }
}
