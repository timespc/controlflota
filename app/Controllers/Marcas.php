<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\MarcasModel;

class Marcas extends CrudBaseController
{
    protected string $crudModelClass    = MarcasModel::class;
    protected string $crudPrimaryKey   = 'id_marca';
    protected string $crudFieldName    = 'marca';
    protected string $crudEntityLabel  = 'Marca';
    protected string $crudGuardarMethod  = 'guardarMarca';
    protected string $crudEliminarMethod = 'eliminarMarca';

    public function index()
    {
        return view('marcas/index', ['titulo' => 'Marcas']);
    }
}
