<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\NotificacionModel;
use App\Models\NotificacionConfigModel;
use App\Models\NotificacionConfigTipoModel;
use App\Models\ParametroSistemaModel;

/**
 * Notificaciones para admins. Permite aprobar/rechazar solicitudes de acceso.
 */
class Notificaciones extends BaseController
{
    public function index()
    {
        if (! function_exists('es_admin') || ! es_admin()) {
            return redirect()->to(site_url())->with('error', 'Sin permiso.');
        }
        $u = usuario_actual();
        $idAdmin = (int) $u['id_usuario'];
        $model = model(NotificacionModel::class);
        $notificaciones = $model->listarParaAdmin($idAdmin, 100);
        $tipoModel = model(NotificacionConfigTipoModel::class);
        $tiposDisponibles = NotificacionModel::getTiposDisponibles();
        $tiposActivos = $tipoModel->getTiposPorUsuario($idAdmin, $tiposDisponibles);
        $notificaciones = array_values(array_filter($notificaciones, function ($n) use ($tiposActivos) {
            $tipo = $n['tipo'] ?? '';
            return ! empty($tiposActivos[$tipo]);
        }));
        $data = [
            'titulo'         => 'Notificaciones',
            'notificaciones' => $notificaciones,
            'roles'          => []
        ];
        return view('notificaciones/index', $data);
    }

    /**
     * Cuenta de no leídas (para el badge del header). JSON.
     */
    public function contarNoLeidas()
    {
        if (! function_exists('es_admin') || ! es_admin()) {
            return $this->response->setJSON(json_response(true, ['count' => 0]));
        }
        try {
            $u = usuario_actual();
            $count = model(NotificacionModel::class)->contarNoLeidas((int) $u['id_usuario']);
            return $this->response->setJSON(json_response(true, ['count' => $count]));
        } catch (\Throwable $e) {
            return $this->response->setJSON(json_response(false, ['message' => $e->getMessage(), 'count' => 0]));
        }
    }

    /**
     * Estado para push en browser: count + última no leída (titulo, mensaje, url). JSON.
     * Respeta push_activo (si está en 0 no muestra badge/toast) y los tipos activos del usuario.
     */
    public function estadoPush()
    {
        if (! function_exists('es_admin') || ! es_admin()) {
            return $this->response->setJSON(json_response(true, ['count' => 0, 'ultima' => null]));
        }
        try {
            $u = usuario_actual();
            $idAdmin = (int) $u['id_usuario'];
            $configModel = model(NotificacionConfigModel::class);
            $config = $configModel->getOrCreate($idAdmin);
            $pushBrowserActivo = ! isset($config['push_browser_activo']) || ! empty($config['push_browser_activo']);
            if (isset($config['push_activo']) && empty($config['push_activo'])) {
                return $this->response->setJSON(json_response(true, ['count' => 0, 'ultima' => null, 'push_browser_activo' => $pushBrowserActivo]));
            }
            $tipoModel = model(NotificacionConfigTipoModel::class);
            $tiposDisponibles = NotificacionModel::getTiposDisponibles();
            $tiposActivos = $tipoModel->getTiposPorUsuario($idAdmin, $tiposDisponibles);
            $tiposFiltro = array_keys(array_filter($tiposActivos));
            $model = model(NotificacionModel::class);
            $count = $model->contarNoLeidas($idAdmin, $tiposFiltro);
            $ultima = $model->ultimaNoLeida($idAdmin, $tiposFiltro);
            $out = [
                'count' => $count,
                'ultima' => null,
                'push_browser_activo' => $pushBrowserActivo,
            ];
            if ($ultima) {
                $out['ultima'] = [
                    'titulo' => $ultima['titulo'],
                    'mensaje' => $ultima['mensaje'] ?: '',
                    'url' => site_url('notificaciones'),
                ];
            }
            return $this->response->setJSON(json_response(true, $out));
        } catch (\Throwable $e) {
            return $this->response->setJSON(json_response(false, ['message' => $e->getMessage(), 'count' => 0, 'ultima' => null]));
        }
    }

    /**
     * Marcar una notificación como leída. POST. JSON.
     */
    public function marcarLeida()
    {
        if (! function_exists('es_admin') || ! es_admin()) {
            return $this->response->setJSON(json_response(false, ['message' => 'Sin permiso']));
        }
        try {
            $idNotif = (int) $this->request->getPost('id_notificacion');
            if ($idNotif <= 0) {
                return $this->response->setJSON(json_response(false, ['message' => 'ID inválido']));
            }
            $u = usuario_actual();
            model(NotificacionModel::class)->marcarLeida($idNotif, (int) $u['id_usuario']);
            return $this->response->setJSON(json_response(true, []));
        } catch (\Throwable $e) {
            return $this->response->setJSON(json_response(false, ['message' => $e->getMessage()]));
        }
    }

    /**
     * Marcar todas las notificaciones como leídas. POST. JSON.
     */
    public function marcarTodasLeidas()
    {
        if (! function_exists('es_admin') || ! es_admin()) {
            return $this->response->setJSON(json_response(false, ['message' => 'Sin permiso']));
        }
        try {
            $u = usuario_actual();
            model(NotificacionModel::class)->marcarTodasLeidas((int) $u['id_usuario']);
            return $this->response->setJSON(json_response(true, []));
        } catch (\Throwable $e) {
            return $this->response->setJSON(json_response(false, ['message' => $e->getMessage()]));
        }
    }

    /**
     * Configuración de notificaciones del admin (email, WhatsApp, recordatorio, tipos).
     */
    public function config()
    {
        if (! function_exists('es_admin') || ! es_admin()) {
            return redirect()->to(site_url())->with('error', 'Sin permiso.');
        }
        $u = usuario_actual();
        $idAdmin = (int) $u['id_usuario'];
        $configModel = model(NotificacionConfigModel::class);
        $tipoModel = model(NotificacionConfigTipoModel::class);
        $tiposDisponibles = NotificacionModel::getTiposDisponibles();
        $config = $configModel->getOrCreate($idAdmin);
        $tiposActivos = $tipoModel->getTiposPorUsuario($idAdmin, $tiposDisponibles);
        $mesesVencidaMaxKpi = $this->getMesesVencidaMaxKpi();
        $data = [
            'titulo'       => 'Configurar notificaciones',
            'config'       => $config,
            'tiposDisponibles' => $tiposDisponibles,
            'tiposActivos' => $tiposActivos,
            'emailUsuario' => $u['email'] ?? '',
            'meses_vencida_max_kpi' => $mesesVencidaMaxKpi,
        ];
        return view('notificaciones/config', $data);
    }

    /**
     * Guardar configuración de notificaciones. POST.
     */
    public function guardarConfig()
    {
        if (! function_exists('es_admin') || ! es_admin()) {
            return redirect()->to(site_url())->with('error', 'Sin permiso.');
        }
        $u = usuario_actual();
        $idAdmin = (int) $u['id_usuario'];
        $configModel = model(NotificacionConfigModel::class);
        $tipoModel = model(NotificacionConfigTipoModel::class);
        $recordatorioActivo = (bool) $this->request->getPost('recordatorio_activo');
        $recordatorioMinutos = $recordatorioActivo ? max(1, (int) $this->request->getPost('recordatorio_minutos')) : 0;
        $configModel->guardarParaUsuario($idAdmin, [
            'email_activo' => $this->request->getPost('email_activo'),
            'email_destino' => $this->request->getPost('email_destino'),
            'push_activo' => $this->request->getPost('push_activo'),
            'push_browser_activo' => $this->request->getPost('push_browser_activo'),
            'recordatorio_minutos' => $recordatorioMinutos,
            'dias_aviso_vencimiento' => max(1, (int) $this->request->getPost('dias_aviso_vencimiento')),
        ]);
        $tiposDisponibles = NotificacionModel::getTiposDisponibles();
        $tiposActivos = [];
        foreach (array_keys($tiposDisponibles) as $tipo) {
            $tiposActivos[$tipo] = (bool) $this->request->getPost('tipo_' . $tipo);
        }
        $tipoModel->guardarParaUsuario($idAdmin, $tiposActivos);
        $meses = $this->request->getPost('meses_vencida_max_kpi');
        if ($meses !== null) {
            $db = \Config\Database::connect();
            if ($db->tableExists('parametros_sistema')) {
                model(ParametroSistemaModel::class)->setValor('meses_vencida_max_kpi', (string) (int) $meses);
            }
        }
        return redirect()->to(site_url('notificaciones/config'))->with('success', 'Configuración guardada.');
    }

    /**
     * Valor de meses_vencida_max_kpi (parametros_sistema o Config\Calibraciones).
     */
    private function getMesesVencidaMaxKpi(): int
    {
        $db = \Config\Database::connect();
        if ($db->tableExists('parametros_sistema')) {
            $v = model(ParametroSistemaModel::class)->getValor('meses_vencida_max_kpi');
            if ($v !== null && $v !== '') {
                return (int) $v;
            }
        }
        $config = config(\Config\Calibraciones::class);
        return (int) ($config->mesesVenidaMaxKpi ?? 24);
    }

    /**
     * Procesa recordatorios (para llamar desde cron). Opcional: token en query para seguridad.
     */
    public function procesarRecordatorios()
    {
        $token = env('NOTIF_RECORDATORIO_TOKEN', '');
        if ($token !== '' && $this->request->getGet('token') !== $token) {
            return $this->response->setStatusCode(403)->setBody('Forbidden');
        }
        \App\Libraries\NotificacionEnvio::procesarRecordatorios();
        \App\Libraries\NotificacionEnvio::procesarVencimientosCalibraciones();
        return $this->response->setBody('OK');
    }
}
