<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\CalibradoresModel;

class Calibradores extends CrudBaseController
{
    protected string $crudModelClass    = CalibradoresModel::class;
    protected string $crudPrimaryKey   = 'id_calibrador';
    protected string $crudFieldName    = 'calibrador';
    protected string $crudEntityLabel  = 'Calibrador';
    protected string $crudGuardarMethod  = 'guardarCalibrador';
    protected string $crudEliminarMethod = 'eliminarCalibrador';
    protected string $crudEliminadoSuffix   = 'eliminado';
    protected string $crudActualizadoSuffix = 'actualizado';
    protected string $crudCreadoSuffix     = 'creado';
    protected string $crudOpcionesIdField    = 'id_calibrador';
    protected string $crudOpcionesLabelField = 'calibrador';

    public function index()
    {
        return view('calibradores/index', ['titulo' => 'Calibradores']);
    }
}
