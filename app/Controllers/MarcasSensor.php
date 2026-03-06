<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\MarcasSensorModel;

class MarcasSensor extends CrudBaseController
{
    protected string $crudModelClass    = MarcasSensorModel::class;
    protected string $crudPrimaryKey   = 'id_marca_sensor';
    protected string $crudFieldName    = 'marca';
    protected string $crudEntityLabel  = 'Marca';
    protected string $crudGuardarMethod  = 'guardarMarca';
    protected string $crudEliminarMethod = 'eliminarMarca';

    public function index()
    {
        return view('marcas_sensor/index', ['titulo' => 'Marcas de sensores']);
    }
}
