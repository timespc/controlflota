<?php

namespace App\Controllers;

use App\Models\TiposCargamentosModel;

/**
 * Tipos Cargamentos: solo consulta e impresión (sin alta/edición/eliminación).
 */
class TiposCargamentos extends BaseController
{
    public function index()
    {
        return view('tipos_cargamentos/index', [
            'titulo' => 'Tipos Cargamentos',
        ]);
    }

    /**
     * Listar para DataTable (JSON).
     */
    public function listar()
    {
        try {
            $model = model(TiposCargamentosModel::class);
            $lista = $model->listarTodos();
            return $this->response->setJSON(json_response(true, ['data' => $lista]));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, [
                'message' => $e->getMessage(),
                'data'    => [],
            ]));
        }
    }

    /**
     * Vista para imprimir la tabla.
     */
    public function imprimir()
    {
        $model = model(TiposCargamentosModel::class);
        $lista = $model->listarTodos();
        return view('tipos_cargamentos/imprimir', [
            'titulo' => 'Tipos Cargamentos',
            'lista'  => $lista,
        ]);
    }
}
