<?php

namespace App\Controllers;

use App\Models\CalibracionModel;
use App\Models\CalibracionAccesoModel;
use App\Models\CalibracionDetalleModel;
use App\Models\CalibracionInformeCargaSeguraModel;
use App\Models\CalibracionInformeCargaSeguraDetalleModel;
use App\Models\CalibracionMultiflechaModel;
use App\Models\CalibracionNotasModel;
use App\Models\CalibracionReimpresionModel;
use App\Models\EquiposModel;
use App\Models\TransportistasModel;
use App\Models\CalibradoresModel;
use App\Models\CubiertasModel;
use App\Models\MarcasModel;
use App\Models\ReglasModel;

class Calibracion extends BaseController
{
    /**
     * Enriquece calibración con unidad, transportista y nombre de calibrador para vista/impresión.
     */
    private function enriquecerParaVista(array $cal): array
    {
        if (! empty($cal['id_calibrador'])) {
            $calibrador = model(CalibradoresModel::class)->find((int) $cal['id_calibrador']);
            $cal['calibrador_nombre'] = $calibrador['calibrador'] ?? null;
        } else {
            $cal['calibrador_nombre'] = null;
        }
        $cal['unidad'] = null;
        $cal['transportista'] = null;
        if (! empty($cal['patente'])) {
            $unidad = model(EquiposModel::class)->where('patente_semi_delantero', $cal['patente'])->first();
            if ($unidad) {
                if (! empty($unidad['id_marca'])) {
                    $marca = model(MarcasModel::class)->find((int) $unidad['id_marca']);
                    $unidad['marca_nombre'] = $marca['marca'] ?? null;
                } else {
                    $unidad['marca_nombre'] = null;
                }
                $unidad = $this->agregarMedidasCubiertasEnUnidad($unidad);
                $cal['unidad'] = $unidad;
                if (! empty($unidad['id_tta'])) {
                    $tta = model(TransportistasModel::class)->find((int) $unidad['id_tta']);
                    if ($tta) {
                        $cal['transportista'] = $tta['transportista'] ?? null;
                        $cal['transportista_cuit'] = $tta['cuit'] ?? null;
                        $cal['transportista_domicilio'] = $tta['direccion'] ?? null;
                        $cal['transportista_localidad'] = $tta['localidad'] ?? null;
                        $cal['transportista_provincia'] = $tta['provincia'] ?? null;
                        $cal['transportista_codigo'] = $tta['codigo_postal'] ?? null;
                    }
                }
            }
        }
        return $cal;
    }

    /**
     * Añade a la unidad los textos "medida" de cada cubierta (para mostrar en vista/impresión).
     * Los campos cubierta_*_eje* guardan el ID; aquí se resuelve contra la tabla cubiertas y se
     * agrega cubierta_*_eje*_medida con el texto (ej. "315/80X22,5").
     */
    private function agregarMedidasCubiertasEnUnidad(array $unidad): array
    {
        $campos = [
            'cubierta_tractor_eje1', 'cubierta_tractor_eje2', 'cubierta_tractor_eje3',
            'cubierta_semi_delantero_eje1', 'cubierta_semi_delantero_eje2', 'cubierta_semi_delantero_eje3',
            'cubierta_semi_trasero_eje1', 'cubierta_semi_trasero_eje2', 'cubierta_semi_trasero_eje3',
        ];
        $cubiertasModel = model(CubiertasModel::class);
        foreach ($campos as $campo) {
            $id = isset($unidad[$campo]) ? (int) $unidad[$campo] : 0;
            $unidad[$campo . '_medida'] = null;
            if ($id > 0) {
                $cub = $cubiertasModel->find($id);
                $unidad[$campo . '_medida'] = $cub['medida'] ?? null;
            }
        }
        return $unidad;
    }

    public function index()
    {
        $reglaHabilitada = model(ReglasModel::class)->obtenerHabilitada();
        $puedeUsarInformeCargaSegura = false;
        if (\Config\Database::connect()->tableExists('marcas_sensor')) {
            $total = model(\App\Models\MarcasSensorModel::class)->obtenerTotal();
            $puedeUsarInformeCargaSegura = $total > 0;
        }
        $data = [
            'titulo'                         => 'Calibración',
            'regla_habilitada_numero'        => $reglaHabilitada['numero_regla'] ?? '',
            'puede_usar_informe_carga_segura' => $puedeUsarInformeCargaSegura,
        ];
        return view('calibracion/index', $data);
    }

    /**
     * Listar calibraciones para DataTable (POST). Parámetros opcionales: numero, patente.
     */
    public function listar()
    {
        $numero = $this->request->getPost('numero');
        $patente = $this->request->getPost('patente');
        if (is_string($numero)) {
            $numero = trim($numero) === '' ? null : $numero;
        }
        if (is_string($patente)) {
            $patente = trim($patente) === '' ? null : $patente;
        }

        try {
            $model = model(CalibracionModel::class);
            $data = $model->listarParaDataTable($numero, $patente);
            return $this->response->setJSON(['data' => $data]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'data' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener una calibración por ID con detalle (para edición).
     */
    public function obtener($id = null)
    {
        if (! $id || (int) $id <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID no proporcionado'
            ]);
        }

        try {
            $model = model(CalibracionModel::class);
            $cal = $model->obtenerPorIdConDetalle((int) $id);
            if ($cal) {
                $cal = $this->enriquecerParaVista($cal);
                return $this->response->setJSON([
                    'success' => true,
                    'data' => $cal
                ]);
            }
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Calibración no encontrada'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener la última calibración por patente (para autocompletar formulario). GET ?patente=XXX
     */
    public function ultimaPorPatente()
    {
        $patente = $this->request->getGet('patente');
        if (! is_string($patente) || trim($patente) === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Patente no proporcionada'
            ]);
        }

        try {
            $model = model(CalibracionModel::class);
            $cal = $model->obtenerUltimaPorPatente(trim($patente));
            if ($cal) {
                $cal = $this->enriquecerParaVista($cal);
                return $this->response->setJSON([
                    'success' => true,
                    'data' => $cal
                ]);
            }
            return $this->response->setJSON([
                'success' => false,
                'data' => null
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Guardar calibración (crear o actualizar). Recibe cabecera + detalle en POST o JSON.
     */
    public function guardar()
    {
        $input = null;
        $contentType = $this->request->getHeaderLine('Content-Type');
        if (strpos($contentType, 'application/json') !== false) {
            try {
                $input = $this->request->getJSON(true);
            } catch (\Throwable $e) {
                $input = null;
            }
        }
        if (! is_array($input) || $input === []) {
            $cap = $this->request->getPost('detalle_capacidad') ?? [];
            $enrase = $this->request->getPost('detalle_enrase') ?? [];
            $referen = $this->request->getPost('detalle_referen') ?? [];
            $vacio = $this->request->getPost('detalle_vacio') ?? [];
            $vacioLts = $this->request->getPost('detalle_vacio_lts') ?? [];
            $pc = $this->request->getPost('detalle_precinto_campana') ?? [];
            $ps = $this->request->getPost('detalle_precinto_soporte') ?? [];
            $ph = $this->request->getPost('detalle_precinto_hombre') ?? [];
            $pu = $this->request->getPost('detalle_precinto_ultima') ?? [];
            $detalle = [];
            $n = is_array($cap) ? count($cap) : 0;
            for ($i = 0; $i < $n; $i++) {
                $detalle[] = [
                    'capacidad' => $cap[$i] ?? 0,
                    'enrase' => $enrase[$i] ?? 0,
                    'referen' => $referen[$i] ?? 0,
                    'vacio_calc' => $vacio[$i] ?? null,
                    'vacio_lts' => $vacioLts[$i] ?? null,
                    'precinto_campana' => $pc[$i] ?? null,
                    'precinto_soporte' => $ps[$i] ?? null,
                    'precinto_hombre' => $ph[$i] ?? null,
                    'precinto_ultima' => $pu[$i] ?? null
                ];
            }
            $multiflechaJson = $this->request->getPost('multiflecha_por_cisterna');
            $multiflechaPorCisterna = is_string($multiflechaJson) ? json_decode($multiflechaJson, true) : null;
            if (! is_array($multiflechaPorCisterna)) {
                $multiflechaPorCisterna = [];
            }
            $multiflechaSemi2Json = $this->request->getPost('multiflecha_por_cisterna_semi2');
            $multiflechaSemi2 = is_string($multiflechaSemi2Json) ? json_decode($multiflechaSemi2Json, true) : null;
            if (! is_array($multiflechaSemi2)) {
                $multiflechaSemi2 = [];
            }
            $detalleSemi2 = [];
            $cap2 = $this->request->getPost('detalle_semi2_capacidad') ?? [];
            $n2 = is_array($cap2) ? count($cap2) : 0;
            for ($i = 0; $i < $n2; $i++) {
                $detalleSemi2[] = [
                    'capacidad' => $cap2[$i] ?? 0,
                    'enrase' => ($this->request->getPost('detalle_semi2_enrase') ?? [])[$i] ?? 0,
                    'referen' => ($this->request->getPost('detalle_semi2_referen') ?? [])[$i] ?? 0,
                    'vacio_calc' => ($this->request->getPost('detalle_semi2_vacio') ?? [])[$i] ?? null,
                    'vacio_lts' => ($this->request->getPost('detalle_semi2_vacio_lts') ?? [])[$i] ?? null,
                    'precinto_campana' => ($this->request->getPost('detalle_semi2_precinto_campana') ?? [])[$i] ?? null,
                    'precinto_soporte' => ($this->request->getPost('detalle_semi2_precinto_soporte') ?? [])[$i] ?? null,
                    'precinto_hombre' => ($this->request->getPost('detalle_semi2_precinto_hombre') ?? [])[$i] ?? null,
                    'precinto_ultima' => ($this->request->getPost('detalle_semi2_precinto_ultima') ?? [])[$i] ?? null
                ];
            }
            $input = [
                'id_calibracion'         => $this->request->getPost('id_calibracion'),
                'patente'                => $this->request->getPost('patente'),
                'fecha_calib'            => $this->request->getPost('fecha_calib'),
                'vto_calib'              => $this->request->getPost('vto_calib'),
                'id_calibrador'          => $this->request->getPost('id_calibrador'),
                'temp_agua'              => $this->request->getPost('temp_agua'),
                'valvulas'               => $this->request->getPost('valvulas'),
                'observaciones'          => $this->request->getPost('observaciones'),
                'tipo_unidad'             => $this->request->getPost('tipo_unidad'),
                'multi_flecha'           => $this->request->getPost('multi_flecha'),
                'multi_flecha_semi2'     => $this->request->getPost('multi_flecha_semi2'),
                'n_regla'                => $this->request->getPost('n_regla'),
                'detalle'                => $detalle,
                'detalle_semi2'          => $detalleSemi2,
                'multiflecha_por_cisterna' => $multiflechaPorCisterna,
                'multiflecha_por_cisterna_semi2' => $multiflechaSemi2
            ];
        }

        $patente = trim($input['patente'] ?? '');
        if ($patente === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'La patente es obligatoria',
                'errors' => ['patente' => 'La patente es obligatoria']
            ]);
        }

        $cabecera = [
            'id_calibracion' => $input['id_calibracion'] ?? '',
            'patente' => $patente,
            'fecha_calib' => ! empty($input['fecha_calib']) ? $input['fecha_calib'] : null,
            'vto_calib' => ! empty($input['vto_calib']) ? $input['vto_calib'] : null,
            'id_calibrador' => ! empty($input['id_calibrador']) ? (int) $input['id_calibrador'] : null,
            'temp_agua' => isset($input['temp_agua']) && $input['temp_agua'] !== '' ? $input['temp_agua'] : null,
            'valvulas' => isset($input['valvulas']) && $input['valvulas'] !== '' ? ($input['valvulas'] === 'CERRADAS' ? '0' : '1') : null,
            'observaciones' => $input['observaciones'] ?? null,
            'tipo_unidad' => ! empty($input['tipo_unidad']) ? $input['tipo_unidad'] : null,
            'multi_flecha' => ! empty($input['multi_flecha']) ? $input['multi_flecha'] : null,
            'multi_flecha_semi2' => ! empty($input['multi_flecha_semi2']) ? $input['multi_flecha_semi2'] : null,
            'n_regla' => $input['n_regla'] ?? null
        ];

        $detalle = $input['detalle'] ?? [];
        if (! is_array($detalle)) {
            $detalle = [];
        }
        $detalle_semi2 = $input['detalle_semi2'] ?? [];
        if (! is_array($detalle_semi2)) {
            $detalle_semi2 = [];
        }
        $multiflechaPorCisterna = $input['multiflecha_por_cisterna'] ?? [];
        if (! is_array($multiflechaPorCisterna)) {
            $multiflechaPorCisterna = [];
        }
        $multiflechaPorCisternaSemi2 = $input['multiflecha_por_cisterna_semi2'] ?? [];
        if (! is_array($multiflechaPorCisternaSemi2)) {
            $multiflechaPorCisternaSemi2 = [];
        }

        $idExistente = isset($cabecera['id_calibracion']) ? (int) $cabecera['id_calibracion'] : 0;
        $multiFlechaActivo = ! empty($cabecera['multi_flecha']) && strtoupper((string) $cabecera['multi_flecha']) === 'SI';
        $numLineas = count($detalle);
        if ($multiFlechaActivo && $numLineas > 0) {
            if ($idExistente <= 0) {
                for ($n = 1; $n <= $numLineas; $n++) {
                    $mf = $multiflechaPorCisterna[$n] ?? $multiflechaPorCisterna[(string) $n] ?? null;
                    if (! is_array($mf) || count($mf) === 0) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Con multiflecha activo, debe cargar la información de multiflecha para todas las cisternas. Falta cargar datos MF para la cisterna ' . $n . '. Use el botón MF de cada línea.'
                        ]);
                    }
                }
            } else {
                $modelMf = model(CalibracionMultiflechaModel::class);
                $cisternasSinMf = [];
                for ($n = 1; $n <= $numLineas; $n++) {
                    $mfEnviado = $multiflechaPorCisterna[$n] ?? $multiflechaPorCisterna[(string) $n] ?? null;
                    if (is_array($mfEnviado) && count($mfEnviado) > 0) {
                        continue;
                    }
                    $rows = $modelMf->listarPorCalibracionYCisterna($idExistente, $n);
                    if ($rows === []) {
                        $cisternasSinMf[] = 'C' . $n;
                    }
                }
                if ($cisternasSinMf !== []) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Con multiflecha activo, todas las cisternas deben tener datos de multiflecha cargados. Faltan: ' . implode(', ', $cisternasSinMf) . '. Use el botón MF de cada línea.'
                    ]);
                }
            }
        }

        $multiFlechaSemi2Activo = ! empty($cabecera['multi_flecha_semi2']) && strtoupper((string) $cabecera['multi_flecha_semi2']) === 'SI';
        $numLineasSemi2 = count($detalle_semi2);
        if ($multiFlechaSemi2Activo && $numLineasSemi2 > 0) {
            if ($idExistente <= 0) {
                for ($n = 1; $n <= $numLineasSemi2; $n++) {
                    $mf = $multiflechaPorCisternaSemi2[$n] ?? $multiflechaPorCisternaSemi2[(string) $n] ?? null;
                    if (! is_array($mf) || count($mf) === 0) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Con multiflecha activo en 2do semi, debe cargar la información de multiflecha para todas las cisternas del 2do semi. Falta cargar datos MF para la cisterna ' . $n . '. Use el botón MF de cada línea del 2do semi.'
                        ]);
                    }
                }
            } else {
                $modelMf = model(CalibracionMultiflechaModel::class);
                $cisternasSinMfSemi2 = [];
                for ($n = 1; $n <= $numLineasSemi2; $n++) {
                    $mfEnviado = $multiflechaPorCisternaSemi2[$n] ?? $multiflechaPorCisternaSemi2[(string) $n] ?? null;
                    if (is_array($mfEnviado) && count($mfEnviado) > 0) {
                        continue;
                    }
                    $rows = $modelMf->listarPorCalibracionYCisterna($idExistente, $n, 2);
                    if ($rows === []) {
                        $cisternasSinMfSemi2[] = 'C' . $n;
                    }
                }
                if ($cisternasSinMfSemi2 !== []) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Con multiflecha activo en 2do semi, todas las cisternas del 2do semi deben tener datos de multiflecha. Faltan: ' . implode(', ', $cisternasSinMfSemi2) . '. Use el botón MF de cada línea del 2do semi.'
                    ]);
                }
            }
        }

        try {
            $model = model(CalibracionModel::class);
            $idCalib = $model->guardarCalibracion($cabecera, $detalle, $detalle_semi2);
            $hoy = date('Y-m-d');
            if ($idCalib > 0 && ! empty($cabecera['vto_calib']) && $cabecera['vto_calib'] >= $hoy) {
                $model->marcarAnterioresComoRecalibradas($patente, $idCalib, $hoy);
            }
            if ($idCalib > 0) {
                $modelMf = model(CalibracionMultiflechaModel::class);
                foreach ($multiflechaPorCisterna as $numeroLinea => $lineas) {
                    $numeroLinea = (int) $numeroLinea;
                    if ($numeroLinea <= 0 || ! is_array($lineas)) {
                        continue;
                    }
                    $modelMf->eliminarPorCalibracionYCisterna($idCalib, $numeroLinea, 1);
                    foreach ($lineas as $i => $lin) {
                        $numMf = isset($lin['numero_multiflecha']) ? (int) $lin['numero_multiflecha'] : ($i + 1);
                        $modelMf->insert([
                            'id_calibracion'     => $idCalib,
                            'numero_semi'        => 1,
                            'numero_linea'       => $numeroLinea,
                            'numero_multiflecha' => $numMf,
                            'capacidad'          => $lin['capacidad'] ?? 0,
                            'enrase'             => $lin['enrase'] ?? 0,
                            'referen'            => $lin['referen'] ?? 0,
                            'vacio_calc'         => $lin['vacio_calc'] ?? null,
                            'vacio_lts'          => $lin['vacio_lts'] ?? null,
                            'precinto_campana'   => $lin['precinto_campana'] ?? null,
                            'precinto_soporte'   => $lin['precinto_soporte'] ?? null,
                            'precinto_hombre'    => $lin['precinto_hombre'] ?? null
                        ]);
                    }
                }
                foreach ($multiflechaPorCisternaSemi2 as $numeroLinea => $lineas) {
                    $numeroLinea = (int) $numeroLinea;
                    if ($numeroLinea <= 0 || ! is_array($lineas)) {
                        continue;
                    }
                    $modelMf->eliminarPorCalibracionYCisterna($idCalib, $numeroLinea, 2);
                    foreach ($lineas as $i => $lin) {
                        $numMf = isset($lin['numero_multiflecha']) ? (int) $lin['numero_multiflecha'] : ($i + 1);
                        $modelMf->insert([
                            'id_calibracion'     => $idCalib,
                            'numero_semi'        => 2,
                            'numero_linea'       => $numeroLinea,
                            'numero_multiflecha' => $numMf,
                            'capacidad'          => $lin['capacidad'] ?? 0,
                            'enrase'             => $lin['enrase'] ?? 0,
                            'referen'            => $lin['referen'] ?? 0,
                            'vacio_calc'         => $lin['vacio_calc'] ?? null,
                            'vacio_lts'          => $lin['vacio_lts'] ?? null,
                            'precinto_campana'   => $lin['precinto_campana'] ?? null,
                            'precinto_soporte'   => $lin['precinto_soporte'] ?? null,
                            'precinto_hombre'    => $lin['precinto_hombre'] ?? null
                        ]);
                    }
                }
            }
            return $this->response->setJSON([
                'success'       => true,
                'message'       => $cabecera['id_calibracion'] ? 'Calibración actualizada correctamente' : 'Calibración creada correctamente',
                'id_calibracion' => $idCalib
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al guardar: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Eliminar calibración por ID.
     */
    public function eliminar($id = null)
    {
        if (! $id || (int) $id <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID no proporcionado'
            ]);
        }

        try {
            $model = model(CalibracionModel::class);
            $model->eliminarCalibracion((int) $id);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Calibración eliminada correctamente'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener detalle multiflecha de una cisterna (para el modal MF).
     * GET calibracion/multiflecha/{id_calibracion}/{numero_linea} o .../{id}/{num_linea}/{numero_semi}
     */
    public function getMultiflecha($idCalibracion, $numeroLinea, $numeroSemi = 1)
    {
        $idCalibracion = (int) $idCalibracion;
        $numeroLinea   = (int) $numeroLinea;
        $numeroSemi    = (int) $numeroSemi;
        if ($numeroSemi < 1) {
            $numeroSemi = 1;
        }
        if ($numeroSemi > 2) {
            $numeroSemi = 2;
        }
        if ($idCalibracion <= 0 || $numeroLinea <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Parámetros inválidos', 'data' => []]);
        }
        $model = model(CalibracionMultiflechaModel::class);
        $rows  = $model->listarPorCalibracionYCisterna($idCalibracion, $numeroLinea, $numeroSemi);
        return $this->response->setJSON(['success' => true, 'data' => $rows]);
    }

    /**
     * Obtener notas del calibrador de una calibración.
     * GET calibracion/notas/(:num)
     */
    public function getNotas($idCalibracion)
    {
        $idCalibracion = (int) $idCalibracion;
        if ($idCalibracion <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID inválido', 'notas' => '', 'updated_at' => null, 'usuario' => null]);
        }
        $model = model(CalibracionNotasModel::class);
        $row = $model->getPorCalibracion($idCalibracion);
        $notas = $row['notas'] ?? '';
        $updatedAt = $row['updated_at'] ?? $row['created_at'] ?? null;
        $idUsuario = ! empty($row['id_usuario']) ? (int) $row['id_usuario'] : null;
        return $this->response->setJSON([
            'success'    => true,
            'notas'      => $notas,
            'updated_at' => $updatedAt,
            'id_usuario' => $idUsuario,
        ]);
    }

    /**
     * Guardar notas del calibrador de una calibración.
     * POST calibracion/notas-guardar con id_calibracion y notas.
     */
    public function guardarNotas()
    {
        helper('auth');
        $usuario = usuario_actual();
        if (! $usuario || empty($usuario['id_usuario'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Debe estar logueado para guardar notas']);
        }
        $idCalibracion = (int) ($this->request->getPost('id_calibracion') ?: $this->request->getJSON(true)['id_calibracion'] ?? 0);
        $notas = $this->request->getPost('notas') !== null ? $this->request->getPost('notas') : ($this->request->getJSON(true)['notas'] ?? '');
        $notas = is_string($notas) ? $notas : '';

        if ($idCalibracion <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID de calibración no válido']);
        }

        $modelCalib = model(CalibracionModel::class);
        if (! $modelCalib->find($idCalibracion)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Calibración no encontrada']);
        }

        try {
            $model = model(CalibracionNotasModel::class);
            $model->guardarNotas($idCalibracion, $notas, (int) $usuario['id_usuario']);
            return $this->response->setJSON(['success' => true, 'message' => 'Notas guardadas']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Guardar detalle multiflecha de una cisterna (desde el modal MF).
     * POST calibracion/multiflecha-guardar con JSON: id_calibracion, numero_linea, numero_semi (opcional, 1 o 2), lineas: [...]
     */
    public function guardarMultiflecha()
    {
        $json = $this->request->getJSON(true);
        $idCalibracion = isset($json['id_calibracion']) ? (int) $json['id_calibracion'] : 0;
        $numeroLinea   = isset($json['numero_linea']) ? (int) $json['numero_linea'] : 0;
        $numeroSemi    = isset($json['numero_semi']) ? (int) $json['numero_semi'] : 1;
        if ($numeroSemi < 1) {
            $numeroSemi = 1;
        }
        if ($numeroSemi > 2) {
            $numeroSemi = 2;
        }
        $lineas = $json['lineas'] ?? [];
        if (! is_array($lineas)) {
            $lineas = [];
        }
        if ($idCalibracion <= 0 || $numeroLinea <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'id_calibracion y numero_linea son obligatorios']);
        }

        try {
            $model = model(CalibracionMultiflechaModel::class);
            $model->eliminarPorCalibracionYCisterna($idCalibracion, $numeroLinea, $numeroSemi);
            foreach ($lineas as $i => $lin) {
                $numMf = isset($lin['numero_multiflecha']) ? (int) $lin['numero_multiflecha'] : ($i + 1);
                $model->insert([
                    'id_calibracion'     => $idCalibracion,
                    'numero_semi'        => $numeroSemi,
                    'numero_linea'       => $numeroLinea,
                    'numero_multiflecha' => $numMf,
                    'capacidad'          => $lin['capacidad'] ?? 0,
                    'enrase'             => $lin['enrase'] ?? 0,
                    'referen'            => $lin['referen'] ?? 0,
                    'vacio_calc'         => $lin['vacio_calc'] ?? null,
                    'vacio_lts'          => $lin['vacio_lts'] ?? null,
                    'precinto_campana'   => $lin['precinto_campana'] ?? null,
                    'precinto_soporte'   => $lin['precinto_soporte'] ?? null,
                    'precinto_hombre'    => $lin['precinto_hombre'] ?? null
                ]);
            }
            return $this->response->setJSON(['success' => true, 'message' => 'Multiflecha guardada']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Registrar reimpresión: guarda en historial (quién, mensaje/motivo, fecha, id_calibracion).
     * POST con id_calibracion y mensaje (opcional).
     */
    public function registrarReimpresion()
    {
        $json = $this->request->getJSON(true);
        if (! is_array($json)) {
            $json = [];
        }
        $idCalibracion = (int) ($this->request->getPost('id_calibracion') ?? $json['id_calibracion'] ?? 0);
        $mensaje       = trim($this->request->getPost('mensaje') ?? $json['mensaje'] ?? '');

        if ($idCalibracion <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID de calibración no válido'
            ]);
        }

        helper('auth');
        $usuario = usuario_actual();
        if (! $usuario || empty($usuario['id_usuario'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Debe estar logueado para registrar una reimpresión'
            ]);
        }

        $modelCalib = model(CalibracionModel::class);
        if (! $modelCalib->find($idCalibracion)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Calibración no encontrada'
            ]);
        }

        try {
            $modelReimp = model(CalibracionReimpresionModel::class);
            $modelReimp->insert([
                'id_calibracion' => $idCalibracion,
                'id_usuario'     => (int) $usuario['id_usuario'],
                'mensaje'        => $mensaje === '' ? null : $mensaje
            ]);
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Reimpresión registrada'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al registrar: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Vista pública de solo lectura por token (sin login). Registra IP, user_agent, referer.
     */
    public function ver(string $token = '')
    {
        $token = trim($token);
        if ($token === '') {
            return $this->response->setStatusCode(404)->setBody(view('errors/html/error_404'));
        }

        $model = model(CalibracionModel::class);
        $cal = $model->getByToken($token);
        if (! $cal) {
            return $this->response->setStatusCode(404)->setBody(view('errors/html/error_404'));
        }

        $cal = $this->enriquecerParaVista($cal);

        model(CalibracionAccesoModel::class)->registrarAcceso(
            (int) $cal['id_calibracion'],
            $this->request->getIPAddress(),
            $this->request->getUserAgent()->getAgentString(),
            $this->request->getHeaderLine('Referer')
        );

        helper('url');
        $data = [
            'cal'    => $cal,
            'titulo' => 'Certificado de calibración',
        ];
        return view('calibracion/ver_publico', $data);
    }

    /**
     * Vista para imprimir tarjeta/certificado con QR (requiere estar en el sistema).
     * GET ?original=1 = primera impresión (marca fecha_impresion y usuario).
     * GET ?borrador=1 = vista borrador (muestra marca BORRADOR, no marca como impresa).
     */
    public function imprimir(int $id = 0)
    {
        if ($id <= 0) {
            return redirect()->to(site_url('calibracion'))->with('error', 'ID no válido');
        }

        $esOriginal = $this->request->getGet('original') === '1' || $this->request->getGet('original') === true;
        $esBorrador = $this->request->getGet('borrador') === '1' || $this->request->getGet('borrador') === true;
        $mostrarObservaciones = $this->request->getGet('incluir_observaciones') === '1' || $this->request->getGet('incluir_observaciones') === true;

        $model = model(CalibracionModel::class);
        $cal = $model->obtenerPorIdConDetalle($id);
        if (! $cal) {
            return redirect()->to(site_url('calibracion'))->with('error', 'Calibración no encontrada');
        }

        if ($esOriginal && ! $esBorrador && empty($cal['fecha_impresion'])) {
            helper('auth');
            $usuario = usuario_actual();
            if ($usuario && ! empty($usuario['id_usuario'])) {
                $model->marcarComoImpreso($id, (int) $usuario['id_usuario']);
            }
        }

        $cal = $this->enriquecerParaVista($cal);
        if (empty($cal['token_publico'])) {
            $cal['token_publico'] = CalibracionModel::generarToken();
            $model->update($id, ['token_publico' => $cal['token_publico']]);
        }
        $urlPublica = base_url('calibracion/ver/' . $cal['token_publico']);

        $esMultiflecha = ! empty($cal['multi_flecha']) && strtoupper((string) $cal['multi_flecha']) === 'SI';
        $multiflecha = [];
        if ($esMultiflecha) {
            $multiflecha = model(CalibracionMultiflechaModel::class)->listarPorCalibracion($id, 1);
        }
        $tieneDosSemis = ! empty($cal['detalle_semi2']);
        $esMultiflechaSemi2 = ! empty($cal['multi_flecha_semi2']) && strtoupper((string) $cal['multi_flecha_semi2']) === 'SI';
        $multiflechaSemi2 = [];
        if ($esMultiflechaSemi2) {
            $multiflechaSemi2 = model(CalibracionMultiflechaModel::class)->listarPorCalibracion($id, 2);
        }

        $tipoImpresion = null;
        if (! $esBorrador) {
            $tipoImpresion = $esOriginal ? 'original' : 'reimpresion';
        }

        $data = [
            'cal'                   => $cal,
            'url_publica'           => $urlPublica,
            'titulo'                => 'Certificado de calibración - Imprimir',
            'es_borrador'           => $esBorrador,
            'mostrar_observaciones' => $mostrarObservaciones,
            'es_multiflecha'        => $esMultiflecha,
            'multiflecha'           => $multiflecha,
            'tiene_dos_semis'       => $tieneDosSemis,
            'es_multiflecha_semi2'  => $esMultiflechaSemi2,
            'multiflecha_semi2'     => $multiflechaSemi2,
            'tipo_impresion'        => $tipoImpresion,
        ];
        return view('calibracion/imprimir', $data);
    }

    /**
     * Formulario para cargar datos del Informe de Carga Segura (asociado a una calibración).
     */
    public function informeCargaSegura(int $id = 0)
    {
        if ($id <= 0) {
            return redirect()->to(site_url('calibracion'))->with('error', 'ID no válido');
        }
        $model = model(CalibracionModel::class);
        $cal = $model->obtenerPorIdConDetalle($id);
        if (! $cal) {
            return redirect()->to(site_url('calibracion'))->with('error', 'Calibración no encontrada');
        }
        $cal = $this->enriquecerParaVista($cal);

        $calibracionDetalleModel = model(CalibracionDetalleModel::class);
        $todosDetalle = $calibracionDetalleModel->listarPorCalibracion($id, null);
        $detalleSemi1 = [];
        $detalleSemi2 = [];
        foreach ($todosDetalle as $fila) {
            $numSemi = (int) ($fila['numero_semi'] ?? 1);
            if ($numSemi === 2) {
                $detalleSemi2[] = $fila;
            } else {
                $detalleSemi1[] = $fila;
            }
        }
        $cal['detalle'] = $detalleSemi1;
        $cal['detalle_semi2'] = $detalleSemi2;

        $informeModel = model(CalibracionInformeCargaSeguraModel::class);
        $detalleModel = model(CalibracionInformeCargaSeguraDetalleModel::class);
        $informe = $informeModel->porCalibracion($id);
        $detalle = $informe ? $detalleModel->listarPorCalibracion($id) : [];
        $marcasSensor = [];
        if (\Config\Database::connect()->tableExists('marcas_sensor')) {
            $marcasSensor = model(\App\Models\MarcasSensorModel::class)->listarTodos();
        }
        if (count($marcasSensor) === 0) {
            return redirect()->to(site_url('calibracion'))->with('error', 'Para cargar el Informe de Carga Segura debe haber al menos una marca de sensor cargada. Cargue marcas en Marcas sensor y vuelva a intentar.');
        }

        $numCisternasSemi1 = count($detalleSemi1);
        $numCisternasSemi2 = count($detalleSemi2);
        $detalleCalib = array_merge($detalleSemi1, $detalleSemi2);
        $numCisternas = count($detalleCalib);
        if ($numCisternas === 0) {
            return redirect()->to(site_url('calibracion'))->with('error', 'La calibración no tiene detalle de cisternas cargado. Complete el detalle de la calibración y vuelva a intentar.');
        }

        $data = [
            'cal'                 => $cal,
            'informe'             => $informe,
            'detalle'             => $detalle,
            'detalleCalib'        => $detalleCalib,
            'numCisternas'        => $numCisternas,
            'numCisternasSemi1'   => $numCisternasSemi1,
            'numCisternasSemi2'   => $numCisternasSemi2,
            'marcasSensor'        => $marcasSensor,
            'titulo'              => 'Informe de Carga Segura - Calibración ' . $id,
        ];
        return view('calibracion/informe_carga_segura', $data);
    }

    /**
     * Guardar datos del Informe de Carga Segura (POST).
     */
    public function guardarInformeCargaSegura(int $id = 0)
    {
        if ($id <= 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID no válido']);
        }
        if (\Config\Database::connect()->tableExists('marcas_sensor')) {
            $totalMarcas = model(\App\Models\MarcasSensorModel::class)->obtenerTotal();
            if ($totalMarcas === 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Debe cargar al menos una marca de sensor (Marcas sensor) antes de guardar el informe.',
                ]);
            }
        }
        $model = model(CalibracionModel::class);
        $cal = $model->find($id);
        if (! $cal) {
            return $this->response->setJSON(['success' => false, 'message' => 'Calibración no encontrada']);
        }

        $informeModel = model(CalibracionInformeCargaSeguraModel::class);
        $detalleModel = model(CalibracionInformeCargaSeguraDetalleModel::class);

        $cal = $this->enriquecerParaVista($cal);
        $cuitTransportista = trim((string) ($cal['transportista_cuit'] ?? ''));

        $resultadoControl = trim((string) $this->request->getPost('resultado_control_vacio'));
        $resultadoTrazabilidad = trim((string) $this->request->getPost('resultado_trazabilidad'));
        $resultadoPosicion = trim((string) $this->request->getPost('resultado_posicion'));
        $responsableNombre = trim((string) $this->request->getPost('responsable_nombre'));
        $responsableCargo = trim((string) $this->request->getPost('responsable_cargo'));
        $notaPosicionSensores = trim((string) $this->request->getPost('nota_posicion_sensores'));
        $fechaEmision = $cal['fecha_calib'] ?? null;

        $cabecera = [
            'id_calibracion'          => $id,
            'resultado_control_vacio' => $resultadoControl !== '' ? $resultadoControl : null,
            'resultado_trazabilidad'  => $resultadoTrazabilidad !== '' ? $resultadoTrazabilidad : null,
            'resultado_posicion'      => $resultadoPosicion !== '' ? $resultadoPosicion : null,
            'responsable_nombre'      => $responsableNombre !== '' ? $responsableNombre : null,
            'responsable_cargo'       => $responsableCargo !== '' ? $responsableCargo : null,
            'cuit_transportista'      => $cuitTransportista !== '' ? $cuitTransportista : null,
            'fecha_emision'           => $fechaEmision ?: null,
            'nota_posicion_sensores'  => $notaPosicionSensores !== '' ? $notaPosicionSensores : null,
        ];

        $existe = $informeModel->porCalibracion($id);
        if ($existe) {
            unset($cabecera['id_calibracion']);
            $informeModel->update($id, $cabecera);
        } else {
            $informeModel->insert($cabecera);
        }

        $detalleModel->eliminarPorCalibracion($id);
        $numCisternas = (int) $this->request->getPost('num_cisternas') ?: 0;
        if ($numCisternas <= 0 || $numCisternas > 20) {
            $calConDetalle = $model->obtenerPorIdConDetalle($id);
            $detalleCalib = array_merge($calConDetalle['detalle'] ?? [], $calConDetalle['detalle_semi2'] ?? []);
            $numCisternas = count($detalleCalib) ?: 10;
        }
        for ($n = 1; $n <= $numCisternas; $n++) {
            $volumen = $this->request->getPost("cisterna_{$n}_volumen");
            $vacioReq = $this->request->getPost("cisterna_{$n}_vacio_requerido");
            $vacioMed = $this->request->getPost("cisterna_{$n}_vacio_medido");
            $accion = $this->request->getPost("cisterna_{$n}_accion_tomada");
            $volFinal = $this->request->getPost("cisterna_{$n}_volumen_final");
            $cumpleControl = $this->request->getPost("cisterna_{$n}_cumple_control");
            $marcaSensor = $this->request->getPost("cisterna_{$n}_marca_sensor");
            $serieSensor = $this->request->getPost("cisterna_{$n}_numero_serie_sensor");
            $cumpleTraz = $this->request->getPost("cisterna_{$n}_cumple_trazabilidad");
            $cumplePos = $this->request->getPost("cisterna_{$n}_cumple_posicion");
            $obsPos = $this->request->getPost("cisterna_{$n}_observacion_posicion");
            $litrosReb = $this->request->getPost("cisterna_{$n}_litros_sensor_rebalse");

            $tieneDatos = $volumen !== null || $vacioReq !== null || $vacioMed !== null || $accion !== null
                || $volFinal !== null || $cumpleControl !== null || $marcaSensor !== null || $serieSensor !== null
                || $cumpleTraz !== null || $cumplePos !== null || $obsPos !== null || $litrosReb !== null;
            if (! $tieneDatos) {
                continue;
            }

            $detalleModel->insert([
                'id_calibracion'         => $id,
                'numero_cisterna'        => $n,
                'volumen_lts'            => $volumen !== null && $volumen !== '' ? (float) $volumen : null,
                'vacio_requerido'        => $vacioReq !== null && $vacioReq !== '' ? (float) $vacioReq : null,
                'vacio_medido'           => $vacioMed !== null && $vacioMed !== '' ? (float) $vacioMed : null,
                'accion_tomada'          => $accion ? trim((string) $accion) : null,
                'volumen_final_lts'      => $volFinal !== null && $volFinal !== '' ? (float) $volFinal : null,
                'cumple_control'         => $cumpleControl ? trim((string) $cumpleControl) : null,
                'marca_sensor'           => $marcaSensor ? trim((string) $marcaSensor) : null,
                'numero_serie_sensor'    => $serieSensor ? trim((string) $serieSensor) : null,
                'cumple_trazabilidad'    => $cumpleTraz ? trim((string) $cumpleTraz) : null,
                'cumple_posicion'        => $cumplePos ? trim((string) $cumplePos) : null,
                'observacion_posicion'   => $obsPos ? trim((string) $obsPos) : null,
                'litros_sensor_rebalse'  => $litrosReb ? trim((string) $litrosReb) : null,
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Informe de Carga Segura guardado correctamente',
            'redirect' => site_url('calibracion/informe-carga-segura/' . $id),
        ]);
    }

    /**
     * Vista para imprimir el Informe de Carga Segura (formato documento formal).
     */
    public function imprimirInformeCargaSegura(int $id = 0)
    {
        helper('auth');
        if (! function_exists('es_admin') || ! es_admin()) {
            return redirect()->to(site_url('calibracion'))->with('error', 'Solo administradores pueden imprimir el informe.');
        }
        if ($id <= 0) {
            return redirect()->to(site_url('calibracion'))->with('error', 'ID no válido');
        }
        $model = model(CalibracionModel::class);
        $cal = $model->obtenerPorIdConDetalle($id);
        if (! $cal) {
            return redirect()->to(site_url('calibracion'))->with('error', 'Calibración no encontrada');
        }
        $cal = $this->enriquecerParaVista($cal);

        $calibracionDetalleModel = model(CalibracionDetalleModel::class);
        $todosDetalle = $calibracionDetalleModel->listarPorCalibracion($id, null);
        $detalleSemi1 = [];
        $detalleSemi2 = [];
        foreach ($todosDetalle as $fila) {
            $numSemi = (int) ($fila['numero_semi'] ?? 1);
            if ($numSemi === 2) {
                $detalleSemi2[] = $fila;
            } else {
                $detalleSemi1[] = $fila;
            }
        }
        $cal['detalle'] = $detalleSemi1;
        $cal['detalle_semi2'] = $detalleSemi2;

        $informeModel = model(CalibracionInformeCargaSeguraModel::class);
        $detalleModel = model(CalibracionInformeCargaSeguraDetalleModel::class);
        $informe = $informeModel->porCalibracion($id);
        $detalle = $informe ? $detalleModel->listarPorCalibracion($id) : [];

        $data = [
            'cal'      => $cal,
            'informe'  => $informe,
            'detalle'  => $detalle,
            'titulo'   => 'Informe de Carga Segura N° ' . $id,
        ];
        return view('calibracion/imprimir_informe_carga_segura', $data);
    }
}
