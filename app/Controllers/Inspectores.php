<?php

namespace App\Controllers;

use App\Models\InspectoresModel;

class Inspectores extends CrudBaseController
{
    protected string $crudModelClass    = InspectoresModel::class;
    protected string $crudPrimaryKey   = 'id_inspector';
    protected string $crudFieldName    = 'inspector';
    protected string $crudEntityLabel  = 'Inspector';
    protected string $crudGuardarMethod  = 'guardarInspector';
    protected string $crudEliminarMethod = 'eliminarInspector';
    protected string $crudEliminadoSuffix   = 'eliminado';
    protected string $crudActualizadoSuffix = 'actualizado';
    protected string $crudCreadoSuffix     = 'creado';

    public function index()
    {
        return view('inspectores/index', ['titulo' => 'Inspectores']);
    }

    /**
     * Listar inspectores para dropdown (id_inspector, inspector).
     */
    public function opciones()
    {
        try {
            $lista = $this->getCrudModel()->listarTodos();
            $data  = array_map(function ($r) {
                return [
                    'id_inspector' => (int) $r['id_inspector'],
                    'inspector'    => $r['inspector'] ?? '',
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
