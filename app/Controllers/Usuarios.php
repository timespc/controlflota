<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\NotificacionModel;
use App\Libraries\NotificacionEnvio;

/**
 * Gestión de usuarios (solo administradores). Lista, alta y edición de usuarios que pueden entrar con Google.
 */
class Usuarios extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = model(UserModel::class);
    }

    public function index()
    {
        $u = function_exists('usuario_actual') ? usuario_actual() : null;
        $idActual = $u ? (int) ($u['id_usuario'] ?? 0) : 0;
        return view('usuarios/index', [
            'titulo'           => 'Usuarios',
            'id_usuario_actual' => $idActual,
        ]);
    }

    /**
     * Listar usuarios para DataTable (JSON).
     */
    public function listar()
    {
        try {
            $lista = $this->userModel->listarParaAdmin();
            return $this->response->setJSON(json_response(true, ['data' => $lista]));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, [
                'message' => $e->getMessage(),
                'data'    => [],
            ]));
        }
    }

    /**
     * Obtener un usuario por ID (JSON).
     */
    public function obtener($id = null)
    {
        if (! $id || (int) $id < 1) {
            return $this->response->setJSON(json_response(false, ['message' => 'ID no proporcionado']));
        }
        try {
            $registro = $this->userModel->obtenerParaAdmin((int) $id);
            if ($registro) {
                return $this->response->setJSON(json_response(true, ['data' => $registro]));
            }
            return $this->response->setJSON(json_response(false, ['message' => 'Usuario no encontrado']));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, [
                'message' => 'Error al obtener el usuario: ' . $e->getMessage(),
            ]));
        }
    }

    /**
     * Crear o actualizar usuario. POST: id (opcional), email (obligatorio si nuevo), first_name, last_name, username, grupo, active.
     */
    public function guardar()
    {
        $id = $this->request->getPost('id') ? (int) $this->request->getPost('id') : 0;
        $email = trim((string) $this->request->getPost('email'));
        $firstName = trim((string) $this->request->getPost('first_name'));
        $lastName = trim((string) $this->request->getPost('last_name'));
        $username = trim((string) $this->request->getPost('username'));
        $grupo = $this->request->getPost('grupo');
        $active = $this->request->getPost('active');

        $idActual = 0;
        if (function_exists('usuario_actual')) {
            $u = usuario_actual();
            $idActual = $u ? (int) ($u['id_usuario'] ?? 0) : 0;
        }

        if ($id > 0) {
            // No permitir modificar al usuario administrador principal (id 1) ni al dev (francurtofrd@gmail.com)
            if ($id === 1) {
                return $this->response->setJSON(json_response(false, ['message' => 'No se puede modificar al usuario administrador principal.']));
            }
            $userRow = $this->userModel->find($id);
            $emailUser = is_object($userRow) ? ($userRow->email ?? '') : ($userRow['email'] ?? '');
            if (strtolower(trim($emailUser)) === 'francurtofrd@gmail.com') {
                return $this->response->setJSON(json_response(false, ['message' => 'No se puede modificar al usuario desarrollador.']));
            }
            // No permitir desactivar el propio usuario
            if ($id === $idActual && $active !== null && $active !== '' && ($active === '0' || $active === false)) {
                return $this->response->setJSON(json_response(false, ['message' => 'No podés desactivar tu propio usuario.']));
            }
            // Edición
            $data = [];
            if ($grupo !== null && $grupo !== '') {
                $data['grupo'] = $grupo === 'admin' ? 'admin' : 'calibrador';
            }
            if ($active !== null && $active !== '') {
                $data['active'] = ($active === '1' || $active === true) ? 1 : 0;
            }
            $data['first_name'] = $firstName;
            $data['last_name'] = $lastName;
            $data['username'] = $username !== '' ? $username : null;
            if ($data['first_name'] === '' && $data['last_name'] === '' && $data['username'] === null
                && ! isset($data['grupo']) && ! isset($data['active'])) {
                return $this->response->setJSON(json_response(false, ['message' => 'No hay datos para actualizar']));
            }
            try {
                $this->userModel->actualizarUsuario($id, $data);
                $idNotifDesactivado = null;
                if (isset($data['active']) && (int) $data['active'] === 0) {
                    $emailUsuario = $this->userModel->where('id', $id)->first();
                    $emailUsuario = is_object($emailUsuario) ? $emailUsuario->email : ($emailUsuario['email'] ?? '');
                    $quien = 'Sistema';
                    if ($idActual && function_exists('usuario_actual')) {
                        $ua = usuario_actual();
                        $quien = $ua['email'] ?? 'admin';
                    }
                    $titulo = 'Usuario desactivado';
                    $mensaje = 'El usuario ' . $emailUsuario . ' fue desactivado el ' . date('d/m/Y H:i') . ' por ' . $quien . '.';
                    $idNotifDesactivado = model(NotificacionModel::class)->crear(NotificacionModel::TIPO_USUARIO_DESACTIVADO, $titulo, $mensaje, [
                        'id_user' => $id,
                        'email' => $emailUsuario,
                        'por_email' => $quien,
                        'fecha' => date('Y-m-d H:i:s'),
                    ]);
                }
                $this->response->setJSON(json_response(true, [
                    'message' => 'Usuario actualizado correctamente',
                    'id'      => $id,
                ]));
                $this->response->send();
                if (function_exists('fastcgi_finish_request')) {
                    fastcgi_finish_request();
                }
                if ($idNotifDesactivado !== null) {
                    NotificacionEnvio::notificarAdmins($idNotifDesactivado);
                }
                exit(0);
            } catch (\Exception $e) {
                return $this->response->setJSON(json_response(false, ['message' => $e->getMessage()]));
            }
        }

        // Alta nueva
        if ($email === '') {
            return $this->response->setJSON(json_response(false, [
                'message' => 'El email es obligatorio',
                'errors'  => ['email' => 'El email es obligatorio'],
            ]));
        }
        $grupo = ($grupo === 'admin' ? 'admin' : 'calibrador');
        try {
            $newId = $this->userModel->agregarUsuario($email, $grupo, $firstName !== '' ? $firstName : null, $lastName !== '' ? $lastName : null, $username !== '' ? $username : null);
            // Notificación a admins: usuario dado de alta (solo informativa)
            $quien = $idActual ? (usuario_actual()['email'] ?? 'admin') : 'Sistema';
            $titulo = 'Usuario dado de alta';
            $mensaje = 'Se dio de alta al usuario ' . $email . ' (grupo: ' . $grupo . '). Podrá entrar con Google. Dado de alta por: ' . $quien . '.';
            $notifModel = model(NotificacionModel::class);
            $idNotif = $notifModel->crear(NotificacionModel::TIPO_NUEVO_USUARIO, $titulo, $mensaje, [
                'id_user' => $newId,
                'email' => $email,
                'grupo' => $grupo,
                'por_email' => $quien,
                'fecha' => date('Y-m-d H:i:s'),
            ]);
            NotificacionEnvio::notificarAdmins($idNotif);
            return $this->response->setJSON(json_response(true, [
                'message' => 'Usuario dado de alta. Podrá entrar con Google con ese correo.',
                'id'      => $newId,
            ]));
        } catch (\InvalidArgumentException $e) {
            return $this->response->setJSON(json_response(false, [
                'message' => $e->getMessage(),
                'errors'  => ['email' => $e->getMessage()],
            ]));
        } catch (\RuntimeException $e) {
            return $this->response->setJSON(json_response(false, [
                'message' => $e->getMessage(),
                'errors'  => ['email' => $e->getMessage()],
            ]));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, ['message' => $e->getMessage()]));
        }
    }

    /**
     * Restablecer contraseña del usuario a la por defecto ("password"). El usuario deberá cambiarla en el próximo login.
     * POST usuarios/restablecer-password/(:num)
     */
    public function restablecerPassword($id = null)
    {
        if (! $id || (int) $id < 1) {
            return $this->response->setJSON(json_response(false, ['message' => 'ID no válido']));
        }
        $id = (int) $id;
        if ($id === 1) {
            return $this->response->setJSON(json_response(false, ['message' => 'No se puede restablecer la contraseña del administrador principal.']));
        }
        $userRow = $this->userModel->find($id);
        $emailUser = is_object($userRow) ? ($userRow->email ?? '') : ($userRow['email'] ?? '');
        if (strtolower(trim($emailUser ?? '')) === 'francurtofrd@gmail.com') {
            return $this->response->setJSON(json_response(false, ['message' => 'No se puede restablecer la contraseña del usuario desarrollador.']));
        }
        try {
            if ($this->userModel->restablecerPasswordPorDefecto($id)) {
                // Desbloquear: borrar intentos fallidos de login para este email (Ion Auth)
                $config = config('IonAuth');
                $tables = $config->tables;
                if (! empty($tables['login_attempts']) && (string) $emailUser !== '') {
                    \Config\Database::connect()
                        ->table($tables['login_attempts'])
                        ->where('login', $emailUser)
                        ->delete();
                }
                return $this->response->setJSON(json_response(true, [
                    'message' => 'Contraseña restablecida. El usuario podrá entrar con su email y la contraseña por defecto, y deberá cambiarla al ingresar.',
                ]));
            }
            return $this->response->setJSON(json_response(false, ['message' => 'Usuario no encontrado o error al actualizar.']));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, ['message' => $e->getMessage()]));
        }
    }

    /**
     * Eliminar usuario (solo admins). No se puede eliminar al admin principal (id 1),
     * al usuario desarrollador (francurtofrd@gmail.com) ni a uno mismo.
     * POST usuarios/eliminar/(:num)
     */
    public function eliminar($id = null)
    {
        if (! $id || (int) $id < 1) {
            return $this->response->setJSON(json_response(false, ['message' => 'ID no válido']));
        }
        $id = (int) $id;
        $idActual = 0;
        if (function_exists('usuario_actual')) {
            $u = usuario_actual();
            $idActual = $u ? (int) ($u['id_usuario'] ?? 0) : 0;
        }
        if ($id === $idActual) {
            return $this->response->setJSON(json_response(false, ['message' => 'No podés eliminar tu propio usuario.']));
        }
        if ($id === 1) {
            return $this->response->setJSON(json_response(false, ['message' => 'No se puede eliminar al usuario administrador principal.']));
        }
        $userRow = $this->userModel->find($id);
        $emailUser = is_object($userRow) ? ($userRow->email ?? '') : ($userRow['email'] ?? '');
        if (strtolower(trim($emailUser ?? '')) === 'francurtofrd@gmail.com') {
            return $this->response->setJSON(json_response(false, ['message' => 'No se puede eliminar al usuario desarrollador.']));
        }
        try {
            if (! $this->userModel->eliminarUsuario($id)) {
                return $this->response->setJSON(json_response(false, ['message' => 'Usuario no encontrado o error al eliminar.']));
            }
            $quien = $idActual ? (usuario_actual()['email'] ?? 'admin') : 'Sistema';
            $titulo = 'Usuario eliminado';
            $mensaje = 'El usuario ' . $emailUser . ' fue eliminado el ' . date('d/m/Y H:i') . ' por ' . $quien . '.';
            $notifModel = model(NotificacionModel::class);
            $idNotif = $notifModel->crear(NotificacionModel::TIPO_USUARIO_DESACTIVADO, $titulo, $mensaje, [
                'id_user'   => $id,
                'email'     => $emailUser,
                'por_email' => $quien,
                'fecha'     => date('Y-m-d H:i:s'),
            ]);
            NotificacionEnvio::notificarAdmins($idNotif);
            return $this->response->setJSON(json_response(true, [
                'message' => 'Usuario eliminado correctamente.',
            ]));
        } catch (\Exception $e) {
            return $this->response->setJSON(json_response(false, ['message' => $e->getMessage()]));
        }
    }
}
