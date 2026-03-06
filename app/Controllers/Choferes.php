<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\ChoferesModel;
use App\Models\NacionesModel;
use App\Models\TransportistasModel;

/**
 * Choferes: documento, nombre, nacionalidad, transportista, comentarios.
 * No incluye vto_art ni vto_lnh.
 */
class Choferes extends BaseController
{
    public function index()
    {
        $naciones = model(NacionesModel::class)->orderBy('nacion', 'ASC')->findAll();
        $transportistas = model(TransportistasModel::class)->select('id_tta, transportista')->orderBy('transportista', 'ASC')->findAll();
        return view('choferes/index', [
            'titulo'         => 'Choferes',
            'naciones'       => $naciones,
            'transportistas' => $transportistas,
        ]);
    }

    public function listar()
    {
        try {
            $model = model(ChoferesModel::class);
            $lista = $model->listarTodos();
            return $this->response->setJSON(json_response(true, ['data' => $lista]));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, [
                'message' => $e->getMessage(),
                'data'    => [],
            ]));
        }
    }

    public function obtener($id = null)
    {
        if (! $id || (int) $id < 1) {
            return $this->response->setJSON(json_response(false, ['message' => 'ID no proporcionado']));
        }
        try {
            $model = model(ChoferesModel::class);
            $registro = $model->obtenerPorId((int) $id);
            if ($registro) {
                return $this->response->setJSON(json_response(true, ['data' => $registro]));
            }
            return $this->response->setJSON(json_response(false, ['message' => 'Chofer no encontrado']));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, [
                'message' => 'Error al obtener: ' . $e->getMessage(),
            ]));
        }
    }

    public function guardar()
    {
        $documento = trim((string) $this->request->getPost('documento'));
        $nombre = trim((string) $this->request->getPost('nombre'));
        if ($documento === '' || $nombre === '') {
            return $this->response->setJSON(json_response(false, [
                'message' => 'Documento y nombre son obligatorios',
                'errors'  => [
                    'documento' => $documento === '' ? 'El documento es obligatorio' : '',
                    'nombre'    => $nombre === '' ? 'El nombre es obligatorio' : '',
                ],
            ]));
        }
        try {
            $model = model(ChoferesModel::class);
            $id = (int) $this->request->getPost('id');
            $data = [
                'id'          => $id ?: null,
                'documento'   => $documento,
                'nombre'      => $nombre,
                'id_nacion'   => $this->request->getPost('id_nacion') ? (int) $this->request->getPost('id_nacion') : null,
                'id_tta'      => $this->request->getPost('id_tta') ? (int) $this->request->getPost('id_tta') : null,
                'comentarios' => $this->request->getPost('comentarios') ? trim((string) $this->request->getPost('comentarios')) : null,
            ];
            if ($id > 0) {
                $data['id'] = $id;
            }
            $idResp = $model->guardarChofer($data);
            $msg = $id > 0 ? 'Chofer actualizado correctamente.' : 'Chofer dado de alta correctamente.';
            return $this->response->setJSON(json_response(true, ['message' => $msg, 'id' => $idResp]));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, ['message' => $e->getMessage()]));
        }
    }

    public function eliminar($id = null)
    {
        if (! $id || (int) $id < 1) {
            return $this->response->setJSON(json_response(false, ['message' => 'ID no proporcionado']));
        }
        try {
            $model = model(ChoferesModel::class);
            $model->eliminarChofer((int) $id);
            return $this->response->setJSON(json_response(true, ['message' => 'Chofer eliminado correctamente.']));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, ['message' => $e->getMessage()]));
        }
    }
}
