<?php

/**
 * Helper de roles (grupos Ion Auth).
 */

if (! function_exists('getRoles')) {
    /**
     * Devuelve los nombres de grupos del usuario (Ion Auth).
     *
     * @param int $user_id ID del usuario (users.id)
     * @return array<string>
     */
    function getRoles(int $user_id): array
    {
        $userGroupModel = model('UserGroupModel');
        $groupModel     = model('GroupModel');
        $rows           = $userGroupModel->getRolesPorUserId($user_id);
        $roles          = [];
        foreach ($rows as $row) {
            $g = $groupModel->find(is_object($row) ? $row->group_id : $row['group_id']);
            if ($g && isset($g->name)) {
                $roles[] = $g->name;
            }
        }
        return $roles;
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
