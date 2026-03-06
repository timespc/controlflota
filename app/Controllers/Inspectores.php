<?php
declare(strict_types=1);

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
    protected string $crudOpcionesIdField    = 'id_inspector';
    protected string $crudOpcionesLabelField = 'inspector';

    public function index()
    {
        return view('inspectores/index', ['titulo' => 'Inspectores']);
    }
}
