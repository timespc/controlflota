<?php
declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Modelo para la tabla groups de Ion Auth.
 */
class GroupModel extends Model
{
    protected $table         = 'groups';
    protected $primaryKey   = 'id';
    protected $allowedFields = ['name', 'description'];
    protected $returnType   = 'object';
}
