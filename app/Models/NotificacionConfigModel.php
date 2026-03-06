<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\BaseModel;
use App\Models\NotificacionModel;
use App\Models\UserModel;

class NotificacionConfigModel extends BaseModel
{
    protected $table = 'notificacion_config';
    protected $primaryKey = 'id_config';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = [
        'id_usuario',
        'email_activo',
        'email_destino',
        'push_activo',
        'push_browser_activo',
        'recordatorio_minutos',
        'dias_aviso_vencimiento',
    ];

    /**
     * Obtiene la config de un admin; si no existe, crea una por defecto.
     */
    public function getOrCreate(int $idUsuario): array
    {
        $row = $this->where('id_usuario', $idUsuario)->first();
        if ($row) {
            return $row;
        }
        $this->insert([
            'id_usuario' => $idUsuario,
            'email_activo' => 1,
            'push_activo' => 1,
            'push_browser_activo' => 1,
            'recordatorio_minutos' => 0,
            'dias_aviso_vencimiento' => 30,
        ]);
        $row = $this->find((int) $this->getInsertID());
        return $row ?: [];
    }

    /**
     * Guarda la config (solo para el usuario indicado).
     */
    public function guardarParaUsuario(int $idUsuario, array $data): void
    {
        $row = $this->where('id_usuario', $idUsuario)->first();
        $payload = [
            'email_activo' => ! empty($data['email_activo']) ? 1 : 0,
            'email_destino' => ! empty($data['email_destino']) ? trim($data['email_destino']) : null,
            'push_activo' => ! empty($data['push_activo']) ? 1 : 0,
            'push_browser_activo' => ! empty($data['push_browser_activo']) ? 1 : 0,
            'recordatorio_minutos' => max(0, (int) ($data['recordatorio_minutos'] ?? 0)),
            'dias_aviso_vencimiento' => max(0, (int) ($data['dias_aviso_vencimiento'] ?? 0)),
        ];
        if ($row) {
            $this->update($row['id_config'], $payload);
        } else {
            $payload['id_usuario'] = $idUsuario;
            $this->insert($payload);
        }
    }

    /**
     * Lista admins (Ion Auth grupo admin) con su config que deben recibir una notificación de tipo $tipo.
     * Para tipo calibracion_por_vencer, $notif puede contener datos con items[].dias_restantes; solo se incluyen
     * admins cuyo dias_aviso_vencimiento >= al menos un item (reciben si hay alguna calibración en su ventana).
     *
     * @param array|null $notif Notificación completa (con 'datos' o 'datos_json') para filtrar por dias_restantes
     */
    public function listarAdminsParaNotificar(string $tipo, ?array $notif = null): array
    {
        $usuarios = model(UserModel::class)->getAdminUsers();
        $tipoModel = model(NotificacionConfigTipoModel::class);
        $minDiasRestantes = null;
        if ($tipo === NotificacionModel::TIPO_CALIBRACION_POR_VENCER && $notif !== null) {
            $datos = $notif['datos'] ?? [];
            if (empty($datos) && ! empty($notif['datos_json'])) {
                $datos = json_decode($notif['datos_json'], true) ?: [];
            }
            $items = $datos['items'] ?? [];
            foreach ($items as $item) {
                $d = (int) ($item['dias_restantes'] ?? 0);
                if ($minDiasRestantes === null || $d < $minDiasRestantes) {
                    $minDiasRestantes = $d;
                }
            }
        }
        if (empty($usuarios)) {
            return [];
        }

        // Batch: cargar todas las configs existentes en 1 consulta
        $todosIds = array_map(fn($u) => (int) $u['id'], $usuarios);
        $configsExistentes = $this->whereIn('id_usuario', $todosIds)->findAll();
        $configsPorUsuario = [];
        foreach ($configsExistentes as $c) {
            $configsPorUsuario[(int) $c['id_usuario']] = $c;
        }

        // Crear configs faltantes en batch
        $insertar = [];
        foreach ($todosIds as $uid) {
            if (! isset($configsPorUsuario[$uid])) {
                $insertar[] = [
                    'id_usuario'             => $uid,
                    'email_activo'           => 1,
                    'push_activo'            => 1,
                    'push_browser_activo'    => 1,
                    'recordatorio_minutos'   => 0,
                    'dias_aviso_vencimiento' => 30,
                ];
            }
        }
        if (! empty($insertar)) {
            $this->insertBatch($insertar);
            // Recargar solo las recién creadas
            $nuevas = $this->whereIn('id_usuario', array_column($insertar, 'id_usuario'))->findAll();
            foreach ($nuevas as $c) {
                $configsPorUsuario[(int) $c['id_usuario']] = $c;
            }
        }

        $out = [];
        foreach ($usuarios as $u) {
            $idUsuario = (int) $u['id'];
            if (! $tipoModel->recibeTipo($idUsuario, $tipo)) {
                continue;
            }
            $config = $configsPorUsuario[$idUsuario] ?? [];
            if ($tipo === NotificacionModel::TIPO_CALIBRACION_POR_VENCER) {
                $diasAviso = (int) ($config['dias_aviso_vencimiento'] ?? 0);
                if ($diasAviso <= 0) {
                    continue;
                }
                if ($minDiasRestantes !== null && $diasAviso < $minDiasRestantes) {
                    continue;
                }
            }
            $emailDestino = ! empty($config['email_destino']) ? $config['email_destino'] : ($u['email'] ?? null);
            $out[] = [
                'id_usuario'   => $idUsuario,
                'usuario'      => ['id_usuario' => $idUsuario, 'email' => $u['email'] ?? ''],
                'config'       => $config,
                'email_destino' => ! empty($config['email_activo']) ? $emailDestino : null,
            ];
        }
        return $out;
    }
}
