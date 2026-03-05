<?php

namespace App\Controllers;

use App\Models\CalibradoresModel;

class Calibradores extends CrudBaseController
{
    protected string $crudModelClass    = CalibradoresModel::class;
    protected string $crudPrimaryKey   = 'id_calibrador';
    protected string $crudFieldName    = 'calibrador';
    protected string $crudEntityLabel  = 'Calibrador';
    protected string $crudGuardarMethod  = 'guardarCalibrador';
    protected string $crudEliminarMethod = 'eliminarCalibrador';
    protected string $crudEliminadoSuffix   = 'eliminado';
    protected string $crudActualizadoSuffix = 'actualizado';
    protected string $crudCreadoSuffix     = 'creado';

    public function index()
    {
        return view('calibradores/index', ['titulo' => 'Calibradores']);
    }

    /**
     * Listar calibradores para dropdown (id_calibrador, calibrador).
     */
    public function opciones()
    {
        try {
            $lista = $this->getCrudModel()->listarTodos();
            $data  = array_map(function ($r) {
                return [
                    'id_calibrador' => (int) $r['id_calibrador'],
                    'calibrador'    => $r['calibrador'] ?? '',
                ];
            }, $lista);
            return $this->response->setJSON([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'data'    => [],
                'message' => $e->getMessage(),
            ]);
        }
    }
}
