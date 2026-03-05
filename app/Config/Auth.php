<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Configuración para autenticación (Google OAuth y roles).
 */
class Auth extends BaseConfig
{
    /** Client ID de Google OAuth (credencial en Google Cloud Console) */
    public string $googleClientId = '';

    /** Client Secret de Google OAuth */
    public string $googleClientSecret = '';

    /** Redirect URI debe coincidir con la configurada en Google (ej. https://tudominio.com/auth/callback) */
    public string $googleRedirectUri = '';

    /** Clave en sesión donde se guarda el usuario logueado */
    public string $sessionKey = 'auth_usuario';

    /** Email del administrador (para mostrar en vista de usuario rechazado) */
    public string $adminEmail = '';

    public function __construct()
    {
        parent::__construct();
        $this->googleClientId     = env('GOOGLE_CLIENT_ID', '');
        $this->googleClientSecret = env('GOOGLE_CLIENT_SECRET', '');
        $this->googleRedirectUri  = env('GOOGLE_REDIRECT_URI', '');
        $this->adminEmail         = env('ADMIN_EMAIL', '');
    }
}
