<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo base para todos los modelos de la aplicación.
 *
 * Expone métodos genéricos: get($id), store($data), destroy($id), getAll(), countAll().
 * La convención del proyecto es que los controladores usen métodos propios por modelo
 * (listarTodos, obtenerPorId, guardar, etc.) con nombres y reglas específicas de cada entidad.
 * BaseModel sirve como herencia común (tabla, primaryKey, returnType, etc.); el uso de
 * get/store/destroy/getAll es opcional. Ver app/docs/CONVENCION_MODELOS.md.
 */
class BaseModel extends Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [];
    protected $useSoftDeletes = false;
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    public function __construct()
    {
        parent::__construct();
    }

    public function getAll($paginate = false, $order_field = '', $order_by = 'ASC')
    {
        $query = $this->orderBy($order_field, $order_by);

        if($paginate)
            $sql = $query->paginate(1);
        else
            $sql = $query->findAll();

        return $sql;
    }

    public function get($id)
    {
        return $this->where('id', $id)->first();
    }

    public function store(array $data)
    {
        if(array_key_exists('id', $data) && !empty($data['id'])) {
            // Actualizar
            $id = $data['id'];
            unset($data['id']);
            return $this->update($id, $data);
        } else {
            // Crear
            return $this->insert($data);
        }
    }

    public function destroy(int $id, bool $purge = false)
    {
        return $this->delete($id, $purge);
    }

    public function countAll()
    {
        return $this->countAllResults();
    }
}

