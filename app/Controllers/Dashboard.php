<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\CalibracionModel;
use App\Models\EquiposModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $estado = $this->request->getGet('estado');
        if (! in_array($estado, ['vencido', 'proximo'], true)) {
            $estado = 'vencido';
        }

        $data = [
            'equipos_vencidos'      => $this->getEquiposVencidos(),
            'proximos_30'           => $this->getProximos30Dias(),
            'flota_activa'          => $this->getFlotaActiva(),
            'calibraciones_activas' => $this->getCalibracionesActivas(),
            'vencimientos_criticos' => $this->getVencimientosCriticos($estado),
            'estado_activo'         => $estado,
        ];

        return view('dashboard/index', $data);
    }

    /**
     * Cantidad de calibraciones vencidas (vto_calib < hoy).
     */
    private function getEquiposVencidos(): int
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('calibraciones')) {
            return 0;
        }
        $model = model(CalibracionModel::class);
        return $model->contarVencidas(date('Y-m-d'));
    }

    /**
     * Cantidad de vencimientos en los próximos 30 días (calibraciones).
     */
    private function getProximos30Dias(): int
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('calibraciones')) {
            return 0;
        }
        $model = model(CalibracionModel::class);
        $hoy = date('Y-m-d');
        $limite30 = date('Y-m-d', strtotime('+30 days'));
        return $model->contarProximos30($hoy, $limite30);
    }

    /**
     * Flota activa: cantidad de equipos.
     */
    private function getFlotaActiva(): int
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('equipos')) {
            return 0;
        }
        $model = model(EquiposModel::class);
        return $model->countAllResults();
    }

    /**
     * Calibraciones activas (vto_calib >= hoy).
     */
    private function getCalibracionesActivas(): int
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('calibraciones')) {
            return 0;
        }
        $model = model(CalibracionModel::class);
        return $model->contarActivas(date('Y-m-d'));
    }

    /**
     * Lista de vencimientos críticos: calibraciones vencidas o que vencen en 30 días.
     * $estado: 'vencido' = solo vencidas, 'proximo' = solo próximos 30 días.
     */
    private function getVencimientosCriticos(string $estado): array
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('calibraciones')) {
            return [];
        }
        $model = model(CalibracionModel::class);
        return $model->listarVencimientosCriticos(2000, $estado);
    }
}




