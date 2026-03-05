<?php

namespace App\Models;

use App\Models\BaseModel;

class NotificacionModel extends BaseModel
{
    protected $table = 'notificaciones';
    protected $primaryKey = 'id_notificacion';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = false;

    protected $allowedFields = ['tipo', 'titulo', 'mensaje', 'datos_json'];

    const TIPO_NUEVO_USUARIO = 'nuevo_usuario';
    const TIPO_USUARIO_DESACTIVADO = 'usuario_desactivado';
    const TIPO_CALIBRACION_POR_VENCER = 'calibracion_por_vencer';

    /** Tipos de notificación y su etiqueta para la config (cada admin elige cuáles recibir). */
    public static function getTiposDisponibles(): array
    {
        return [
            self::TIPO_NUEVO_USUARIO => 'Usuario dado de alta (por admin)',
            self::TIPO_USUARIO_DESACTIVADO => 'Usuario desactivado o eliminado',
            self::TIPO_CALIBRACION_POR_VENCER => 'Calibraciones por vencer (aviso X días antes)',
        ];
    }

    /**
     * Crea una notificación (para que la vean los admins).
     */
    public function crear(string $tipo, string $titulo, string $mensaje = '', array $datos = []): int
    {
        $this->insert([
            'tipo'      => $tipo,
            'titulo'    => $titulo,
            'mensaje'   => $mensaje,
            'datos_json' => json_encode($datos),
            'created_at' => date('Y-m-d H:i:s')
        ]);
        return (int) $this->getInsertID();
    }

    /**
     * Lista notificaciones para un admin (las no leídas primero, con datos parseados).
     * Para tipo calibracion_por_vencer no incluye el array 'items' en datos (se carga bajo demanda).
     */
    public function listarParaAdmin(int $idAdmin, int $limite = 50): array
    {
        $notif = $this->db->prefixTable('notificaciones');
        $leida = $this->db->prefixTable('notificacion_leida');
        $sql = "SELECT n.*, l.id_notificacion AS leida
                FROM {$notif} n
                LEFT JOIN {$leida} l ON l.id_notificacion = n.id_notificacion AND l.id_usuario = ?
                ORDER BY n.created_at DESC
                LIMIT ?";
        $rows = $this->db->query($sql, [$idAdmin, $limite])->getResultArray();
        $out = [];
        foreach ($rows as $r) {
            $r['datos'] = $r['datos_json'] ? json_decode($r['datos_json'], true) : [];
            $r['leida'] = ! empty($r['leida']);
            unset($r['datos_json']);
            if (($r['tipo'] ?? '') === self::TIPO_CALIBRACION_POR_VENCER && isset($r['datos']['items'])) {
                unset($r['datos']['items']);
            }
            $out[] = $r;
        }
        return $out;
    }

    /**
     * Cuenta notificaciones no leídas por un admin.
     * Si $tiposFiltro no está vacío, solo cuenta notificaciones de esos tipos (respeta config push).
     * Si $tiposFiltro es vacío (usuario sin ningún tipo activo), devuelve 0.
     */
    public function contarNoLeidas(int $idAdmin, array $tiposFiltro = null): int
    {
        if ($tiposFiltro !== null && $tiposFiltro === []) {
            return 0;
        }
        $notif = $this->db->prefixTable('notificaciones');
        $leida = $this->db->prefixTable('notificacion_leida');
        $sql = "SELECT COUNT(*) as c FROM {$notif} n
                WHERE NOT EXISTS (SELECT 1 FROM {$leida} l WHERE l.id_notificacion = n.id_notificacion AND l.id_usuario = ?)";
        $params = [$idAdmin];
        if ($tiposFiltro !== null && $tiposFiltro !== []) {
            $placeholders = implode(',', array_fill(0, count($tiposFiltro), '?'));
            $sql .= " AND n.tipo IN ({$placeholders})";
            $params = array_merge($params, $tiposFiltro);
        }
        $row = $this->db->query($sql, $params)->getRowArray();
        return (int) ($row['c'] ?? 0);
    }

    /**
     * Última notificación no leída del admin (para mostrar en push/toast).
     * Si $tiposFiltro no está vacío, solo considera notificaciones de esos tipos.
     * Si $tiposFiltro es vacío (usuario sin ningún tipo activo), devuelve null.
     */
    public function ultimaNoLeida(int $idAdmin, array $tiposFiltro = null): ?array
    {
        if ($tiposFiltro !== null && $tiposFiltro === []) {
            return null;
        }
        $notif = $this->db->prefixTable('notificaciones');
        $leida = $this->db->prefixTable('notificacion_leida');
        $sql = "SELECT n.id_notificacion, n.titulo, n.mensaje, n.created_at
                FROM {$notif} n
                WHERE NOT EXISTS (SELECT 1 FROM {$leida} l WHERE l.id_notificacion = n.id_notificacion AND l.id_usuario = ?)";
        $params = [$idAdmin];
        if ($tiposFiltro !== null && $tiposFiltro !== []) {
            $placeholders = implode(',', array_fill(0, count($tiposFiltro), '?'));
            $sql .= " AND n.tipo IN ({$placeholders})";
            $params = array_merge($params, $tiposFiltro);
        }
        $sql .= " ORDER BY n.created_at DESC LIMIT 1";
        $row = $this->db->query($sql, $params)->getRowArray();
        return $row ?: null;
    }

    /**
     * Marca una notificación como leída por un admin.
     */
    public function marcarLeida(int $idNotificacion, int $idUsuario): void
    {
        $tbl = $this->db->prefixTable('notificacion_leida');
        $this->db->query("INSERT IGNORE INTO `{$tbl}` (id_notificacion, id_usuario, leida_at) VALUES (?, ?, NOW())", [$idNotificacion, $idUsuario]);
    }

    /**
     * Marca todas las notificaciones como leídas para un admin.
     */
    public function marcarTodasLeidas(int $idUsuario): void
    {
        $notif = $this->db->prefixTable('notificaciones');
        $leida = $this->db->prefixTable('notificacion_leida');
        $this->db->query(
            "INSERT IGNORE INTO `{$leida}` (id_notificacion, id_usuario, leida_at)
             SELECT n.id_notificacion, ?, NOW() FROM {$notif} n
             WHERE NOT EXISTS (SELECT 1 FROM {$leida} l WHERE l.id_notificacion = n.id_notificacion AND l.id_usuario = ?)",
            [$idUsuario, $idUsuario]
        );
    }
}
