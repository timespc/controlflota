<?php
declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para la tabla users_groups de Ion Auth.
 */
class UserGroupModel extends Model
{
    protected $table         = 'users_groups';
    protected $primaryKey   = 'id';
    protected $allowedFields = ['user_id', 'group_id'];
    protected $returnType   = 'object';

    /**
     * Devuelve los grupos del usuario (cada fila con group_id).
     * Para nombres de grupo usar GroupModel->find(group_id)->name en el helper.
     */
    public function getRolesPorUserId(int $user_id): array
    {
        return $this->where('user_id', $user_id)->findAll();
    }
}
