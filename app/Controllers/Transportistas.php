<?php

namespace App\Controllers;

use App\Models\TransportistasModel;
use App\Models\PaisModel;
use App\Models\PaisProvinciaModel;

class Transportistas extends CrudBaseController
{
    protected string $crudModelClass    = TransportistasModel::class;
    protected string $crudPrimaryKey   = 'id_tta';
    protected string $crudFieldName    = 'transportista';
    protected string $crudEntityLabel  = 'Transportista';
    protected string $crudGuardarMethod = 'guardarTransportista';
    protected string $crudEliminarMethod = 'eliminarTransportista';
    protected string $crudTotalMethod  = 'obtenerTotal';
    protected string $crudEliminadoSuffix   = 'eliminado';
    protected string $crudActualizadoSuffix = 'actualizado';
    protected string $crudCreadoSuffix     = 'creado';

    protected $paisModel;
    protected $paisProvinciaModel;

    public function __construct()
    {
        $this->paisModel = new PaisModel();
        $this->paisProvinciaModel = new PaisProvinciaModel();
    }

    public function index()
    {
        try {
            $paises = $this->paisModel->obtenerTodos();
        } catch (\Exception $e) {
            $paises = [];
        }
        $data = [
            'titulo' => 'Transportistas',
            'paises' => $paises,
        ];
        return view('transportistas/index', $data);
    }

    /**
     * Vista de detalle del transportista (datos + documentación).
     */
    public function ver($id = null)
    {
        $id = (int) $id;
        if ($id <= 0) {
            return redirect()->to(site_url('transportistas'))->with('error', 'ID inválido.');
        }
        $model = $this->getCrudModel();
        $transportista = $model->obtenerPorId($id);
        if (! $transportista) {
            return redirect()->to(site_url('transportistas'))->with('error', 'Transportista no encontrado.');
        }
        return view('transportistas/ver', [
            'titulo' => 'Transportista',
            'transportista' => $transportista,
        ]);
    }

    /**
     * Obtener provincias por país (AJAX).
     */
    public function provinciasPorPais($pais_id = null)
    {
        if (! $pais_id) {
            return $this->response->setJSON(json_response(false, ['message' => 'ID de país no proporcionado', 'data' => []]));
        }
        try {
            $provincias = $this->paisProvinciaModel->provinciasPorPaisId($pais_id);
            return $this->response->setJSON(json_response(true, ['data' => $provincias]));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, ['message' => 'Error: ' . $e->getMessage(), 'data' => []]));
        }
    }

    /**
     * Guardar o actualizar un transportista (lógica específica: muchos campos, país/provincia).
     */
    public function guardar()
    {
        $validation = \Config\Services::validation();
        $rules = [
            'transportista' => 'required|min_length[3]|max_length[255]',
            'mail_contacto' => 'permit_empty|valid_email_multiple',
        ];

        if (! $this->validate($rules)) {
            return $this->response->setJSON(json_response(false, [
                'message' => 'Error de validación',
                'errors'  => $validation->getErrors(),
            ]));
        }

        try {
            $id = $this->request->getPost('id_tta');
            $pais_id = $this->request->getPost('pais_id');
            $provincia_id = $this->request->getPost('provincia_id');

            $pais_nombre = '';
            $provincia_nombre = '';
            if ($pais_id) {
                $pais = $this->paisModel->find($pais_id);
                $pais_nombre = $pais ? $pais['nombre'] : '';
            }
            if ($provincia_id) {
                $provincia = $this->paisProvinciaModel->find($provincia_id);
                $provincia_nombre = $provincia ? $provincia['nombre'] : '';
            }

            $data = [
                'transportista' => $this->request->getPost('transportista'),
                'cuit' => $this->request->getPost('cuit'),
                'direccion' => $this->request->getPost('direccion'),
                'localidad' => $this->request->getPost('localidad'),
                'codigo_postal' => $this->request->getPost('codigo_postal'),
                'provincia' => $provincia_nombre,
                'nacion' => $pais_nombre,
                'pais_id' => $pais_id ?: null,
                'provincia_id' => $provincia_id ?: null,
                'mail_contacto' => $this->request->getPost('mail_contacto'),
                'telefono' => $this->request->getPost('telefono'),
                'comentarios' => $this->request->getPost('comentarios'),
            ];
            if ($id && $id !== '') {
                $data['id_tta'] = $id;
            }

            $model = $this->getCrudModel();
            $result = $model->guardarTransportista($data);

            if ($result) {
                return $this->response->setJSON(json_response(true, [
                    'message' => $id ? 'Transportista actualizado correctamente' : 'Transportista creado correctamente',
                    'id'      => $id ?: $model->getInsertID(),
                ]));
            }
            return $this->response->setJSON(json_response(false, ['message' => 'Error al guardar el transportista']));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, ['message' => 'Error: ' . $e->getMessage()]));
        }
    }
}
