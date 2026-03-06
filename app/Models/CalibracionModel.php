<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\BaseModel;

class CalibracionModel extends BaseModel
{
    protected $table = 'calibraciones';
    protected $primaryKey = 'id_calibracion';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $allowedFields = [
        'patente',
        'id_equipo',
        'fecha_calib',
        'vto_calib',
        'id_calibracion_reemplazo',
        'id_calibrador',
        'temp_agua',
        'valvulas',
        'observaciones',
        'tipo_unidad',
        'multi_flecha',
        'multi_flecha_semi2',
        'n_regla',
        'token_publico',
        'fecha_impresion',
        'id_usuario_impresion'
    ];

    /**
     * Fecha límite inferior: las vencidas con vto_calib anterior a esta no cuentan en KPI/dashboard.
     * Lee meses_vencida_max_kpi de parametros_sistema; si no existe, usa Config\Calibraciones (0 = sin límite).
     */
    private function getFechaLimiteInferiorVencidas(string $hoy): ?string
    {
        $meses = null;
        if ($this->db->tableExists('parametros_sistema')) {
            $paramModel = model(ParametroSistemaModel::class);
            $v = $paramModel->getValor('meses_vencida_max_kpi');
            $meses = $v !== null ? (int) $v : null;
        }
        if ($meses === null) {
            $config = config(\Config\Calibraciones::class);
            $meses = (int) ($config->mesesVenidaMaxKpi ?? 0);
        }
        if ($meses <= 0) {
            return null;
        }
        return date('Y-m-d', strtotime($hoy . ' - ' . $meses . ' months'));
    }

    /**
     * Cantidad de calibraciones vencidas (vto_calib < hoy) que no fueron recalibradas.
     * Excluye las que tienen id_calibracion_reemplazo y las que llevan más de X meses vencidas (config).
     */
    public function contarVencidas(string $hoy): int
    {
        $builder = $this->where('vto_calib <', $hoy)
            ->where('id_calibracion_reemplazo', null);
        $fechaLimite = $this->getFechaLimiteInferiorVencidas($hoy);
        if ($fechaLimite !== null) {
            $builder->where('vto_calib >=', $fechaLimite);
        }
        return $builder->countAllResults();
    }

    /**
     * Cantidad de calibraciones que vencen en los próximos 30 días (hoy <= vto <= hoy+30).
     */
    public function contarProximos30(string $hoy, string $limite30): int
    {
        return $this->where('vto_calib >=', $hoy)
            ->where('vto_calib <=', $limite30)
            ->countAllResults();
    }

    /**
     * Cantidad de calibraciones activas (vto_calib >= hoy).
     */
    public function contarActivas(string $hoy): int
    {
        return $this->where('vto_calib >=', $hoy)->countAllResults();
    }

    /**
     * Lista de vencimientos críticos para el dashboard: vencidas + próximos 30 días.
     * Retorna array de [equipo, transportista, documento, fecha_vencimiento, estado].
     * $estadoFiltro: 'vencido' = solo vencidas, 'proximo' = solo próximos 30 días, null = ambos.
     */
    public function listarVencimientosCriticos(int $limite = 50, ?string $estadoFiltro = null): array
    {
        $hoy = date('Y-m-d');
        $limite30 = date('Y-m-d', strtotime('+30 days'));
        $fechaLimite = $this->getFechaLimiteInferiorVencidas($hoy);

        $builder = $this->select('
            calibraciones.id_calibracion,
            calibraciones.patente,
            calibraciones.vto_calib,
            equipos.patente_tractor,
            transportistas.transportista
        ')
            ->join('equipos', 'equipos.patente_semi_delantero = calibraciones.patente', 'left')
            ->join('transportistas', 'transportistas.id_tta = equipos.id_tta', 'left')
            ->where('calibraciones.id_calibracion_reemplazo', null);

        if ($estadoFiltro === 'vencido') {
            $builder->where('calibraciones.vto_calib <', $hoy);
            if ($fechaLimite !== null) {
                $builder->where('calibraciones.vto_calib >=', $fechaLimite);
            }
        } elseif ($estadoFiltro === 'proximo') {
            $builder->where('calibraciones.vto_calib >=', $hoy)
                ->where('calibraciones.vto_calib <=', $limite30);
        } else {
            $builder->where('calibraciones.vto_calib <=', $limite30);
            if ($fechaLimite !== null) {
                $builder->where('calibraciones.vto_calib >=', $fechaLimite);
            }
        }

        $builder->orderBy('calibraciones.vto_calib', 'ASC')->limit($limite);

        $rows = $builder->findAll();
        $out = [];

        foreach ($rows as $r) {
            $vto = $r['vto_calib'] ?? null;
            $equipo = $r['patente'];
            if (!empty($r['patente_tractor'])) {
                $equipo = $r['patente_tractor'] . ' / ' . $r['patente'];
            }
            $transportista = $r['transportista'] ?? '-';
            $fechaVto = $vto ? date('d-m-Y', strtotime($vto)) : '-';

            if ($vto && $vto < $hoy) {
                $estado = 'VENCIDO';
            } elseif ($vto && $vto <= $limite30) {
                $estado = 'Próximo';
            } else {
                $estado = 'OK';
            }

            $out[] = [
                'equipo' => $equipo,
                'transportista' => $transportista,
                'documento' => 'Calibración',
                'fecha_vencimiento' => $fechaVto,
                'estado' => $estado
            ];
        }

        return $out;
    }

    /**
     * Cuenta calibraciones que vencen entre hoy y hoy + $dias días (misma lógica que listarPorVencer).
     * Si $incluirVencidos es true incluye ya vencidas (respeta meses_vencida_max_kpi).
     */
    public function contarPorVencer(int $dias, bool $incluirVencidos = false): int
    {
        $hoy = date('Y-m-d');
        $limiteFecha = date('Y-m-d', strtotime("+{$dias} days"));
        $fechaLimite = $incluirVencidos ? $this->getFechaLimiteInferiorVencidas($hoy) : null;

        $builder = $this->where('vto_calib <=', $limiteFecha)
            ->where('id_calibracion_reemplazo', null);
        if (! $incluirVencidos) {
            $builder->where('vto_calib >=', $hoy);
        } elseif ($fechaLimite !== null) {
            $builder->where('vto_calib >=', $fechaLimite);
        }
        return $builder->countAllResults();
    }

    /**
     * Lista calibraciones que vencen entre hoy y hoy + $dias días.
     * Si $incluirVencidos es false (por defecto): solo vto >= hoy (próximos o vence hoy).
     * Si $incluirVencidos es true: incluye ya vencidas (vto < hoy), útil para notificaciones/dashboard.
     * Retorna: id_calibracion, patente, transportista, fecha_vencimiento, dias_restantes (negativo = vencido).
     */
    public function listarPorVencer(int $dias, int $limite = 100, bool $incluirVencidos = false): array
    {
        $hoy = date('Y-m-d');
        $limiteFecha = date('Y-m-d', strtotime("+{$dias} days"));
        $fechaLimite = $incluirVencidos ? $this->getFechaLimiteInferiorVencidas($hoy) : null;

        $builder = $this->select('
            calibraciones.id_calibracion,
            calibraciones.patente,
            calibraciones.vto_calib,
            equipos.patente_tractor,
            transportistas.transportista
        ')
            ->join('equipos', 'equipos.patente_semi_delantero = calibraciones.patente', 'left')
            ->join('transportistas', 'transportistas.id_tta = equipos.id_tta', 'left')
            ->where('calibraciones.vto_calib <=', $limiteFecha)
            ->where('calibraciones.id_calibracion_reemplazo', null);
        if (! $incluirVencidos) {
            $builder->where('calibraciones.vto_calib >=', $hoy);
        } elseif ($fechaLimite !== null) {
            $builder->where('calibraciones.vto_calib >=', $fechaLimite);
        }
        $builder->orderBy('calibraciones.vto_calib', 'ASC')->limit($limite);

        $rows = $builder->findAll();
        $out = [];

        foreach ($rows as $r) {
            $vto = $r['vto_calib'] ?? null;
            if (! $vto) {
                continue;
            }
            $equipo = $r['patente'];
            if (! empty($r['patente_tractor'])) {
                $equipo = $r['patente_tractor'] . ' / ' . $r['patente'];
            }
            $transportista = $r['transportista'] ?? '-';
            $tsVto = strtotime($vto);
            $diasRestantes = (int) floor(($tsVto - strtotime($hoy)) / 86400);

            $out[] = [
                'id_calibracion' => (int) $r['id_calibracion'],
                'patente' => $r['patente'],
                'equipo' => $equipo,
                'transportista' => $transportista,
                'fecha_vencimiento' => date('d-m-Y', $tsVto),
                'dias_restantes' => $diasRestantes,
            ];
        }

        return $out;
    }

    /**
     * Lista calibraciones ya vencidas (vto_calib < hoy).
     * Respeta meses_vencida_max_kpi si está configurado (no lista vencidas de hace más de X meses).
     * Retorna misma estructura que listarPorVencer; dias_restantes será negativo.
     */
    public function listarVencidos(int $limite = 500): array
    {
        $hoy = date('Y-m-d');
        $fechaLimite = $this->getFechaLimiteInferiorVencidas($hoy);

        $builder = $this->select('
            calibraciones.id_calibracion,
            calibraciones.patente,
            calibraciones.vto_calib,
            equipos.patente_tractor,
            transportistas.transportista
        ')
            ->join('equipos', 'equipos.patente_semi_delantero = calibraciones.patente', 'left')
            ->join('transportistas', 'transportistas.id_tta = equipos.id_tta', 'left')
            ->where('calibraciones.vto_calib <', $hoy)
            ->where('calibraciones.id_calibracion_reemplazo', null);
        if ($fechaLimite !== null) {
            $builder->where('calibraciones.vto_calib >=', $fechaLimite);
        }
        $builder->orderBy('calibraciones.vto_calib', 'DESC')->limit($limite);

        $rows = $builder->findAll();
        $out = [];

        foreach ($rows as $r) {
            $vto = $r['vto_calib'] ?? null;
            if (! $vto) {
                continue;
            }
            $equipo = $r['patente'];
            if (! empty($r['patente_tractor'])) {
                $equipo = $r['patente_tractor'] . ' / ' . $r['patente'];
            }
            $transportista = $r['transportista'] ?? '-';
            $tsVto = strtotime($vto);
            $diasRestantes = (int) floor(($tsVto - strtotime($hoy)) / 86400);

            $out[] = [
                'id_calibracion' => (int) $r['id_calibracion'],
                'patente' => $r['patente'],
                'equipo' => $equipo,
                'transportista' => $transportista,
                'fecha_vencimiento' => date('d-m-Y', $tsVto),
                'dias_restantes' => $diasRestantes,
            ];
        }

        return $out;
    }

    /**
     * Listar calibraciones para el reporte (filtros: período, patente, calibrador, precinto).
     * Retorna array con: numero, patente, equipo, transportista, fecha_calib, vto_calib, calibrador, estado (Vigente/Vencido).
     * Período se aplica sobre fecha_calib (fecha de calibración).
     * precinto: filtra por cualquier campo precinto (campana, soporte, hombre) en detalle o multiflecha.
     */
    public function listarParaReporte(?string $fechaDesde = null, ?string $fechaHasta = null, ?string $patente = null, ?int $idCalibrador = null, ?string $precinto = null): array
    {
        $builder = $this->select('
            calibraciones.id_calibracion,
            calibraciones.patente,
            calibraciones.fecha_calib,
            calibraciones.vto_calib,
            equipos.patente_tractor,
            transportistas.transportista,
            calibradores.calibrador
        ')
            ->join('equipos', 'equipos.patente_semi_delantero = calibraciones.patente', 'left')
            ->join('transportistas', 'transportistas.id_tta = equipos.id_tta', 'left')
            ->join('calibradores', 'calibradores.id_calibrador = calibraciones.id_calibrador', 'left')
            ->orderBy('calibraciones.id_calibracion', 'DESC');

        if ($fechaDesde !== null && $fechaDesde !== '') {
            $builder->where('calibraciones.fecha_calib >=', $fechaDesde);
        }
        if ($fechaHasta !== null && $fechaHasta !== '') {
            $builder->where('calibraciones.fecha_calib <=', $fechaHasta);
        }
        if ($patente !== null && $patente !== '') {
            $builder->like('calibraciones.patente', $patente, 'both');
        }
        if ($idCalibrador !== null && $idCalibrador > 0) {
            $builder->where('calibraciones.id_calibrador', $idCalibrador);
        }
        if ($precinto !== null && $precinto !== '') {
            $term = '%' . $precinto . '%';
            $esc = $this->db->escape($term);
            $tblDet = $this->db->prefixTable('calibracion_detalle');
            $tblMf  = $this->db->prefixTable('calibracion_multiflecha');
            $tblCal = $this->db->prefixTable('calibraciones');
            $builder->where("(EXISTS (SELECT 1 FROM {$tblDet} d WHERE d.id_calibracion = {$tblCal}.id_calibracion AND (d.precinto_campana LIKE {$esc} OR d.precinto_soporte LIKE {$esc} OR d.precinto_hombre LIKE {$esc})) OR EXISTS (SELECT 1 FROM {$tblMf} m WHERE m.id_calibracion = {$tblCal}.id_calibracion AND (m.precinto_campana LIKE {$esc} OR m.precinto_soporte LIKE {$esc} OR m.precinto_hombre LIKE {$esc})))", null, false);
        }

        $rows = $builder->findAll();
        $hoy = date('Y-m-d');
        $out = [];

        foreach ($rows as $r) {
            $equipo = $r['patente'] ?? '';
            if (! empty($r['patente_tractor'])) {
                $equipo = $r['patente_tractor'] . ' / ' . ($r['patente'] ?? '');
            }
            $vto = $r['vto_calib'] ?? null;
            $estado = 'Vigente';
            if ($vto && $vto < $hoy) {
                $estado = 'Vencido';
            }
            $out[] = [
                'numero'         => (int) ($r['id_calibracion'] ?? 0),
                'patente'        => $r['patente'] ?? '',
                'equipo'         => $equipo,
                'transportista'  => $r['transportista'] ?? '-',
                'fecha_calib'    => $r['fecha_calib'] ? date('d/m/Y', strtotime($r['fecha_calib'])) : '-',
                'vto_calib'      => $vto ? date('d/m/Y', strtotime($vto)) : '-',
                'calibrador'     => $r['calibrador'] ?? '-',
                'estado'         => $estado,
            ];
        }

        return $out;
    }

    /**
     * Listar calibraciones para DataTable (con filtros opcionales numero y patente).
     * Retorna array con: id_calibracion, numero (id_calibracion), patente, fecha_calib (formato d/m/Y), calibrador.
     */
    public function listarParaDataTable(?string $numero = null, ?string $patente = null): array
    {
        $builder = $this->select('
            calibraciones.id_calibracion,
            calibraciones.patente,
            calibraciones.fecha_calib,
            calibradores.calibrador
        ')
            ->join('calibradores', 'calibradores.id_calibrador = calibraciones.id_calibrador', 'left')
            ->orderBy('calibraciones.id_calibracion', 'DESC');

        if ($numero !== null && $numero !== '') {
            $builder->where('calibraciones.id_calibracion', (int) $numero);
        }
        if ($patente !== null && $patente !== '') {
            $builder->like('calibraciones.patente', $patente, 'both');
        }

        $rows = $builder->findAll();
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id_calibracion' => (int) $r['id_calibracion'],
                'numero' => (int) $r['id_calibracion'],
                'patente' => $r['patente'] ?? '',
                'fecha_calib' => $r['fecha_calib'] ? date('d/m/Y', strtotime($r['fecha_calib'])) : '-',
                'calibrador' => $r['calibrador'] ?? '-'
            ];
        }
        return $out;
    }

    /**
     * Lista calibraciones por patente (para historial en vista de unidad/equipo).
     * Retorna: id_calibracion, fecha, tipo, inspector_taller, observaciones, vencimiento, token_publico (para link a detalle).
     */
    public function listarPorPatente(string $patente): array
    {
        if ($patente === '') {
            return [];
        }
        $rows = $this->select('
            calibraciones.id_calibracion,
            calibraciones.fecha_calib,
            calibraciones.vto_calib,
            calibraciones.observaciones,
            calibraciones.token_publico,
            calibradores.calibrador
        ')
            ->join('calibradores', 'calibradores.id_calibrador = calibraciones.id_calibrador', 'left')
            ->where('calibraciones.patente', $patente)
            ->orderBy('calibraciones.fecha_calib', 'DESC')
            ->findAll();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id_calibracion'   => (int) ($r['id_calibracion'] ?? 0),
                'fecha'            => !empty($r['fecha_calib']) ? date('d-M-Y', strtotime($r['fecha_calib'])) : '-',
                'tipo'             => 'Calibración',
                'inspector_taller' => $r['calibrador'] ?? '-',
                'observaciones'    => $r['observaciones'] ?? '-',
                'vencimiento'      => !empty($r['vto_calib']) ? date('d-M-Y', strtotime($r['vto_calib'])) : 'N/A',
                'token_publico'    => $r['token_publico'] ?? '',
            ];
        }
        return $out;
    }

    /**
     * Genera un token único para acceso público (solo lectura).
     */
    public static function generarToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    /**
     * Obtiene una calibración por token público con su detalle (para vista pública).
     */
    public function getByToken(string $token): ?array
    {
        $cab = $this->where('token_publico', $token)->first();
        if (! $cab) {
            return null;
        }
        $id = (int) $cab['id_calibracion'];
        $detalleModel = model(CalibracionDetalleModel::class);
        $cab['detalle'] = $detalleModel->listarPorCalibracion($id, 1);
        $cab['detalle_semi2'] = $detalleModel->listarPorCalibracion($id, 2);
        return $cab;
    }

    /**
     * Obtener la última calibración por patente con su detalle (para autocompletar formulario).
     * Orden por id_calibracion DESC. Retorna null si no hay ninguna.
     */
    public function obtenerUltimaPorPatente(string $patente): ?array
    {
        $patente = trim($patente);
        if ($patente === '') {
            return null;
        }
        $cab = $this->where('patente', $patente)
            ->orderBy('id_calibracion', 'DESC')
            ->first();
        if (! $cab) {
            return null;
        }
        $id = (int) $cab['id_calibracion'];
        $detalleModel = model(CalibracionDetalleModel::class);
        $cab['detalle'] = $detalleModel->listarPorCalibracion($id, 1);
        $cab['detalle_semi2'] = $detalleModel->listarPorCalibracion($id, 2);
        $cab['fecha_calib'] = $cab['fecha_calib'] ?? null;
        $cab['vto_calib'] = $cab['vto_calib'] ?? null;
        return $cab;
    }

    /**
     * Obtener una calibración por ID con su detalle (líneas).
     */
    /**
     * Marca la calibración como impresa por primera vez (fecha e usuario).
     * Solo actualiza si fecha_impresion es null.
     */
    public function marcarComoImpreso(int $idCalibracion, int $idUsuario): bool
    {
        $cab = $this->find($idCalibracion);
        if (! $cab || ! empty($cab['fecha_impresion'])) {
            return false;
        }
        $this->update($idCalibracion, [
            'fecha_impresion'      => date('Y-m-d H:i:s'),
            'id_usuario_impresion' => $idUsuario
        ]);
        return true;
    }

    public function obtenerPorIdConDetalle(int $id): ?array
    {
        $cab = $this->find($id);
        if (! $cab) {
            return null;
        }
        $detalleModel = model(CalibracionDetalleModel::class);
        $cab['detalle'] = $detalleModel->listarPorCalibracion($id, 1);
        $cab['detalle_semi2'] = $detalleModel->listarPorCalibracion($id, 2);
        $cab['fecha_calib'] = $cab['fecha_calib'] ?? null;
        $cab['vto_calib'] = $cab['vto_calib'] ?? null;
        return $cab;
    }

    /**
     * Guardar cabecera y detalle. Si id_calibracion viene, actualiza; si no, inserta.
     * $cabecera: array con campos de calibraciones (incl. multi_flecha_semi2 si aplica).
     * $detalle: array de líneas del 1er semi.
     * $detalle_semi2: array de líneas del 2do semi (opcional).
     */
    public function guardarCalibracion(array $cabecera, array $detalle, array $detalle_semi2 = []): int
    {
        $id = isset($cabecera['id_calibracion']) ? (int) $cabecera['id_calibracion'] : 0;
        unset($cabecera['id_calibracion']);

        $this->db->transStart();

        if ($id > 0) {
            unset($cabecera['token_publico']);
            $this->update($id, $cabecera);
            $detalleModel = model(CalibracionDetalleModel::class);
            $detalleModel->eliminarPorCalibracion($id);
            $idCalib = $id;
        } else {
            if (empty($cabecera['token_publico'])) {
                $cabecera['token_publico'] = self::generarToken();
            }
            $this->insert($cabecera);
            $idCalib = (int) $this->getInsertID();
        }

        $detalleModel = model(CalibracionDetalleModel::class);
        $numeroLinea = 1;
        foreach ($detalle as $linea) {
            $detalleModel->insert([
                'id_calibracion' => $idCalib,
                'numero_semi' => 1,
                'numero_linea' => $numeroLinea,
                'mflec' => $linea['mflec'] ?? null,
                'capacidad' => $linea['capacidad'] ?? 0,
                'enrase' => $linea['enrase'] ?? 0,
                'referen' => $linea['referen'] ?? 0,
                'vacio_calc' => $linea['vacio_calc'] ?? null,
                'vacio_lts' => $linea['vacio_lts'] ?? null,
                'precinto_campana' => $linea['precinto_campana'] ?? null,
                'precinto_soporte' => $linea['precinto_soporte'] ?? null,
                'precinto_hombre' => $linea['precinto_hombre'] ?? null,
                'precinto_ultima' => $linea['precinto_ultima'] ?? null
            ]);
            $numeroLinea++;
        }
        $numeroLinea = 1;
        foreach ($detalle_semi2 as $linea) {
            $detalleModel->insert([
                'id_calibracion' => $idCalib,
                'numero_semi' => 2,
                'numero_linea' => $numeroLinea,
                'mflec' => $linea['mflec'] ?? null,
                'capacidad' => $linea['capacidad'] ?? 0,
                'enrase' => $linea['enrase'] ?? 0,
                'referen' => $linea['referen'] ?? 0,
                'vacio_calc' => $linea['vacio_calc'] ?? null,
                'vacio_lts' => $linea['vacio_lts'] ?? null,
                'precinto_campana' => $linea['precinto_campana'] ?? null,
                'precinto_soporte' => $linea['precinto_soporte'] ?? null,
                'precinto_hombre' => $linea['precinto_hombre'] ?? null,
                'precinto_ultima' => $linea['precinto_ultima'] ?? null
            ]);
            $numeroLinea++;
        }

        $this->db->transComplete();

        if (! $this->db->transStatus()) {
            throw new \RuntimeException('Error al guardar la calibración: transacción fallida.');
        }

        return $idCalib;
    }

    /**
     * Marca como recalibradas las calibraciones vencidas anteriores de la misma patente,
     * enlazándolas a la nueva calibración (id_calibracion_reemplazo).
     * Debe llamarse después de guardar una nueva calibración.
     */
    public function marcarAnterioresComoRecalibradas(string $patente, int $idCalibracionNueva, string $hoy): int
    {
        $anteriores = $this->where('patente', $patente)
            ->where('vto_calib <', $hoy)
            ->where('id_calibracion !=', $idCalibracionNueva)
            ->where('id_calibracion_reemplazo', null)
            ->findAll();
        $count = 0;
        foreach ($anteriores as $row) {
            $this->update((int) $row['id_calibracion'], ['id_calibracion_reemplazo' => $idCalibracionNueva]);
            $count++;
        }
        return $count;
    }

    /**
     * Eliminar calibración y su detalle (CASCADE ya borra detalle si FK está bien).
     */
    public function eliminarCalibracion(int $id): bool
    {
        $this->db->transStart();
        $detalleModel = model(CalibracionDetalleModel::class);
        $detalleModel->eliminarPorCalibracion($id);
        $this->delete($id);
        $this->db->transComplete();
        return $this->db->transStatus();
    }
}
