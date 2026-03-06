<?php
declare(strict_types=1);

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

    // ─── Helpers internos ───────────────────────────────────────────

    private function filtrosCalibraciones(string $method = 'post'): array
    {
        $get = $method === 'get';
        $fechaDesde   = $get ? $this->request->getGet('fecha_desde') : $this->request->getPost('fecha_desde');
        $fechaHasta   = $get ? $this->request->getGet('fecha_hasta') : $this->request->getPost('fecha_hasta');
        $patente      = $get ? $this->request->getGet('patente') : $this->request->getPost('patente');
        $precinto     = $get ? $this->request->getGet('precinto') : $this->request->getPost('precinto');
        $idCalibrador = $get ? $this->request->getGet('id_calibrador') : $this->request->getPost('id_calibrador');

        $fechaDesde   = $fechaDesde ?: null;
        $fechaHasta   = $fechaHasta ?: null;
        $patente      = $patente ? trim($patente) : null;
        $precinto     = $precinto ? trim($precinto) : null;
        $idCalibrador = ($idCalibrador !== null && $idCalibrador !== '') ? (int) $idCalibrador : null;

        return compact('fechaDesde', 'fechaHasta', 'patente', 'precinto', 'idCalibrador');
    }

    private function filtrosFlota(string $method = 'post'): array
    {
        $get = $method === 'get';
        $id_tta = $get ? $this->request->getGet('id_tta') : $this->request->getPost('id_tta');
        $bitren = $get ? $this->request->getGet('bitren') : $this->request->getPost('bitren');
        $nacion = $get ? $this->request->getGet('nacion') : $this->request->getPost('nacion');

        $id_tta = ($id_tta !== null && $id_tta !== '') ? (int) $id_tta : null;
        if ($bitren !== null && $bitren !== '') {
            $bitren = ($bitren === 'SI' || $bitren === 'NO') ? $bitren : null;
        } else {
            $bitren = null;
        }
        $nacion = ($nacion !== null && $nacion !== '') ? $nacion : null;

        return compact('id_tta', 'bitren', 'nacion');
    }

    private function filtrosTransportistas(string $method = 'post'): ?string
    {
        $get = $method === 'get';
        $cantEquipos = $get ? $this->request->getGet('cant_equipos') : $this->request->getPost('cant_equipos');
        $cantEquipos = $cantEquipos ?: null;
        $validos = ['5', '5-10', '10-15', '15plus'];
        if ($cantEquipos !== null && $cantEquipos !== '' && ! in_array($cantEquipos, $validos, true)) {
            $cantEquipos = null;
        }
        return $cantEquipos;
    }

    /**
     * Genera y envía un CSV con headers, filas y mapeo de columnas.
     */
    protected function exportarCsv(string $nombre, array $headers, array $filas, callable $mapRow)
    {
        $this->response->setHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $nombre . '"');

        $out = fopen('php://output', 'w');
        fputcsv($out, $headers, ';');
        foreach ($filas as $r) {
            fputcsv($out, $mapRow($r), ';');
        }
        fclose($out);

        return $this->response;
    }

    // ─── Vistas ─────────────────────────────────────────────────────

    public function index()
    {
        return view('reportes/index', ['titulo' => 'Reportes']);
    }

    public function calibraciones()
    {
        $calibradores = [];
        try {
            $calibradores = $this->calibradoresModel->listarTodos();
        } catch (\Throwable $e) {
        }
        return view('reportes/calibraciones', [
            'titulo'       => 'Reporte de Calibraciones',
            'calibradores' => $calibradores,
        ]);
    }

    public function vencimientos()
    {
        return view('reportes/vencimientos', ['titulo' => 'Reporte de Vencimientos']);
    }

    public function flota()
    {
        $transportistas = [];
        try {
            $transportistas = $this->transportistasModel->listarTodos();
        } catch (\Throwable $e) {
        }
        $naciones = [];
        try {
            $naciones = $this->nacionesModel->orderBy('nacion', 'ASC')->findAll();
        } catch (\Throwable $e) {
        }
        return view('reportes/flota', [
            'titulo'         => 'Reporte de Flota (Equipos)',
            'transportistas' => $transportistas,
            'naciones'       => $naciones,
        ]);
    }

    public function transportistas()
    {
        return view('reportes/transportistas', ['titulo' => 'Reporte de Transportistas']);
    }

    // ─── AJAX listar ────────────────────────────────────────────────

    public function listarCalibraciones()
    {
        try {
            $f = $this->filtrosCalibraciones('post');
            if (! $this->esFechaYmdValida($f['fechaDesde'])) {
                return $this->response->setJSON(json_response(false, ['message' => 'Fecha desde inválida (formato esperado: YYYY-MM-DD)']));
            }
            if (! $this->esFechaYmdValida($f['fechaHasta'])) {
                return $this->response->setJSON(json_response(false, ['message' => 'Fecha hasta inválida (formato esperado: YYYY-MM-DD)']));
            }
            if (! $this->esIdPositivo($f['idCalibrador'])) {
                return $this->response->setJSON(json_response(false, ['message' => 'ID de calibrador inválido']));
            }
            $filas = $this->calibracionModel->listarParaReporte($f['fechaDesde'], $f['fechaHasta'], $f['patente'] ?: null, $f['idCalibrador'], $f['precinto'] ?: null);
            return $this->response->setJSON(json_response(true, ['data' => $filas]));
        } catch (\Throwable $e) {
            return $this->response->setJSON(json_response(false, ['message' => 'Error al listar calibraciones: ' . $e->getMessage()]));
        }
    }

    public function listarVencimientos()
    {
        try {
            $diasParam = $this->request->getPost('dias');
            if ($diasParam === 'vencidos') {
                $filas = $this->calibracionModel->listarVencidos(500);
            } else {
                $dias = max(1, min(365, (int) $diasParam ?: 30));
                $filas = $this->calibracionModel->listarPorVencer($dias, 500);
            }
            return $this->response->setJSON(json_response(true, ['data' => $filas]));
        } catch (\Throwable $e) {
            return $this->response->setJSON(json_response(false, ['message' => 'Error al listar vencimientos: ' . $e->getMessage()]));
        }
    }

    public function listarFlota()
    {
        try {
            $f = $this->filtrosFlota('post');
            $filas = $this->equiposModel->listarParaReporteFlota($f['id_tta'], $f['bitren'], $f['nacion']);
            return $this->response->setJSON(json_response(true, ['data' => $filas]));
        } catch (\Throwable $e) {
            return $this->response->setJSON(json_response(false, ['message' => 'Error al listar flota: ' . $e->getMessage()]));
        }
    }

    public function listarTransportistas()
    {
        try {
            $cantEquipos = $this->filtrosTransportistas('post');
            $filas = $this->transportistasModel->listarParaReporte($cantEquipos);
            return $this->response->setJSON(json_response(true, ['data' => $filas]));
        } catch (\Throwable $e) {
            return $this->response->setJSON(json_response(false, ['message' => 'Error al listar transportistas: ' . $e->getMessage()]));
        }
    }

    // ─── CSV exports ────────────────────────────────────────────────

    public function exportarCalibracionesCsv()
    {
        $f = $this->filtrosCalibraciones('get');
        if (! $this->esFechaYmdValida($f['fechaDesde']) || ! $this->esFechaYmdValida($f['fechaHasta'])) {
            return $this->response->setStatusCode(400)->setBody('Fechas inválidas');
        }
        if (! $this->esIdPositivo($f['idCalibrador'])) {
            return $this->response->setStatusCode(400)->setBody('ID de calibrador inválido');
        }
        $filas = $this->calibracionModel->listarParaReporte($f['fechaDesde'], $f['fechaHasta'], $f['patente'] ?: null, $f['idCalibrador'], $f['precinto'] ?: null);

        return $this->exportarCsv(
            'reporte_calibraciones_' . date('Y-m-d_His') . '.csv',
            ['Nº', 'Patente', 'Equipo', 'Transportista', 'Fecha calib.', 'Vto. calib.', 'Calibrador', 'Estado'],
            $filas,
            fn($r) => [$r['numero'] ?? '', $r['patente'] ?? '', $r['equipo'] ?? '', $r['transportista'] ?? '', $r['fecha_calib'] ?? '', $r['vto_calib'] ?? '', $r['calibrador'] ?? '', $r['estado'] ?? '']
        );
    }

    public function exportarVencimientosCsv()
    {
        $diasParam = $this->request->getGet('dias');
        if ($diasParam === 'vencidos') {
            $filas = $this->calibracionModel->listarVencidos(1000);
        } else {
            $dias = max(1, min(365, (int) ($diasParam ?? 30)));
            $filas = $this->calibracionModel->listarPorVencer($dias, 1000);
        }

        return $this->exportarCsv(
            'reporte_vencimientos_' . date('Y-m-d_His') . '.csv',
            ['Nº calib.', 'Patente', 'Equipo', 'Transportista', 'Fecha vto.', 'Días restantes'],
            $filas,
            fn($r) => [$r['id_calibracion'] ?? '', $r['patente'] ?? '', $r['equipo'] ?? '', $r['transportista'] ?? '', $r['fecha_vencimiento'] ?? '', $r['dias_restantes'] ?? '']
        );
    }

    public function exportarFlotaCsv()
    {
        $f = $this->filtrosFlota('get');
        $filas = $this->equiposModel->listarParaReporteFlota($f['id_tta'], $f['bitren'], $f['nacion']);

        return $this->exportarCsv(
            'reporte_flota_' . date('Y-m-d_His') . '.csv',
            ['Patente', 'Transportista', 'Bitren', 'Fecha alta', 'Modo carga', 'Nación', 'Tractor', 'Semi delan.', 'Semi trasero', 'Tara total', 'Peso máx.', 'Capacidad total'],
            $filas,
            fn($r) => [$r['patente'] ?? '', $r['transportista'] ?? '', $r['bitren'] ?? '', $r['fecha_alta'] ?? '', $r['modo_carga'] ?? '', $r['nacion'] ?? '', $r['tractor_patente'] ?? '', $r['semi_delan_patente'] ?? '', $r['semi_trasero_patente'] ?? '', $r['tara_total'] ?? '', $r['peso_maximo'] ?? '', $r['capacidad_total'] ?? '']
        );
    }

    public function exportarTransportistasCsv()
    {
        $cantEquipos = $this->filtrosTransportistas('get');
        $filas = $this->transportistasModel->listarParaReporte($cantEquipos);

        return $this->exportarCsv(
            'reporte_transportistas_' . date('Y-m-d_His') . '.csv',
            ['Transportista', 'Dirección', 'Localidad', 'Provincia', 'Nación', 'Cant. equipos'],
            $filas,
            fn($r) => [$r['transportista'] ?? '', $r['direccion'] ?? '', $r['localidad'] ?? '', $r['provincia'] ?? '', $r['nacion'] ?? '', $r['cant_equipos'] ?? 0]
        );
    }
}
