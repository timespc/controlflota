<?php

namespace App\Controllers;

use App\Models\CubiertasModel;

class Cubiertas extends CrudBaseController
{
    protected string $crudModelClass    = CubiertasModel::class;
    protected string $crudPrimaryKey   = 'id_cubierta';
    protected string $crudFieldName    = 'medida';
    protected string $crudEntityLabel  = 'Cubierta';
    protected string $crudGuardarMethod  = 'guardarCubierta';
    protected string $crudEliminarMethod = 'eliminarCubierta';

    public function index()
    {
        return view('cubiertas/index', ['titulo' => 'Cubiertas']);
    }
}
