<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\ReglasModel;

class Reglas extends CrudBaseController
{
    protected string $crudModelClass    = ReglasModel::class;
    protected string $crudPrimaryKey   = 'id_regla';
    protected string $crudFieldName    = 'numero_regla';
    protected string $crudEntityLabel  = 'Regla';
    protected string $crudGuardarMethod  = 'guardarRegla';
    protected string $crudEliminarMethod = 'eliminarRegla';

    protected function getCrudValidationRules($id = null): array
    {
        $rules = [
            'numero_regla' => 'required|min_length[1]|max_length[100]',
        ];
        if ($id && (int) $id > 0) {
            $rules['habilitada'] = 'required|in_list[0,1]';
        }
        return $rules;
    }

    protected function prepararDataForGuardar(array $data): array
    {
        $id = $data[$this->crudPrimaryKey] ?? null;
        $data['numero_regla'] = trim($this->request->getPost('numero_regla'));
        $data['habilitada']   = ($id && (int) $id > 0)
            ? (int) $this->request->getPost('habilitada')
            : 1;
        if (! ($id && (int) $id > 0)) {
            helper('auth');
            $usuario = usuario_actual();
            if ($usuario && ! empty($usuario['id_usuario'])) {
                $data['id_usuario_creacion'] = (int) $usuario['id_usuario'];
            }
        }
        return $data;
    }

    public function index()
    {
        return view('reglas/index', ['titulo' => 'Reglas']);
    }
}
