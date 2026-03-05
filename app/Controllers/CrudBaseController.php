<?php

namespace App\Controllers;

/**
 * Controlador base para CRUD simples (listar, obtener, guardar, eliminar, total).
 * Las subclases definen las propiedades de configuración y opcionalmente
 * sobrescriben prepararDataForGuardar() o getCrudValidationRules().
 */
abstract class CrudBaseController extends BaseController
{
    /** Clase del modelo (ej. BanderasModel::class) */
    protected string $crudModelClass = '';

    /** Nombre de la clave primaria en BD y en POST (ej. id_bandera) */
    protected string $crudPrimaryKey = '';

    /** Nombre del campo principal del formulario (ej. bandera, marca) */
    protected string $crudFieldName = '';

    /** Etiqueta de la entidad para mensajes (ej. Bandera, Marca) */
    protected string $crudEntityLabel = '';

    /** Método del modelo para guardar (ej. guardarBandera) */
    protected string $crudGuardarMethod = '';

    /** Método del modelo para eliminar (ej. eliminarBandera) */
    protected string $crudEliminarMethod = '';

    /** Método del modelo para total (opcional, ej. obtenerTotal). Vacío = no exponer total() */
    protected string $crudTotalMethod = 'obtenerTotal';

    /** Sufijos para mensajes (género): eliminada/eliminado, actualizada/actualizado, creada/creado */
    protected string $crudEliminadoSuffix   = 'eliminada';
    protected string $crudActualizadoSuffix = 'actualizada';
    protected string $crudCreadoSuffix     = 'creada';

    /** Reglas de validación para guardar. Si está vacío se usa [ crudFieldName => 'required|min_length[1]|max_length[255]' ] */
    protected array $crudValidationRules = [];

    /** @var object|null Instancia del modelo (lazy) */
    protected $crudModel;

    protected function getCrudModel(): object
    {
        if ($this->crudModel === null) {
            $this->crudModel = model($this->crudModelClass);
        }
        return $this->crudModel;
    }

    /**
     * Inyectar el modelo (para tests con mock). Si se llama antes de getCrudModel(), se usará este en lugar del real.
     */
    public function setCrudModel(object $model): void
    {
        $this->crudModel = $model;
    }

    /**
     * Hook: reglas de validación para guardar(). Por defecto usa $crudValidationRules
     * o construye una regla estándar para el campo principal.
     *
     * @param mixed $id ID del registro si es edición (desde POST), para reglas condicionales
     */
    protected function getCrudValidationRules($id = null): array
    {
        if ($this->crudValidationRules !== []) {
            return $this->crudValidationRules;
        }
        $config = config(\Config\CrudValidation::class);
        if ($config !== null && isset($config->rules[$this->crudFieldName])) {
            return $config->rules[$this->crudFieldName];
        }
        return [
            $this->crudFieldName => 'required|min_length[1]|max_length[255]',
        ];
    }

    /**
     * Hook: preparar datos antes de llamar al modelo en guardar().
     * Por defecto devuelve $data con el campo principal y la PK si es edición.
     * Reglas u otros pueden sobrescribir para añadir habilitada, id_usuario_creacion, etc.
     */
    protected function prepararDataForGuardar(array $data): array
    {
        return $data;
    }

    public function listar()
    {
        try {
            $model = $this->getCrudModel();
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
        if (! $id) {
            return $this->response->setJSON(json_response(false, ['message' => 'ID no proporcionado']));
        }
        try {
            $model     = $this->getCrudModel();
            $registro  = $model->obtenerPorId($id);
            if ($registro) {
                return $this->response->setJSON(json_response(true, ['data' => $registro]));
            }
            return $this->response->setJSON(json_response(false, ['message' => 'Registro no encontrado']));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, [
                'message' => 'Error al obtener el registro: ' . $e->getMessage(),
            ]));
        }
    }

    public function guardar()
    {
        $validation = \Config\Services::validation();
        $idPost     = $this->request->getPost($this->crudPrimaryKey);
        $rules      = $this->getCrudValidationRules($idPost);
        if (! $this->validate($rules)) {
            return $this->response->setJSON(json_response(false, [
                'message' => 'Error de validación',
                'errors'  => $validation->getErrors(),
            ]));
        }
        try {
            $id   = $idPost;
            $data = [
                $this->crudFieldName => $this->request->getPost($this->crudFieldName),
            ];
            if ($id && (int) $id > 0) {
                $data[$this->crudPrimaryKey] = $id;
            }
            $data = $this->prepararDataForGuardar($data);
            $model = $this->getCrudModel();
            $model->{$this->crudGuardarMethod}($data);
            $idResp = $id && (int) $id > 0 ? $id : $model->getInsertID();
            $msg   = $id && (int) $id > 0
                ? $this->crudEntityLabel . ' ' . $this->crudActualizadoSuffix . ' correctamente'
                : $this->crudEntityLabel . ' ' . $this->crudCreadoSuffix . ' correctamente';
            return $this->response->setJSON(json_response(true, [
                'message' => $msg,
                'id'      => $idResp,
            ]));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, [
                'message' => 'Error: ' . $e->getMessage(),
            ]));
        }
    }

    public function eliminar($id = null)
    {
        if (! $id) {
            return $this->response->setJSON(json_response(false, ['message' => 'ID no proporcionado']));
        }
        try {
            $model = $this->getCrudModel();
            $model->{$this->crudEliminarMethod}($id);
            return $this->response->setJSON(json_response(true, [
                'message' => $this->crudEntityLabel . ' ' . $this->crudEliminadoSuffix . ' correctamente',
            ]));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, [
                'message' => 'Error: ' . $e->getMessage(),
            ]));
        }
    }

    public function total()
    {
        if ($this->crudTotalMethod === '') {
            return $this->response->setJSON(json_response(false, ['total' => 0, 'message' => 'No disponible']));
        }
        try {
            $model = $this->getCrudModel();
            $total = $model->{$this->crudTotalMethod}();
            return $this->response->setJSON(json_response(true, ['total' => $total]));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, [
                'total'   => 0,
                'message' => $e->getMessage(),
            ]));
        }
    }
}
