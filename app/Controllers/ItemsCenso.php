<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\ItemsCensoModel;

class ItemsCenso extends CrudBaseController
{
    protected string $crudModelClass    = ItemsCensoModel::class;
    protected string $crudPrimaryKey   = 'id_item_censo';
    protected string $crudFieldName    = 'item';
    protected string $crudEntityLabel  = 'Ítem censo';
    protected string $crudGuardarMethod  = 'guardarItemCenso';
    protected string $crudEliminarMethod = 'eliminarItemCenso';
    protected string $crudEliminadoSuffix   = 'eliminado';
    protected string $crudActualizadoSuffix = 'actualizado';
    protected string $crudCreadoSuffix     = 'creado';

    public function index()
    {
        return view('items_censo/index', ['titulo' => 'Items Censo']);
    }
}
