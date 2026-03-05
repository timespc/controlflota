<?php

namespace App\Controllers;

use App\Models\BanderasModel;

class Banderas extends CrudBaseController
{
    protected string $crudModelClass    = BanderasModel::class;
    protected string $crudPrimaryKey   = 'id_bandera';
    protected string $crudFieldName    = 'bandera';
    protected string $crudEntityLabel  = 'Bandera';
    protected string $crudGuardarMethod  = 'guardarBandera';
    protected string $crudEliminarMethod = 'eliminarBandera';

    public function index()
    {
        return view('banderas/index', ['titulo' => 'Banderas']);
    }
}
