<?php

declare(strict_types=1);

namespace App\Commands;

use App\Models\NotificacionConfigModel;
use App\Models\NotificacionConfigTipoModel;
use App\Models\NotificacionModel;
use App\Models\UserModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Muestra la configuración de notificaciones de un usuario por email.
 * Uso: php spark notificaciones:ver-config "email@ejemplo.com"
 */
class NotificacionesVerConfig extends BaseCommand
{
    protected $group       = 'Notificaciones';
    protected $name        = 'notificaciones:ver-config';
    protected $description = 'Muestra la config de notificaciones de un usuario por email.';
    protected $usage       = 'notificaciones:ver-config <email>';

    /** @var array<string, string> */
    protected $arguments = [
        'email' => 'Email del usuario (ej. francurtofrd@gmail.com)',
    ];

    /** @var array<string, string> */
    protected $options = [];

    public function run(array $params): int
    {
        $email = trim($params['email'] ?? $params[0] ?? '');
        if ($email === '') {
            CLI::error('Indicá el email del usuario. Uso: php spark notificaciones:ver-config "email@ejemplo.com"');
            return 1;
        }

        $userModel = model(UserModel::class);
        $user = $userModel->where('email', $email)->first();
        if (! $user) {
            CLI::error("No existe ningún usuario con email: {$email}");
            return 1;
        }

        $idUsuario = (int) (is_object($user) ? $user->id : $user['id']);
        $admins = $userModel->getAdminUsers();
        $adminIds = array_column($admins, 'id');
        $esAdmin = in_array((string) $idUsuario, $adminIds, true) || in_array($idUsuario, $adminIds, true);

        CLI::newLine();
        CLI::write('--- Usuario ---', 'yellow');
        CLI::write("ID: {$idUsuario}");
        CLI::write("Email: {$email}");
        CLI::write('Es admin: ' . ($esAdmin ? 'Sí' : 'No'));

        if (! $esAdmin) {
            CLI::newLine();
            CLI::write('Solo los usuarios admin tienen configuración de notificaciones en esta app.', 'yellow');
            return 0;
        }

        $configModel = model(NotificacionConfigModel::class);
        $tipoModel = model(NotificacionConfigTipoModel::class);
        $config = $configModel->getOrCreate($idUsuario);

        CLI::newLine();
        CLI::write('--- Config notificaciones ---', 'yellow');
        CLI::write('Recibir notificaciones generales (campana): ' . (! isset($config['push_activo']) || ! empty($config['push_activo']) ? 'Sí' : 'No'));
        CLI::write('Recibir push navegador: ' . (! isset($config['push_browser_activo']) || ! empty($config['push_browser_activo']) ? 'Sí' : 'No'));
        CLI::write('Recibir mail: ' . (! empty($config['email_activo']) ? 'Sí' : 'No'));
        CLI::write('Email destino: ' . ($config['email_destino'] ?? '(usa el de la cuenta)'));
        CLI::write('Recordatorio cada X minutos: ' . ((int) ($config['recordatorio_minutos'] ?? 0)) . ' (0 = no recordatorios)');
        CLI::write('Días aviso vencimiento (calibraciones): ' . ((int) ($config['dias_aviso_vencimiento'] ?? 0)) . ' (0 = no aviso)');

        $tiposDisponibles = NotificacionModel::getTiposDisponibles();
        $tiposActivos = $tipoModel->getTiposPorUsuario($idUsuario, $tiposDisponibles);

        CLI::newLine();
        CLI::write('--- Tipos de notificación (recibir sí/no) ---', 'yellow');
        foreach ($tiposActivos as $tipo => $activo) {
            $label = $tiposDisponibles[$tipo] ?? $tipo;
            CLI::write("  {$label}: " . ($activo ? 'Sí' : 'No'));
        }

        CLI::newLine();
        CLI::write('--- Resumen: ¿debería ver/recibir? ---', 'green');
        $recibeAlgunTipo = (bool) array_filter($tiposActivos);
        $pushActivo = ! isset($config['push_activo']) || ! empty($config['push_activo']);
        $push = ($pushActivo && $recibeAlgunTipo) ? 'Sí (badge/toast según tipos activos)' : ($pushActivo ? 'No (no tiene ningún tipo activo)' : 'No (campana desactivada)');
        CLI::write("Campana (badge, toast): {$push}");
        $pushBrowser = ! isset($config['push_browser_activo']) || ! empty($config['push_browser_activo']);
        CLI::write('Push navegador: ' . ($pushBrowser ? 'Sí' : 'No (desactivado en config)'));
        $emailActivo = ! empty($config['email_activo']);
        $emailDestino = ! empty($config['email_destino']) ? $config['email_destino'] : $email;
        $emailRes = $emailActivo && $emailDestino && $recibeAlgunTipo ? 'Sí (a ' . $emailDestino . ')' : 'No';
        CLI::write("Email de notificaciones: {$emailRes}");
        $recordatorio = (int) ($config['recordatorio_minutos'] ?? 0) > 0;
        CLI::write('Recordatorios por email (no leídas): ' . ($recordatorio ? 'Sí (cada ' . ($config['recordatorio_minutos'] ?? 0) . ' min)' : 'No (está en 0)'));
        $diasAviso = (int) ($config['dias_aviso_vencimiento'] ?? 0);
        $calib = $recibeAlgunTipo && array_key_exists(NotificacionModel::TIPO_CALIBRACION_POR_VENCER, $tiposActivos) && $tiposActivos[NotificacionModel::TIPO_CALIBRACION_POR_VENCER];
        CLI::write('Calibraciones por vencer (aviso diario): ' . ($calib && $diasAviso > 0 ? "Sí (aviso con {$diasAviso} días de anticipación)" : 'No'));

        CLI::newLine();
        return 0;
    }
}
