<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Libraries\CustomAuth;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Filtro de login: redirige a auth/login si no está logueado.
 * Si se pasan argumentos (roles), verifica que el usuario tenga uno de esos grupos.
 */
class CheckLogin implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $uri = $request->getUri()->getPath();
        $uri = trim($uri, '/');
        if (strpos($uri, 'index.php/') === 0) {
            $uri = substr($uri, strlen('index.php/'));
        }
        $basePath = trim((string) parse_url(base_url(), PHP_URL_PATH), '/');
        if ($basePath !== '' && $uri === $basePath) {
            $uri = '';
        } elseif ($basePath !== '' && strpos($uri, $basePath . '/') === 0) {
            $uri = substr($uri, strlen($basePath) + 1);
        }
        $public = [
            'auth/login',
            'auth/logout',
            'auth/google-sign-in',
            'auth/pendiente',
            'auth/rechazado',
        ];
        foreach ($public as $path) {
            if ($uri === $path || strpos($uri, $path . '/') === 0) {
                return null;
            }
        }
        if (preg_match('#^calibracion/ver/[a-zA-Z0-9_-]+$#', $uri)) {
            return null;
        }

        $ionAuth = new CustomAuth();
        if (! $ionAuth->loggedIn()) {
            return redirect()->to(site_url('auth/login'));
        }

        $userId = (int) $ionAuth->getUserId();
        $cambiarContrasenaUri = 'auth/cambiar-contrasena';
        $logoutUri = 'auth/logout';
        $session = \Config\Services::session();
        $justChangedPassword = (bool) $session->get('password_just_changed');
        if ($justChangedPassword) {
            $session->remove('password_just_changed');
        }
        $mustChange = $userId && ! $justChangedPassword && model(\App\Models\UserModel::class)->getMustChangePassword($userId);
        if ($mustChange && $uri !== $cambiarContrasenaUri && $uri !== $logoutUri) {
            return redirect()->to(site_url($cambiarContrasenaUri));
        }

        // Acceso controlado solo por Ion Auth (tabla users). La tabla usuarios ya no se usa para estado pendiente/rechazado.
        if ($arguments !== null && $arguments !== []) {
            helper('roles');
            $roles = getRoles($userId);
            $permitido = false;
            foreach ($roles as $rol) {
                if (in_array($rol, $arguments, true)) {
                    $permitido = true;
                    break;
                }
            }
            if (! $permitido) {
                throw PageNotFoundException::forPageNotFound('No tenés privilegios para ver esta página.');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
