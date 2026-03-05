<?php

/**
 * Helper de roles (grupos Ion Auth).
 */

if (! function_exists('getRoles')) {
    /**
     * Devuelve los nombres de grupos del usuario (Ion Auth).
     * Usa un único JOIN para evitar consultas N+1 al resolver nombres de grupo.
     *
     * @param int $user_id ID del usuario (users.id)
     * @return array<string>
     */
    function getRoles(int $user_id): array
    {
        $config = config('IonAuth');
        $tables = $config->tables;
        $db = \Config\Database::connect();
        $rows = $db->table($tables['users_groups'] . ' ug')
            ->select('g.name')
            ->join($tables['groups'] . ' g', 'g.id = ug.group_id', 'inner')
            ->where('ug.user_id', $user_id)
            ->get()->getResultArray();
        return array_column($rows, 'name');
    }
}

if (! function_exists('protegerElemento')) {
    /**
     * Indica si el usuario actual tiene uno de los roles permitidos.
     *
     * @param array<string> $roles_permitidos
     * @return bool
     */
    function protegerElemento(array $roles_permitidos): bool
    {
        $ionAuth = new \App\Libraries\CustomAuth();
        $roles   = getRoles($ionAuth->getUserId());
        foreach ($roles as $rol) {
            if (in_array($rol, $roles_permitidos, true)) {
                return true;
            }
        }
        return false;
    }
}
