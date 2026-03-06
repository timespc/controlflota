<?php
declare(strict_types=1);

namespace App\Models;

use IonAuth\Models\IonAuthModel;

class CustomAuthModel extends IonAuthModel
{
    protected $session;
    protected $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->session = session();
        $this->userModel = model('UserModel');
    }

    /**
     * Login por email (Google): sin contraseña, solo verifica usuario activo.
     */
    public function loginGoogle(string $identity): bool
    {
        $this->triggerEvents('pre_login');
        if (empty($identity)) {
            $this->setError('IonAuth.login_unsuccessful');
            return false;
        }
        $this->triggerEvents('extra_where');
        $builder = $this->db->table($this->tables['users'])
            ->select('email, id, active, last_login')
            ->where('email', $identity)
            ->where('active', 1)
            ->limit(1)
            ->orderBy('id', 'desc');
        if ($this->db->fieldExists('deleted_at', $this->tables['users'])) {
            $builder->where('deleted_at', null);
        }
        $query = $builder->get();
        if ($this->isMaxLoginAttemptsExceeded($identity)) {
            $this->triggerEvents('post_login_unsuccessful');
            $this->setError('IonAuth.login_timeout');
            return false;
        }
        $user = $query->getRow();
        if (isset($user)) {
            $this->setSessionForGoogle($user);
            $this->updateLastLogin((int) $user->id);
            $this->clearLoginAttempts($identity);
            $this->session->regenerate(false);
            $this->triggerEvents(['post_login', 'post_login_successful']);
            $this->setMessage('IonAuth.login_successful');
            return true;
        }
        return false;
    }

    /**
     * Establece sesión para usuario logueado por Google (compatible con Ion Auth loggedIn/inGroup).
     */
    public function setSessionForGoogle(\stdClass $user): bool
    {
        $this->triggerEvents('pre_set_session');
        $identityCol = $this->config->identity ?? 'email';
        $sessionData = [
            $identityCol => $user->email,
            'email'      => $user->email,
            'user_id'    => (int) $user->id,
            'id'         => (int) $user->id,
            'old_last_login' => $user->last_login,
            'last_check' => time(),
        ];
        $this->session->set($sessionData);
        $this->triggerEvents('post_set_session');
        return true;
    }

    /**
     * Recheck de sesión: si recheckTimer > 0, valida que el usuario siga activo.
     */
    public function recheckSession(): bool
    {
        $recheck = (null !== $this->config->recheckTimer) ? $this->config->recheckTimer : 0;
        if ($recheck !== 0) {
            $lastCheck = $this->session->get('last_check');
            if ($lastCheck && $lastCheck + $recheck < time()) {
                $query = $this->userModel->getIdForEmailActive($this->session->get('email'), 1);
                if ($query->getNumRows() === 1) {
                    $this->session->set('last_check', time());
                } else {
                    $this->triggerEvents('logout');
                    $identity = $this->config->identity;
                    $this->session->remove([$identity, 'id', 'user_id', 'email']);
                    return false;
                }
            }
        }
        return (bool) $this->session->get('email');
    }
}
