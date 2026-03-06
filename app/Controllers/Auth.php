<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Models\CustomAuthModel;
use App\Models\UserModel;
use Google\Client;

/**
 * Autenticación con Ion Auth: login tradicional y Google Sign-In (solo usuarios existentes).
 * Mismo esquema que Itdelfoscampus.
 */
class Auth extends BaseController
{
    protected $client;
    protected $modelUser;
    protected $ionAuth;
    protected $ionAuthModel;
    protected $configIonAuth;
    protected $session;
    protected $validation;
    protected $validationListTemplate = 'list';
    protected $viewsFolder = 'auth';
    protected $data = [];

    public function __construct()
    {
        $this->modelUser    = model(UserModel::class);
        $this->client      = new Client();
        $this->client->addScope('email');
        $this->client->setClientId(env('GOOGLE_CLIENT_ID', ''));
        $this->ionAuth      = new \App\Libraries\CustomAuth();
        $this->ionAuthModel = new CustomAuthModel();
        $this->validation   = \Config\Services::validation();
        helper(['form', 'url']);
        $this->configIonAuth = config('IonAuth');
        $this->session      = \Config\Services::session();
        $templates = $this->configIonAuth->templates ?? [];
        if (! empty($templates['errors']['list'])) {
            $this->validationListTemplate = $templates['errors']['list'];
        }
    }

    /**
     * Página de login: usuario/contraseña y botón Google (GIS).
     */
    public function login()
    {
        if ($this->ionAuth->loggedIn()) {
            return redirect()->to(site_url());
        }

        $this->data['title'] = lang('Auth.login_heading');
        $this->validation->setRule('identity', str_replace(':', '', lang('Auth.login_identity_label')), 'required');
        $this->validation->setRule('password', str_replace(':', '', lang('Auth.login_password_label')), 'required');

        if ($this->request->is('post') && $this->validation->withRequest($this->request)->run()) {
            $remember = (bool) $this->request->getVar('remember');
            $identity = trim((string) $this->request->getVar('identity'));
            $password = $this->request->getVar('password');
            $password = is_string($password) ? $password : '';
            // Ion Auth usa identity = email; si el usuario escribe solo el nombre (sin @), resolver a email
            if (strpos($identity, '@') === false) {
                $emailByUsername = $this->modelUser->getEmailByUsername($identity);
                if ($emailByUsername !== null) {
                    $identity = $emailByUsername;
                }
            }
            if ($this->ionAuth->login($identity, $password, $remember)) {
                $this->session->setFlashdata('message', $this->ionAuth->messages());
                return $this->redirectByGroup();
            }
            $this->session->setFlashdata('message', $this->ionAuth->errors($this->validationListTemplate));
            return redirect()->back()->withInput();
        }

        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : $this->session->getFlashdata('message');
        $this->data['identity'] = [
            'name'  => 'identity',
            'id'    => 'identity',
            'type'  => 'text',
            'value' => set_value('identity'),
        ];
        $this->data['password'] = [
            'name' => 'password',
            'id'   => 'password',
            'type' => 'password',
        ];
        return view($this->viewsFolder . '/login', $this->data);
    }

    /**
     * POST: Google Sign-In (credential desde GIS). Solo usuarios ya existentes en users.
     */
    public function googleSignIn()
    {
        $id_token         = $this->request->getPost('credential');
        $g_csrf_token_body = $this->request->getPost('g_csrf_token');
        $g_csrf_token     = $this->request->getCookie('g_csrf_token');

        $clientId = env('GOOGLE_CLIENT_ID', '');
        if (empty($clientId)) {
            $this->session->setFlashdata('message', 'Google Sign-In no está configurado (GOOGLE_CLIENT_ID).');
            return redirect()->to(site_url('auth/login'));
        }

        if (empty($id_token) || ($g_csrf_token_body !== '' && $g_csrf_token !== '' && $g_csrf_token_body !== $g_csrf_token)) {
            $this->session->setFlashdata('message', 'Faltan datos de Google. Intentá de nuevo.');
            return redirect()->to(site_url('auth/login'));
        }

        try {
            $payload = $this->client->verifyIdToken($id_token);
        } catch (\Exception $e) {
            $this->session->setFlashdata('message', 'No se pudo verificar la sesión de Google. Intentá de nuevo.');
            return redirect()->to(site_url('auth/login'));
        }

        if (! $payload || ($payload['aud'] ?? '') !== $clientId) {
            $this->session->setFlashdata('message', 'Error de verificación con Google. Revisá que el origen esté autorizado en la consola de Google.');
            return redirect()->to(site_url('auth/login'));
        }

        $email = $payload['email'] ?? '';
        if (empty($email)) {
            $this->session->setFlashdata('message', 'No se obtuvo el correo de Google.');
            return redirect()->to(site_url('auth/login'));
        }

        if ($this->modelUser->checkExistUser($email) < 1) {
            $this->session->setFlashdata('message', 'Este correo no está registrado. Solo pueden entrar con Google usuarios ya dados de alta. Contactá al administrador.');
            return redirect()->to(site_url('auth/login'));
        }

        if (! $this->ionAuthModel->loginGoogle($email)) {
            $this->session->setFlashdata('message', 'No se pudo iniciar sesión. Intentá de nuevo.');
            return redirect()->to(site_url('auth/login'));
        }

        $this->session->setFlashdata('message', $this->ionAuth->messages());
        return $this->redirectByGroup();
    }

    /**
     * Redirige según el grupo del usuario. Si debe cambiar contraseña, va a auth/cambiar-contrasena.
     */
    protected function redirectByGroup()
    {
        $userId = (int) $this->ionAuth->getUserId();
        if ($userId && $this->modelUser->getMustChangePassword($userId)) {
            return redirect()->to(site_url('auth/cambiar-contrasena'))->withCookies();
        }
        return redirect()->to(site_url())->withCookies();
    }

    /**
     * Cerrar sesión.
     */
    public function logout()
    {
        $this->ionAuth->logout();
        $this->session->setFlashdata('message', $this->ionAuth->messages());
        return redirect()->to(site_url('auth/login'))->withCookies();
    }

    /**
     * Vista: cuenta pendiente de aprobación.
     */
    public function pendiente()
    {
        return view($this->viewsFolder . '/pendiente');
    }

    /**
     * Vista: cuenta rechazada.
     */
    public function rechazado()
    {
        return view($this->viewsFolder . '/rechazado');
    }

    /**
     * Cambiar contraseña obligatoria (primer acceso con contraseña por defecto).
     * GET: formulario; POST: validar y actualizar.
     */
    public function cambiarContrasena()
    {
        if (! $this->ionAuth->loggedIn()) {
            return redirect()->to(site_url('auth/login'));
        }
        $userId = (int) $this->ionAuth->getUserId();
        if (! $userId || ! $this->modelUser->getMustChangePassword($userId)) {
            return redirect()->to(site_url());
        }

        $this->data['title'] = 'Cambiar contraseña';
        // Guía de contraseñas: mínimo 10 caracteres, al menos una mayúscula, una minúscula y un número
        $this->validation->setRule('password_nueva', 'Nueva contraseña', 'required|min_length[10]|regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/]', [
            'regex_match' => 'La contraseña debe tener al menos una mayúscula, una minúscula y un número.',
        ]);
        $this->validation->setRule('password_confirmar', 'Confirmar nueva contraseña', 'required|matches[password_nueva]');

        if ($this->request->is('post') && $this->validation->withRequest($this->request)->run()) {
            $nueva  = $this->request->getPost('password_nueva');
            $user   = $this->modelUser->find($userId);
            if (! $user) {
                $this->session->setFlashdata('message', 'Usuario no encontrado.');
                return redirect()->back()->withInput();
            }
            $hash = password_hash($nueva, PASSWORD_BCRYPT, ['cost' => 10]);
            if ($this->modelUser->setPasswordAndClearMustChange($userId, $hash)) {
                $this->session->set('password_just_changed', 1);
                $this->session->setFlashdata('message', 'Contraseña actualizada. Ya podés usar la aplicación.');
                return redirect()->to(site_url());
            }
            $this->session->setFlashdata('message', 'No se pudo actualizar la contraseña. Intentá de nuevo.');
            return redirect()->back()->withInput();
        }

        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : $this->session->getFlashdata('message');
        return view($this->viewsFolder . '/cambiar_contrasena', $this->data);
    }
}
