<?php

namespace App\Libraries;

use App\Models\CalibracionModel;
use App\Models\NotificacionModel;
use App\Models\NotificacionConfigModel;
use App\Models\NotificacionConfigTipoModel;
use App\Models\UserModel;

/**
 * Envío de notificaciones por email y recordatorios según config de cada admin.
 * Las notificaciones push en el navegador se gestionan aparte (polling/estado-push).
 */
class NotificacionEnvio
{
    /**
     * Después de crear una notificación, notifica a los admins por email según su config.
     * Las push se actualizan vía estado-push en el front.
     * Para tipo calibracion_por_vencer se pasa la notif para filtrar por dias_aviso_vencimiento.
     */
    public static function notificarAdmins(int $idNotificacion): void
    {
        $notifModel = model(NotificacionModel::class);
        $notif = $notifModel->find($idNotificacion);
        if (! $notif) {
            return;
        }
        if (! empty($notif['datos_json'])) {
            $notif['datos'] = json_decode($notif['datos_json'], true) ?: [];
        }
        $configModel = model(NotificacionConfigModel::class);
        $destinatarios = $configModel->listarAdminsParaNotificar($notif['tipo'], $notif);
        foreach ($destinatarios as $d) {
            if (! empty($d['email_destino'])) {
                self::enviarEmail($d['email_destino'], $notif['titulo'], $notif['mensaje'] ?: $notif['titulo']);
            }
        }
    }

    /**
     * Envía recordatorios a admins que tienen recordatorio_minutos > 0 y no leyeron la notificación.
     * Llamar desde cron cada X minutos (ej. cada 5 min).
     */
    public static function procesarRecordatorios(): void
    {
        $configModel = model(NotificacionConfigModel::class);
        $notifModel = model(NotificacionModel::class);
        $admins = model(UserModel::class)->getAdminUsers();
        $prefix = $configModel->db->getPrefix();
        $tblNotif = $prefix . 'notificaciones';
        $tblLeida = $prefix . 'notificacion_leida';
        $tblRecordatorio = $prefix . 'notificacion_recordatorio';
        foreach ($admins as $admin) {
            $idUsuario = (int) $admin['id'];
            $config = $configModel->getOrCreate($idUsuario);
            $minutos = (int) ($config['recordatorio_minutos'] ?? 0);
            if ($minutos <= 0) {
                continue;
            }
            $tipoModel = model(NotificacionConfigTipoModel::class);
            $sql = "SELECT n.id_notificacion, n.titulo, n.mensaje, n.tipo, n.created_at
                    FROM {$tblNotif} n
                    WHERE NOT EXISTS (SELECT 1 FROM {$tblLeida} l WHERE l.id_notificacion = n.id_notificacion AND l.id_usuario = ?)
                    AND NOT EXISTS (
                        SELECT 1 FROM {$tblRecordatorio} r
                        WHERE r.id_notificacion = n.id_notificacion AND r.id_usuario = ?
                        AND r.enviado_at >= DATE_SUB(NOW(), INTERVAL ? MINUTE)
                    )
                    AND n.created_at <= DATE_SUB(NOW(), INTERVAL ? MINUTE)
                    ORDER BY n.created_at ASC";
            $pendientes = $configModel->db->query($sql, [$idUsuario, $idUsuario, $minutos, $minutos])->getResultArray();
            $emailDestino = ! empty($config['email_destino']) ? $config['email_destino'] : ($admin['email'] ?? null);
            if (! $emailDestino && empty($config['email_activo'])) {
                continue;
            }
            foreach ($pendientes as $n) {
                if (! $tipoModel->recibeTipo($idUsuario, $n['tipo'])) {
                    continue;
                }
                if ($emailDestino) {
                    $asunto = '[Recordatorio] ' . $n['titulo'];
                    $cuerpo = ($n['mensaje'] ?: $n['titulo']) . "\n\nNotificación del " . $n['created_at'] . ". Revisá el sistema.";
                    self::enviarEmail($emailDestino, $asunto, $cuerpo);
                }
                $configModel->db->query("INSERT IGNORE INTO `{$tblRecordatorio}` (id_notificacion, id_usuario, enviado_at) VALUES (?, ?, NOW())", [$n['id_notificacion'], $idUsuario]);
            }
        }
    }

    /**
     * Procesa vencimientos de calibraciones: crea una notificación diaria si hay calibraciones por vencer
     * y hay al menos un admin con tipo calibracion_por_vencer y dias_aviso_vencimiento > 0.
     * Llamar desde cron (ej. una vez por día). Se crea como máximo una notificación por día.
     */
    public static function procesarVencimientosCalibraciones(): void
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('calibraciones') || ! $db->tableExists('notificacion_config')) {
            return;
        }

        $configModel = model(NotificacionConfigModel::class);
        $tipoModel = model(NotificacionConfigTipoModel::class);
        $admins = model(UserModel::class)->getAdminUsers();
        $maxDias = 0;
        foreach ($admins as $u) {
            if (! $tipoModel->recibeTipo((int) $u['id'], NotificacionModel::TIPO_CALIBRACION_POR_VENCER)) {
                continue;
            }
            $config = $configModel->getOrCreate((int) $u['id']);
            $dias = (int) ($config['dias_aviso_vencimiento'] ?? 0);
            if ($dias > $maxDias) {
                $maxDias = $dias;
            }
        }
        if ($maxDias <= 0) {
            return;
        }

        $notifModel = model(NotificacionModel::class);
        $prefix = $notifModel->db->getPrefix();
        $tbl = $prefix . 'notificaciones';
        $hoy = date('Y-m-d');
        $yaCreada = $notifModel->db->query(
            "SELECT 1 FROM {$tbl} WHERE tipo = ? AND DATE(created_at) = ? LIMIT 1",
            [NotificacionModel::TIPO_CALIBRACION_POR_VENCER, $hoy]
        )->getRow();
        if ($yaCreada) {
            return;
        }

        $calibracionModel = model(CalibracionModel::class);
        $count = $calibracionModel->contarPorVencer($maxDias, true);
        if ($count <= 0) {
            return;
        }

        $titulo = 'Calibraciones por vencer';
        $datos = ['count' => $count, 'max_dias' => $maxDias];

        $mensaje = "Hay {$count} calibraciones que vencen en los próximos {$maxDias} días o ya están vencidas. Ver detalle en el Dashboard.";
        $idNotif = $notifModel->crear(NotificacionModel::TIPO_CALIBRACION_POR_VENCER, $titulo, $mensaje, $datos);
        self::notificarAdmins($idNotif);
    }

    protected static function enviarEmail(string $to, string $subject, string $body): bool
    {
        try {
            $email = \Config\Services::email();
            $email->setFrom($email->fromEmail ?? 'noreply@montajescampana.com', $email->fromName ?? 'Montajes Campana');
            $email->setTo($to);
            $email->setSubject($subject);
            $email->setMessage(nl2br(esc($body)));
            return $email->send();
        } catch (\Throwable $e) {
            log_message('error', 'NotificacionEnvio email: ' . $e->getMessage());
            return false;
        }
    }

}
