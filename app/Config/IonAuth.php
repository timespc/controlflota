<?php

namespace Config;

class IonAuth extends \IonAuth\Config\IonAuth
{
    public $siteTitle   = 'Montajes Campana';
    public $adminEmail  = 'admin@example.com';
    public $emailTemplates = 'auth/email/';
    public $useCiEmail  = true;
    public $emailActivation = false;
    public $identity    = 'email';
    public $adminGroup   = 'admin';
    /** Rol por defecto para nuevos usuarios (ej. login con Google). */
    public $defaultGroup = 'calibrador';
}
