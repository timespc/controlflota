<?php

namespace App\Controllers;

use App\Models\NacionesModel;

class Nacion extends CrudBaseController
{
    protected string $crudModelClass    = NacionesModel::class;
    protected string $crudPrimaryKey   = 'id_nacion';
    protected string $crudFieldName    = 'nacion';
    protected string $crudEntityLabel  = 'Nación';
    protected string $crudGuardarMethod  = 'guardarNacion';
    protected string $crudEliminarMethod = 'eliminarNacion';

    public function index()
    {
        return view('nacion/index', ['titulo' => 'Nación']);
    }
}
