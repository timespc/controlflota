<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Security extends BaseConfig
{
   /**
     * --------------------------------------------------------------------------
     * CSRF Protection Method
     * --------------------------------------------------------------------------
     *
     * Protection Method for Cross Site Request Forgery protection.
     *
     * @var string 'cookie' or 'session'
     */
    public string $csrfProtection = 'cookie';

    /**
     * --------------------------------------------------------------------------
     * CSRF Token Randomization
     * --------------------------------------------------------------------------
     *
     * Randomize the CSRF Token for added security.
     */
    public bool $tokenRandomize = false;

    /**
     * --------------------------------------------------------------------------
     * CSRF Token Name
     * --------------------------------------------------------------------------
     *
     * Token name for Cross Site Request Forgery protection.
     */
    public string $tokenName = 'csrf_test_name';

    /**
     * --------------------------------------------------------------------------
     * CSRF Header Name
     * --------------------------------------------------------------------------
     *
     * Header name for Cross Site Request Forgery protection.
     */
    public string $headerName = 'X-CSRF-TOKEN';

    /**
     * --------------------------------------------------------------------------
     * CSRF Cookie Name
     * --------------------------------------------------------------------------
     *
     * Cookie name for Cross Site Request Forgery protection.
     */
    public string $cookieName = 'csrf_cookie_name';

    /**
     * --------------------------------------------------------------------------
     * CSRF Expires
     * --------------------------------------------------------------------------
     *
     * Number of seconds that the token should expire.
     */
    public int $expires = 7200;

    /**
     * --------------------------------------------------------------------------
     * CSRF Regenerate
     * --------------------------------------------------------------------------
     *
     * Regenerate CSRF Token on every request.
     * En false el token no cambia por petición; así el valor del meta en el layout
     * sigue siendo válido para todas las peticiones POST (AJAX) de la página,
     * ya que la cookie CSRF es HttpOnly y JavaScript no puede leerla.
     */
    public bool $regenerate = false;

    /**
     * --------------------------------------------------------------------------
     * CSRF Redirect
     * --------------------------------------------------------------------------
     *
     * Redirect to previous page with error on failure.
     */
    public bool $redirect = true;

    /**
     * --------------------------------------------------------------------------
     * CSRF SameSite
     * --------------------------------------------------------------------------
     *
     * Setting for CSRF SameSite cookie token.
     *
     * Allowed values are: None - Lax - Strict - ''.
     *
     * Defaults to `Lax` as recommended in this link:
     *
     * @see https://portswigger.net/web-security/csrf/samesite-cookies
     *
     * @deprecated `Config\Cookie` $samesite property is used.
     */
    public string $samesite = 'Lax';
}


