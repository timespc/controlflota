<?php

namespace App\Controllers;

use App\Models\CalibracionModel;
use App\Models\CalibradoresModel;
use App\Models\TransportistasModel;
use App\Models\EquiposModel;
use App\Models\NacionesModel;

class Reportes extends BaseController
{
    protected $calibracionModel;
    protected $calibradoresModel;
    protected $transportistasModel;
    protected $equiposModel;
    protected $nacionesModel;

    public function __construct()
    {
        $this->calibracionModel   = model(CalibracionModel::class);
        $this->calibradoresModel  = model(CalibradoresModel::class);
        $this->transportistasModel = model(TransportistasModel::class);
        $this->equiposModel      = model(EquiposModel::class);
        $this->nacionesModel     = model(NacionesModel::class);
    }

    /**
     * Página principal del módulo Reportes (tarjetas a cada tipo).
     */
    public function index()
    {
        $data = [
            'titulo' => 'Reportes',
        ];
        return view('reportes/index', $data);
    }

    /**
     * Reporte de calibraciones por período y filtros.
     */
    public function calibraciones()
    {
        $calibradores = [];
        try {
            $calibradores = $this->calibradoresModel->listarTodos();
        } catch (\Throwable $e) {
            // ignore
        }
        $data = [
            'titulo'   => 'Reporte de Calibraciones',
            'calibradores' => $calibradores,
        ];
        return view('reportes/calibraciones', $data);
    }

    /**
     * Listar calibraciones para el reporte (AJAX).
     */
    public function listarCalibraciones()
    {
        $fechaDesde   = $this->request->getPost('fecha_desde') ?: null;
        $fechaHasta   = $this->request->getPost('fecha_hasta') ?: null;
        $patente      = $this->request->getPost('patente') ?: null;
        $precinto     = $this->request->getPost('precinto') ?: null;
        $idCalibrador = $this->request->getPost('id_calibrador') ?: null;

        if (! $this->esFechaYmdValida($fechaDesde)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Fecha desde inválida (formato esperado: YYYY-MM-DD)']);
        }
        if (! $this->esFechaYmdValida($fechaHasta)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Fecha hasta inválida (formato esperado: YYYY-MM-DD)']);
        }
        if (! $this->esIdPositivo($idCalibrador)) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID de calibrador inválido']);
        }

        if ($patente !== null) {
            $patente = trim($patente);
        }
        if ($precinto !== null) {
            $precinto = trim($precinto);
        }
        $idCalibrador = $idCalibrador !== null ? (int) $idCalibrador : null;

        $filas = $this->calibracionModel->listarParaReporte($fechaDesde, $fechaHasta, $patente ?: null, $idCalibrador, $precinto ?: null);

        return $this->response->setJSON([
            'success' => true,
            'data'    => $filas,
        ]);
    }

    /**
     * Exportar reporte de calibraciones a CSV.
     */
    public function exportarCalibracionesCsv()
    {
        $fechaDesde   = $this->request->getGet('fecha_desde') ?: null;
        $fechaHasta   = $this->request->getGet('fecha_hasta') ?: null;
        $patente      = $this->request->getGet('patente') ?: null;
        $precinto     = $this->request->getGet('precinto') ?: null;
        $idCalibrador = $this->request->getGet('id_calibrador') ?: null;

        if (! $this->esFechaYmdValida($fechaDesde) || ! $this->esFechaYmdValida($fechaHasta)) {
            return $this->response->setStatusCode(400)->setBody('Fechas inválidas');
        }
        if (! $this->esIdPositivo($idCalibrador)) {
            return $this->response->setStatusCode(400)->setBody('ID de calibrador inválido');
        }

        if ($patente !== null) {
            $patente = trim($patente);
        }
        if ($precinto !== null) {
            $precinto = trim($precinto);
        }
        $idCalibrador = $idCalibrador !== null ? (int) $idCalibrador : null;

        $filas = $this->calibracionModel->listarParaReporte($fechaDesde, $fechaHasta, $patente ?: null, $idCalibrador, $precinto ?: null);

        $nombre = 'reporte_calibraciones_' . date('Y-m-d_His') . '.csv';
        $this->response->setHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $nombre . '"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Nº', 'Patente', 'Equipo', 'Transportista', 'Fecha calib.', 'Vto. calib.', 'Calibrador', 'Estado'], ';');
        foreach ($filas as $r) {
            fputcsv($out, [
                $r['numero'] ?? '',
                $r['patente'] ?? '',
                $r['equipo'] ?? '',
                $r['transportista'] ?? '',
                $r['fecha_calib'] ?? '',
                $r['vto_calib'] ?? '',
                $r['calibrador'] ?? '',
                $r['estado'] ?? '',
            ], ';');
        }
        fclose($out);

        return $this->response;
    }

    /**
     * Reporte de vencimientos (próximos X días).
     */
    public function vencimientos()
    {
        $data = [
            'titulo' => 'Reporte de Vencimientos',
        ];
        return view('reportes/vencimientos', $data);
    }

    /**
     * Listar vencimientos para el reporte (AJAX).
     * POST dias: número (7, 30, 60, 90) = próximos X días, o "vencidos" = solo ya vencidas.
     */
    public function listarVencimientos()
    {
        $diasParam = $this->request->getPost('dias');
        if ($diasParam === 'vencidos') {
            $filas = $this->calibracionModel->listarVencidos(500);
        } else {
            $dias = (int) $diasParam;
            if ($dias < 1) {
                $dias = 30;
            }
            if ($dias > 365) {
                $dias = 365;
            }
            $filas = $this->calibracionModel->listarPorVencer($dias, 500);
        }

        return $this->response->setJSON([
            'success' => true,
            'data'    => $filas,
        ]);
    }

    /**
     * Exportar reporte de vencimientos a CSV.
     * GET dias: número (7, 30, 60, 90) o "vencidos".
     */
    public function exportarVencimientosCsv()
    {
        $diasParam = $this->request->getGet('dias');
        if ($diasParam === 'vencidos') {
            $filas = $this->calibracionModel->listarVencidos(1000);
        } else {
            $dias = (int) ($diasParam ?? 30);
            if ($dias < 1) {
                $dias = 30;
            }
            if ($dias > 365) {
                $dias = 365;
            }
            $filas = $this->calibracionModel->listarPorVencer($dias, 1000);
        }

        $nombre = 'reporte_vencimientos_' . date('Y-m-d_His') . '.csv';
        $this->response->setHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $nombre . '"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Nº calib.', 'Patente', 'Equipo', 'Transportista', 'Fecha vto.', 'Días restantes'], ';');
        foreach ($filas as $r) {
            fputcsv($out, [
                $r['id_calibracion'] ?? '',
                $r['patente'] ?? '',
                $r['equipo'] ?? '',
                $r['transportista'] ?? '',
                $r['fecha_vencimiento'] ?? '',
                $r['dias_restantes'] ?? '',
            ], ';');
        }
        fclose($out);

        return $this->response;
    }

    /**
     * Reporte de flota (equipos).
     */
    public function flota()
    {
        $transportistas = [];
        try {
            $transportistas = $this->transportistasModel->listarTodos();
        } catch (\Throwable $e) {
            // ignore
        }
        $naciones = [];
        try {
            $naciones = $this->nacionesModel->orderBy('nacion', 'ASC')->findAll();
        } catch (\Throwable $e) {
            // ignore
        }
        $data = [
            'titulo'         => 'Reporte de Flota (Equipos)',
            'transportistas' => $transportistas,
            'naciones'       => $naciones,
        ];
        return view('reportes/flota', $data);
    }

    /**
     * Listar flota (equipos) para el reporte (AJAX).
     */
    public function listarFlota()
    {
        $id_tta = $this->request->getPost('id_tta') ?: null;
        if ($id_tta !== null && $id_tta !== '') {
            $id_tta = (int) $id_tta;
        } else {
            $id_tta = null;
        }
        $bitren = $this->request->getPost('bitren') ?: null;
        if ($bitren !== null && $bitren !== '') {
            $bitren = $bitren === 'SI' || $bitren === 'NO' ? $bitren : null;
        } else {
            $bitren = null;
        }
        $nacion = $this->request->getPost('nacion') ?: null;
        if ($nacion !== null && $nacion === '') {
            $nacion = null;
        }
        $filas = $this->equiposModel->listarParaReporteFlota($id_tta, $bitren, $nacion);
        return $this->response->setJSON([
            'success' => true,
            'data'    => $filas,
        ]);
    }

    /**
     * Exportar reporte de flota a CSV.
     */
    public function exportarFlotaCsv()
    {
        $id_tta = $this->request->getGet('id_tta') ?: null;
        if ($id_tta !== null && $id_tta !== '') {
            $id_tta = (int) $id_tta;
        } else {
            $id_tta = null;
        }
        $bitren = $this->request->getGet('bitren') ?: null;
        if ($bitren !== null && $bitren !== '') {
            $bitren = $bitren === 'SI' || $bitren === 'NO' ? $bitren : null;
        } else {
            $bitren = null;
        }
        $nacion = $this->request->getGet('nacion') ?: null;
        if ($nacion !== null && $nacion === '') {
            $nacion = null;
        }
        $filas = $this->equiposModel->listarParaReporteFlota($id_tta, $bitren, $nacion);

        $nombre = 'reporte_flota_' . date('Y-m-d_His') . '.csv';
        $this->response->setHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $nombre . '"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Patente', 'Transportista', 'Bitren', 'Fecha alta', 'Modo carga', 'Nación', 'Tractor', 'Semi delan.', 'Semi trasero', 'Tara total', 'Peso máx.', 'Capacidad total'], ';');
        foreach ($filas as $r) {
            fputcsv($out, [
                $r['patente'] ?? '',
                $r['transportista'] ?? '',
                $r['bitren'] ?? '',
                $r['fecha_alta'] ?? '',
                $r['modo_carga'] ?? '',
                $r['nacion'] ?? '',
                $r['tractor_patente'] ?? '',
                $r['semi_delan_patente'] ?? '',
                $r['semi_trasero_patente'] ?? '',
                $r['tara_total'] ?? '',
                $r['peso_maximo'] ?? '',
                $r['capacidad_total'] ?? '',
            ], ';');
        }
        fclose($out);

        return $this->response;
    }

    /**
     * Reporte de transportistas (con cant. equipos).
     */
    public function transportistas()
    {
        $data = [
            'titulo' => 'Reporte de Transportistas',
        ];
        return view('reportes/transportistas', $data);
    }

    /**
     * Listar transportistas para el reporte (AJAX).
     */
    public function listarTransportistas()
    {
        $cantEquipos = $this->request->getPost('cant_equipos') ?: null;
        $validos = ['5', '5-10', '10-15', '15plus'];
        if ($cantEquipos !== null && $cantEquipos !== '' && ! in_array($cantEquipos, $validos, true)) {
            $cantEquipos = null;
        }
        $filas = $this->transportistasModel->listarParaReporte($cantEquipos);
        return $this->response->setJSON([
            'success' => true,
            'data'    => $filas,
        ]);
    }

    /**
     * Exportar reporte de transportistas a CSV.
     */
    public function exportarTransportistasCsv()
    {
        $cantEquipos = $this->request->getGet('cant_equipos') ?: null;
        $validos = ['5', '5-10', '10-15', '15plus'];
        if ($cantEquipos !== null && $cantEquipos !== '' && ! in_array($cantEquipos, $validos, true)) {
            $cantEquipos = null;
        }
        $filas = $this->transportistasModel->listarParaReporte($cantEquipos);

        $nombre = 'reporte_transportistas_' . date('Y-m-d_His') . '.csv';
        $this->response->setHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $nombre . '"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['Transportista', 'Dirección', 'Localidad', 'Provincia', 'Nación', 'Cant. equipos'], ';');
        foreach ($filas as $r) {
            fputcsv($out, [
                $r['transportista'] ?? '',
                $r['direccion'] ?? '',
                $r['localidad'] ?? '',
                $r['provincia'] ?? '',
                $r['nacion'] ?? '',
                $r['cant_equipos'] ?? 0,
            ], ';');
        }
        fclose($out);

        return $this->response;
    }
}
