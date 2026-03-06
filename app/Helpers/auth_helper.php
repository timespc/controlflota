<?php

/**
 * Helper de autenticación (Ion Auth / CustomAuth).
 *
 * Roles: admin (solo administradores), calibrador (rol por defecto).
 * Algunas rutas/acciones están permitidas solo para admin.
 */

if (! function_exists('usuario_actual')) {
    /**
     * Devuelve el array del usuario en sesión (compatible con sidebar/notificaciones).
     *
     * @return array{id_usuario: int, email: string, nombre: string, avatar_url: null, id_rol: int, rol_nombre: string}|null
     */
    function usuario_actual(): ?array
    {
        $ionAuth = new \App\Libraries\CustomAuth();
        if (! $ionAuth->loggedIn()) {
            return null;
        }
        $userId = $ionAuth->getUserId();
        $email  = session()->get('email') ?: session()->get('identity');
        helper('roles');
        $roles = getRoles((int) $userId);
        $rolNombre = 'calibrador';
        if (in_array('admin', $roles, true)) {
            $rolNombre = 'admin';
        } elseif (! empty($roles)) {
            $rolNombre = $roles[0];
        }
        return [
            'id_usuario' => (int) $userId,
            'email'      => (string) $email,
            'nombre'     => (string) $email,
            'avatar_url' => null,
            'id_rol'     => 0,
            'rol_nombre' => $rolNombre,
        ];
    }
}

if (! function_exists('tiene_rol')) {
    /**
     * Indica si el usuario actual tiene uno de los roles (grupos) indicados.
     *
     * @param string|list<string> $roles Nombre del grupo o lista (ej. 'admin', ['admin', 'members'])
     * @return bool
     */
    function tiene_rol($roles): bool
    {
        $u = usuario_actual();
        if (! $u) {
            return false;
        }
        $roles = (array) $roles;
        return in_array($u['rol_nombre'] ?? '', $roles, true);
    }
}

if (! function_exists('es_admin')) {
    /**
     * Indica si el usuario actual es admin (grupo admin de Ion Auth).
     */
    function es_admin(): bool
    {
        return tiene_rol('admin');
    }
}

if (! function_exists('es_calibrador')) {
    /**
     * Indica si el usuario actual es calibrador (grupo calibrador de Ion Auth).
     * Los admin también pueden hacer todo lo que hace un calibrador.
     */
    function es_calibrador(): bool
    {
        return tiene_rol(['admin', 'calibrador']);
    }
}
