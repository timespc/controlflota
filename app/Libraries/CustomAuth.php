<?php

namespace App\Libraries;

use App\Models\CustomAuthModel;
use IonAuth\Libraries\IonAuth;

class CustomAuth extends IonAuth
{
    protected $ionAuthModel;

    public function __construct()
    {
        $this->checkCompatibility();
        $this->config = config('IonAuth');
        $this->email = \Config\Services::email();
        helper('cookie');
        $this->session = session();
        $this->ionAuthModel = new CustomAuthModel();
        $emailConfig = $this->config->emailConfig ?? null;
        if ($this->config->useCiEmail && isset($emailConfig) && is_array($emailConfig)) {
            $this->email->initialize($emailConfig);
        }
        $this->ionAuthModel->triggerEvents('library_constructor');
    }

    /**
     * Para login por Google: recheck usando sesión email/user_id.
     */
    public function loggedInGoogle(): bool
    {
        $this->ionAuthModel->triggerEvents('logged_in');
        return $this->ionAuthModel->recheckSession();
    }
}
