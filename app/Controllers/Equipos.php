<?php

namespace App\Controllers;

use App\Models\EquiposModel;
use App\Models\TransportistasModel;
use App\Models\PaisModel;
use App\Models\BanderasModel;
use App\Models\MarcasModel;
use App\Models\CubiertasModel;
use App\Models\CalibracionModel;

class Equipos extends BaseController
{
    protected $equiposModel;
    protected $transportistasModel;
    protected $paisModel;
    protected $banderasModel;
    protected $marcasModel;
    protected $cubiertasModel;
    protected $calibracionModel;

    public function __construct()
    {
        $this->equiposModel       = model(EquiposModel::class);
        $this->transportistasModel = model(TransportistasModel::class);
        $this->paisModel           = model(PaisModel::class);
        $this->banderasModel       = model(BanderasModel::class);
        $this->marcasModel         = model(MarcasModel::class);
        $this->cubiertasModel      = model(CubiertasModel::class);
        $this->calibracionModel    = model(CalibracionModel::class);
    }

    /**
     * Vista de detalle del equipo con tabs: Datos del equipo, Cisternas, Historial inspección.
     */
    public function ver($id)
    {
        $id = (int) $id;
        $equipo = $this->equiposModel->getWithRelations($id);
        if (!$equipo) {
            return redirect()->to(site_url('equipos'))->with('error', 'Equipo no encontrado.');
        }
        $historial = $this->calibracionModel->listarPorPatente($equipo['patente_semi_delantero'] ?? '');
        $data = [
            'titulo'   => 'Datos del equipo',
            'equipo'   => $equipo,
            'historial' => $historial,
        ];
        return view('equipos/ver', $data);
    }

    public function index()
    {
        try {
            $transportistas = $this->transportistasModel->listarTodos();
        } catch (\Exception $e) {
            $transportistas = [];
        }
        try {
            $paises = $this->paisModel->obtenerTodos();
        } catch (\Exception $e) {
            $paises = [];
        }
        try {
            $banderas = $this->banderasModel->listarTodos();
        } catch (\Exception $e) {
            $banderas = [];
        }
        try {
            $marcas = $this->marcasModel->listarTodos();
        } catch (\Exception $e) {
            $marcas = [];
        }
        try {
            $cubiertas = $this->cubiertasModel->listarTodos();
        } catch (\Exception $e) {
            $cubiertas = [];
        }
        $vistaConfig = config('EquiposVista');

        try {
            $transportistasConEquipos = $this->transportistasModel->listarSoloConEquipos();
        } catch (\Exception $e) {
            $transportistasConEquipos = [];
        }
        $data = [
            'titulo'                     => 'Equipos',
            'transportistas'             => $transportistas,
            'transportistas_con_equipos'  => $transportistasConEquipos,
            'paises'         => $paises,
            'banderas'       => $banderas,
            'marcas'         => $marcas,
            'cubiertas'      => $cubiertas,
            'vista'          => $vistaConfig,
        ];
        return view('equipos/index', $data);
    }

    /**
     * Listar equipos para DataTable (acepta filtros: id_tta, patente_tractor, patente_semi_delantero, patente_semi_trasero).
     */
    public function listar()
    {
        try {
            $id_tta = $this->request->getPost('id_tta');
            $id_tta = ($id_tta !== null && $id_tta !== '') ? (int) $id_tta : null;
            $patente_tractor = (string) $this->request->getPost('patente_tractor');
            $patente_semi_delantero = (string) $this->request->getPost('patente_semi_delantero');
            $patente_semi_trasero = (string) $this->request->getPost('patente_semi_trasero');
            $tieneFiltros = ($id_tta !== null && $id_tta > 0) || trim($patente_tractor) !== '' || trim($patente_semi_delantero) !== '' || trim($patente_semi_trasero) !== '';
            $equipos = $tieneFiltros
                ? $this->equiposModel->listarConFiltros($id_tta, $patente_tractor, $patente_semi_delantero, $patente_semi_trasero)
                : $this->equiposModel->getAllWithRelations();

            $data = [];
            foreach ($equipos as $equipo) {
                $data[] = [
                    'id_equipo' => $equipo['id_equipo'],
                    'patente_semi_delantero' => $equipo['patente_semi_delantero'],
                    'transportista' => $equipo['transportista'] ?? '-',
                    'bitren' => $equipo['bitren'] ?? '-',
                    'patente_tractor' => $equipo['patente_tractor'] ?? '-',
                    'tractor_anio_modelo' => $equipo['tractor_anio_modelo'] ?? '-',
                    'patente_semi_trasero' => $equipo['patente_semi_trasero'] ?? '-',
                    'bandera' => $equipo['bandera_nombre'] ?? '-',
                    'marca' => $equipo['marca_nombre'] ?? '-',
                    'semi_delantero_anio_modelo' => $equipo['semi_delantero_anio_modelo'] ?? '-',
                    'semi_delantero_tara' => $equipo['semi_delantero_tara'] ?? '-',
                    'tara_total' => $equipo['tara_total'] ?? '-',
                    'capacidad_total' => $equipo['capacidad_total'] ?? '-',
                    'ejes_semi_delantero' => $equipo['ejes_semi_delantero'] ?? '-',
                    'created_at' => $equipo['created_at'] ?? null,
                    'updated_at' => $equipo['updated_at'] ?? null,
                ];
            }

            return $this->response->setJSON(json_response(true, ['data' => $data]));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, ['message' => $e->getMessage(), 'data' => []]));
        }
    }

    /**
     * Obtener un equipo por ID
     */
    public function obtener($id = null)
    {
        if (! $id) {
            return $this->response->setJSON(json_response(false, ['message' => 'ID no proporcionado']));
        }
        try {
            $equipo = $this->equiposModel->getWithRelations($id);
            if ($equipo) {
                return $this->response->setJSON(json_response(true, ['data' => $equipo]));
            }
            return $this->response->setJSON(json_response(false, ['message' => 'Equipo no encontrado']));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, ['message' => 'Error: ' . $e->getMessage()]));
        }
    }

    /**
     * Guardar equipo (crear o actualizar)
     */
    public function guardar()
    {
        $input = $this->request->getJSON(true);

        $pais_id = !empty($input['pais_id']) ? (int)$input['pais_id'] : null;
        $nacion = '';
        if ($pais_id) {
            $pais = $this->paisModel->find($pais_id);
            $nacion = $pais ? ($pais['nombre'] ?? '') : '';
        }

        $data = [
            'patente_semi_delantero' => $input['patente_semi_delantero'] ?? null,
            'id_tta' => !empty($input['id_tta']) ? (int)$input['id_tta'] : null,
            'patente_tractor' => $input['patente_tractor'] ?? null,
            'bitren' => $input['bitren'] ?? 'NO',
            'patente_semi_trasero' => $input['patente_semi_trasero'] ?? null,
            'fecha_alta' => $input['fecha_alta'] ?? null,
            'modo_carga' => $input['modo_carga'] ?? null,
            'pais_id' => $pais_id,
            'nacion' => $nacion !== '' ? $nacion : null,
            'tractor_tara' => isset($input['tractor_tara']) && $input['tractor_tara'] !== '' ? (float)$input['tractor_tara'] : null,
            'tractor_pbt' => isset($input['tractor_pbt']) && $input['tractor_pbt'] !== '' ? (float)$input['tractor_pbt'] : null,
            'tractor_anio_modelo' => !empty($input['tractor_anio_modelo']) ? (int)$input['tractor_anio_modelo'] : null,
            'semi_delantero_tara' => isset($input['semi_delantero_tara']) && $input['semi_delantero_tara'] !== '' ? (float)$input['semi_delantero_tara'] : null,
            'semi_delantera_pbt' => isset($input['semi_delantera_pbt']) && $input['semi_delantera_pbt'] !== '' ? (float)$input['semi_delantera_pbt'] : null,
            'semi_trasero_anio_modelo' => !empty($input['semi_trasero_anio_modelo']) ? (int)$input['semi_trasero_anio_modelo'] : null,
            'semi_trasero_tara' => isset($input['semi_trasero_tara']) && $input['semi_trasero_tara'] !== '' ? (float)$input['semi_trasero_tara'] : null,
            'semi_trasero_pbt' => isset($input['semi_trasero_pbt']) && $input['semi_trasero_pbt'] !== '' ? (float)$input['semi_trasero_pbt'] : null,
            'tara_total' => isset($input['tara_total']) && $input['tara_total'] !== '' ? (float)$input['tara_total'] : null,
            'peso_maximo' => isset($input['peso_maximo']) && $input['peso_maximo'] !== '' ? (float)$input['peso_maximo'] : null,
            'id_bandera' => !empty($input['id_bandera']) ? (int)$input['id_bandera'] : null,
            'id_marca' => !empty($input['id_marca']) ? (int)$input['id_marca'] : null,
            'semi_delantero_anio_modelo' => !empty($input['semi_delantero_anio_modelo']) ? (int)$input['semi_delantero_anio_modelo'] : null,
            'cubierta_tractor_eje1' => !empty($input['cubierta_tractor_eje1']) ? (int)$input['cubierta_tractor_eje1'] : null,
            'cubierta_tractor_eje2' => !empty($input['cubierta_tractor_eje2']) ? (int)$input['cubierta_tractor_eje2'] : null,
            'cubierta_tractor_eje3' => !empty($input['cubierta_tractor_eje3']) ? (int)$input['cubierta_tractor_eje3'] : null,
            'ejes_tractor' => !empty($input['ejes_tractor']) ? (int)$input['ejes_tractor'] : null,
            'cubierta_semi_delantero_eje1' => !empty($input['cubierta_semi_delantero_eje1']) ? (int)$input['cubierta_semi_delantero_eje1'] : null,
            'cubierta_semi_delantero_eje2' => !empty($input['cubierta_semi_delantero_eje2']) ? (int)$input['cubierta_semi_delantero_eje2'] : null,
            'cubierta_semi_delantero_eje3' => !empty($input['cubierta_semi_delantero_eje3']) ? (int)$input['cubierta_semi_delantero_eje3'] : null,
            'ejes_semi_delantero' => !empty($input['ejes_semi_delantero']) ? (int)$input['ejes_semi_delantero'] : null,
            'ejes_semi_trasero' => !empty($input['ejes_semi_trasero']) ? (int)$input['ejes_semi_trasero'] : null,
            'cubierta_semi_trasero_eje1' => !empty($input['cubierta_semi_trasero_eje1']) ? (int)$input['cubierta_semi_trasero_eje1'] : null,
            'cubierta_semi_trasero_eje2' => !empty($input['cubierta_semi_trasero_eje2']) ? (int)$input['cubierta_semi_trasero_eje2'] : null,
            'cubierta_semi_trasero_eje3' => !empty($input['cubierta_semi_trasero_eje3']) ? (int)$input['cubierta_semi_trasero_eje3'] : null,
            'cota_delantero' => isset($input['cota_delantero']) && $input['cota_delantero'] !== '' ? (float)$input['cota_delantero'] : null,
            'cota_trasero' => isset($input['cota_trasero']) && $input['cota_trasero'] !== '' ? (float)$input['cota_trasero'] : null,
            'observaciones' => $input['observaciones'] ?? null,
        ];
        for ($i = 1; $i <= 10; $i++) {
            $key = 'cisterna_' . $i . '_capacidad';
            $data[$key] = isset($input[$key]) && $input[$key] !== '' ? (float)$input[$key] : null;
        }
        $data['capacidad_total'] = isset($input['capacidad_total']) && $input['capacidad_total'] !== '' ? (float)$input['capacidad_total'] : null;

        $checklistCampos = [
            'checklist_asfalto', 'checklist_alcohol', 'checklist_biodiesel',
            'checklist_comb_liv', 'checklist_comb_pes', 'checklist_solvente',
            'checklist_coke', 'checklist_lubes_gra', 'checklist_lubes_env', 'checklist_glp',
        ];
        foreach ($checklistCampos as $campo) {
            $data[$campo] = ! empty($input[$campo]) ? 1 : 0;
        }

        if (! $this->equiposModel->validate($data)) {
            return $this->response->setJSON(json_response(false, [
                'message' => 'Error de validación',
                'errors'  => $this->equiposModel->errors(),
            ]));
        }

        try {
            if (!empty($input['id_equipo'])) {
                $this->equiposModel->update($input['id_equipo'], $data);
                $message = 'Equipo actualizado correctamente';
            } else {
                $this->equiposModel->insert($data);
                $message = 'Equipo creado correctamente';
            }

            return $this->response->setJSON(json_response(true, ['message' => $message]));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, ['message' => 'Error: ' . $e->getMessage()]));
        }
    }

    /**
     * Eliminar equipo
     */
    public function eliminar($id = null)
    {
        if (! $id) {
            return $this->response->setJSON(json_response(false, ['message' => 'ID no proporcionado']));
        }
        try {
            $this->equiposModel->delete($id);
            return $this->response->setJSON(json_response(true, ['message' => 'Equipo eliminado correctamente']));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, ['message' => 'Error: ' . $e->getMessage()]));
        }
    }

    /**
     * Obtener equipo por patente (para saber si tiene 2do semi, etc.).
     */
    public function infoPatente(string $patente = '')
    {
        $patente = trim($patente);
        if ($patente === '') {
            return $this->response->setJSON(json_response(false, ['data' => null, 'message' => 'Patente no proporcionada']));
        }
        try {
            $equipo = $this->equiposModel->where('patente_semi_delantero', $patente)->first();
            return $this->response->setJSON(json_response(true, ['data' => $equipo]));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, ['data' => null, 'message' => $e->getMessage()]));
        }
    }

    /**
     * Listar patentes para selector/autocompletado (ej. en calibración).
     */
    public function patentes()
    {
        $q = trim((string) $this->request->getGet('q'));
        try {
            $lista = $this->equiposModel->listarPatentes($q);
            return $this->response->setJSON(json_response(true, ['data' => $lista]));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, ['data' => [], 'message' => $e->getMessage()]));
        }
    }

    /**
     * Obtener total de equipos
     */
    public function total()
    {
        try {
            $total = $this->equiposModel->countAll();
            return $this->response->setJSON(json_response(true, ['total' => $total]));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, ['total' => 0, 'message' => $e->getMessage()]));
        }
    }
}
