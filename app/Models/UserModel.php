<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\GroupModel;
use App\Models\UserGroupModel;

/**
 * Modelo para la tabla users de Ion Auth.
 */
class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'ip_address', 'username', 'password', 'email',
        'activation_selector', 'activation_code',
        'forgotten_password_selector', 'forgotten_password_code', 'forgotten_password_time',
        'remember_selector', 'remember_code',
        'created_on', 'last_login', 'active',
        'first_name', 'last_name', 'company', 'phone',
        'must_change_password', 'deleted_at',
    ];
    protected $returnType    = 'object';

    /**
     * Cuenta cuántos usuarios tienen ese email (solo no eliminados si existe deleted_at).
     */
    public function checkExistUser(string $email): int
    {
        $builder = $this->where('email', $email);
        if ($this->db->fieldExists('deleted_at', $this->table)) {
            $builder->where('deleted_at', null);
        }
        return $builder->countAllResults();
    }

    /**
     * Devuelve el email del usuario activo con ese username (para login por nombre de usuario).
     * Si no hay, no está activo o está eliminado, devuelve null.
     */
    public function getEmailByUsername(string $username): ?string
    {
        $builder = $this->where(['username' => $username, 'active' => 1]);
        if ($this->db->fieldExists('deleted_at', $this->table)) {
            $builder->where('deleted_at', null);
        }
        $row = $builder->first();
        if (! $row) {
            return null;
        }
        return is_object($row) ? ($row->email ?? null) : ($row['email'] ?? null);
    }

    /**
     * Devuelve el resultado de usuarios activos con ese email (para recheckSession: getNumRows() === 1).
     * Excluye usuarios con borrado lógico.
     */
    public function getIdForEmailActive(string $email, $active)
    {
        $builder = $this->where(['email' => $email, 'active' => $active]);
        if ($this->db->fieldExists('deleted_at', $this->table)) {
            $builder->where('deleted_at', null);
        }
        return $builder->get();
    }

    /**
     * Lista usuarios que están en el grupo admin (Ion Auth), activos y no eliminados.
     * Devuelve array de ['id' => id, 'email' => email] para notificaciones.
     */
    public function getAdminUsers(): array
    {
        $config = config('IonAuth');
        $tables = $config->tables;
        $builder = $this->db->table($this->table . ' u')
            ->select('u.id, u.email')
            ->join($tables['users_groups'] . ' ug', 'ug.user_id = u.id')
            ->join($tables['groups'] . ' g', 'g.id = ug.group_id AND g.name = \'admin\'')
            ->where('u.active', 1);
        if ($this->db->fieldExists('deleted_at', $tables['users'])) {
            $builder->where('u.deleted_at', null);
        }
        $rows = $builder->get()->getResultArray();
        return $rows ?: [];
    }

    /**
     * Lista todos los usuarios para el módulo admin (id, email, first_name, last_name, active, last_login, grupos).
     * Excluye usuarios con borrado lógico.
     * Carga grupos en 2 consultas batch (en lugar de N+1) y mapea en PHP.
     */
    public function listarParaAdmin(): array
    {
        $config = config('IonAuth');
        $tables = $config->tables;
        $builder = $this->db->table($this->table . ' u')
            ->select('u.id, u.email, u.first_name, u.last_name, u.username, u.active, u.last_login')
            ->orderBy('u.email', 'ASC');
        if ($this->db->fieldExists('deleted_at', $tables['users'])) {
            $builder->where('u.deleted_at', null);
        }
        $rows = $builder->get()->getResultArray();
        if (empty($rows)) {
            return [];
        }

        // Batch: todos los user_groups de los usuarios listados en 1 consulta
        $userIds = array_column($rows, 'id');
        $ugRows = $this->db->table($tables['users_groups'])
            ->whereIn('user_id', $userIds)
            ->get()->getResultArray();

        // Batch: todos los grupos necesarios en 1 consulta
        $groupIds = array_unique(array_column($ugRows, 'group_id'));
        $groupsById = [];
        if (! empty($groupIds)) {
            $groupRows = $this->db->table($tables['groups'])
                ->whereIn('id', $groupIds)
                ->get()->getResultArray();
            foreach ($groupRows as $g) {
                $groupsById[(int) $g['id']] = $g['name'];
            }
        }

        // Indexar user_groups por user_id
        $ugByUser = [];
        foreach ($ugRows as $ug) {
            $ugByUser[(int) $ug['user_id']][] = (int) $ug['group_id'];
        }

        $out = [];
        foreach ($rows as $r) {
            $id = (int) $r['id'];
            $grupos = [];
            foreach ($ugByUser[$id] ?? [] as $gid) {
                if (isset($groupsById[$gid])) {
                    $grupos[] = $groupsById[$gid];
                }
            }
            $out[] = [
                'id'         => $id,
                'email'      => $r['email'] ?? '',
                'first_name' => $r['first_name'] ?? '',
                'last_name'  => $r['last_name'] ?? '',
                'username'   => $r['username'] ?? '',
                'active'     => (int) ($r['active'] ?? 0),
                'last_login' => $r['last_login'] ? date('d/m/Y H:i', (int) $r['last_login']) : '',
                'grupos'     => $grupos,
                'grupo'      => in_array('admin', $grupos, true) ? 'admin' : 'calibrador',
            ];
        }
        return $out;
    }

    /**
     * Obtiene el ID del grupo por nombre (admin, calibrador, members).
     */
    public function getIdGroupByName(string $name): ?int
    {
        $config = config('IonAuth');
        $tables = $config->tables;
        $row = $this->db->table($tables['groups'])->where('name', $name)->get()->getRow();
        return $row ? (int) $row->id : null;
    }

    /**
     * Agrega un usuario (email obligatorio; podrá entrar con Google). Asigna grupo admin o calibrador.
     * first_name, last_name, username opcionales; si no se pasa username y hay nombre/apellido, se arma con primera letra del nombre + apellido en minúsculas.
     */
    public function agregarUsuario(string $email, string $grupo = 'calibrador', ?string $firstName = null, ?string $lastName = null, ?string $username = null): int
    {
        $config = config('IonAuth');
        $tables = $config->tables;
        $email = trim($email);
        if ($email === '') {
            throw new \InvalidArgumentException('El email es obligatorio.');
        }
        if ($this->checkExistUser($email) > 0) {
            throw new \RuntimeException('Ya existe un usuario con ese email.');
        }
        $groupId = $this->getIdGroupByName($grupo);
        if ($groupId === null) {
            $groupId = $this->getIdGroupByName('calibrador');
        }
        if ($username === null || $username === '') {
            $nombre = trim((string) $firstName);
            $apellido = trim((string) $lastName);
            if ($nombre !== '' || $apellido !== '') {
                $username = ($nombre !== '' ? mb_substr($nombre, 0, 1) : '') . $apellido;
                $username = mb_strtolower($username);
            } else {
                $username = preg_replace('/@.*$/', '', $email);
            }
        }
        $username = substr($username, 0, 100);
        $hash = password_hash('password', PASSWORD_BCRYPT, ['cost' => 10]);
        $data = [
            'ip_address'  => \Config\Services::request()->getIPAddress() ?: '127.0.0.1',
            'username'    => $username,
            'password'    => $hash,
            'email'       => $email,
            'activation_selector' => null,
            'activation_code' => null,
            'forgotten_password_selector' => null,
            'forgotten_password_code' => null,
            'forgotten_password_time' => null,
            'remember_selector' => null,
            'remember_code' => null,
            'created_on'  => time(),
            'last_login'  => null,
            'active'      => 1,
            'first_name'  => trim((string) ($firstName ?? '')) ?: null,
            'last_name'   => trim((string) ($lastName ?? '')) ?: null,
            'company'     => null,
            'phone'       => null,
        ];
        if ($this->db->fieldExists('must_change_password', $tables['users'])) {
            $data['must_change_password'] = 1;
        }
        $this->db->table($tables['users'])->insert($data);
        $userId = (int) $this->db->insertID();
        $this->db->table($tables['users_groups'])->insert([
            'user_id'  => $userId,
            'group_id' => $groupId,
        ]);
        return $userId;
    }

    /**
     * Actualiza usuario: active (0/1), grupo (admin|calibrador), first_name, last_name, username.
     * Reemplaza el grupo asignado.
     */
    public function actualizarUsuario(int $id, array $data): bool
    {
        $config = config('IonAuth');
        $tables = $config->tables;
        $user = $this->find($id);
        if (! $user) {
            return false;
        }
        $updateUsers = [];
        if (isset($data['active'])) {
            $updateUsers['active'] = (int) $data['active'];
        }
        if (array_key_exists('first_name', $data)) {
            $updateUsers['first_name'] = trim((string) $data['first_name']) ?: null;
        }
        if (array_key_exists('last_name', $data)) {
            $updateUsers['last_name'] = trim((string) $data['last_name']) ?: null;
        }
        if (array_key_exists('username', $data)) {
            $u = trim((string) $data['username']);
            $updateUsers['username'] = $u !== '' ? substr($u, 0, 100) : null;
        }
        if ($updateUsers !== []) {
            $this->update($id, $updateUsers);
        }
        if (isset($data['grupo'])) {
            $groupId = $this->getIdGroupByName($data['grupo']);
            if ($groupId !== null) {
                $this->db->table($tables['users_groups'])->where('user_id', $id)->delete();
                $this->db->table($tables['users_groups'])->insert([
                    'user_id'  => $id,
                    'group_id' => $groupId,
                ]);
            }
        }
        return true;
    }

    /**
     * Obtiene un usuario para edición (id, email, first_name, last_name, active, grupo).
     * No devuelve usuarios eliminados (borrado lógico).
     * Resuelve grupos con un solo JOIN en lugar de N consultas separadas.
     */
    public function obtenerParaAdmin(int $id): ?array
    {
        $config = config('IonAuth');
        $tables = $config->tables;
        $builder = $this->db->table($this->table)->where('id', $id);
        if ($this->db->fieldExists('deleted_at', $tables['users'])) {
            $builder->where('deleted_at', null);
        }
        $row = $builder->get()->getRowArray();
        if (! $row) {
            return null;
        }

        // Carga grupos con JOIN en 1 consulta
        $grupoCombinado = $this->db->table($tables['users_groups'] . ' ug')
            ->select('g.name')
            ->join($tables['groups'] . ' g', 'g.id = ug.group_id', 'inner')
            ->where('ug.user_id', $id)
            ->get()->getResultArray();
        $grupos = array_column($grupoCombinado, 'name');

        return [
            'id'         => (int) $row['id'],
            'email'      => $row['email'] ?? '',
            'first_name' => $row['first_name'] ?? '',
            'last_name'  => $row['last_name'] ?? '',
            'username'   => $row['username'] ?? '',
            'active'     => (int) ($row['active'] ?? 0),
            'grupo'      => in_array('admin', $grupos, true) ? 'admin' : 'calibrador',
        ];
    }

    /**
     * Indica si el usuario debe cambiar la contraseña en el próximo acceso.
     * Lee de la misma tabla users que usa Ion Auth (con prefijo si existe).
     */
    public function getMustChangePassword(int $userId): bool
    {
        $config = config('IonAuth');
        $tables = $config->tables;
        $tableName = $this->db->prefixTable($tables['users']);
        if (! $this->db->fieldExists('must_change_password', $tableName)) {
            return false;
        }
        $row = $this->db->table($tables['users'])->select('must_change_password')->where('id', $userId)->get()->getRow();
        return $row && (int) $row->must_change_password === 1;
    }

    /**
     * Actualiza la contraseña del usuario y pone must_change_password = 0.
     * Siempre envía must_change_password = 0 (requiere que la columna exista; ejecutar migración si falta).
     */
    public function setPasswordAndClearMustChange(int $userId, string $newPasswordHash): bool
    {
        $config = config('IonAuth');
        $tables = $config->tables;
        $this->db->table($tables['users'])->where('id', $userId)->update([
            'password' => $newPasswordHash,
            'must_change_password' => 0,
        ]);
        return $this->db->affectedRows() > 0;
    }

    /**
     * Restablece la contraseña del usuario a "password" y obliga a cambiarla en el próximo login.
     * Útil para usuarios creados antes de usar contraseña por defecto o para resetear acceso.
     * Escribe en la misma tabla que usa Ion Auth para el login.
     */
    public function restablecerPasswordPorDefecto(int $userId): bool
    {
        $config = config('IonAuth');
        $tables = $config->tables;
        $user = $this->db->table($tables['users'])->where('id', $userId)->get()->getRow();
        if (! $user) {
            return false;
        }
        $hash = password_hash('password', PASSWORD_BCRYPT, ['cost' => 10]);
        $data = ['password' => $hash];
        if ($this->db->fieldExists('must_change_password', $tables['users'])) {
            $data['must_change_password'] = 1;
        }
        $this->db->table($tables['users'])->where('id', $userId)->update($data);
        return $this->db->affectedRows() > 0;
    }

    /**
     * Borrado lógico de un usuario (solo para uso por admin).
     * Marca deleted_at y active=0; no borra la fila para conservar referencias (id_usuario en calibraciones, etc.).
     * No comprueba aquí si es admin principal o dev; eso lo hace el controlador.
     */
    public function eliminarUsuario(int $id): bool
    {
        $config = config('IonAuth');
        $tables = $config->tables;
        $user = $this->db->table($tables['users'])->where('id', $id)->get()->getRowArray();
        if (! $user) {
            return false;
        }
        $email = $user['email'] ?? '';

        $updated = false;
        if ($this->db->fieldExists('deleted_at', $tables['users'])) {
            $this->db->table($tables['users'])->where('id', $id)->update([
                'deleted_at' => date('Y-m-d H:i:s'),
                'active'     => 0,
            ]);
            $updated = $this->db->affectedRows() > 0;
        } else {
            $this->db->table($tables['users'])->where('id', $id)->update(['active' => 0]);
            $updated = $this->db->affectedRows() > 0;
        }
        if (! empty($tables['login_attempts']) && $email !== '') {
            $this->db->table($tables['login_attempts'])->where('login', $email)->delete();
        }
        return $updated;
    }
}
