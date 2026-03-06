<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class App extends BaseConfig
{
    public string $baseURL = 'http://localhost/montajes-campana/public/';

    public string $indexPage = '';

    public function __construct()
    {
        parent::__construct();
        if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== '') {
            $protocol = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
            $path = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/\\') . '/';
            $this->baseURL = $protocol . '://' . $_SERVER['HTTP_HOST'] . $path;
        }
    }

    public string $uriProtocol = 'REQUEST_URI';

    public string $defaultLocale = 'es';

    public bool $negotiateLocale = false;

    public array $supportedLocales = ['es'];

    public string $appTimezone = 'America/Argentina/Buenos_Aires';

    public string $charset = 'UTF-8';

    public bool $forceGlobalSecureRequests = false; // env('app.forceGlobalSecureRequests') — set to true in production .env

    public string $sessionDriver = 'CodeIgniter\Session\Handlers\FileHandler';

    public string $sessionCookieName = 'ci_session';

    public int $sessionExpiration = 7200;

    public string $sessionSavePath = WRITEPATH . 'session';

    public bool $sessionMatchIP = false;

    public int $sessionTimeToUpdate = 300;

    public bool $sessionRegenerateDestroy = false; // env('app.sessionRegenerateDestroy') — set to true in production .env

    public string $cookiePrefix = '';

    public string $cookieDomain = '';

    public string $cookiePath = '/';

    public bool $cookieSecure = false;

    public bool $cookieHTTPOnly = true;

    public string $cookieSameSite = 'Lax';

    /**
     * --------------------------------------------------------------------------
     * Reverse Proxy IPs
     * --------------------------------------------------------------------------
     *
     * If your server is behind a reverse proxy, you must whitelist the proxy
     * IP addresses from which CodeIgniter should trust headers such as
     * X-Forwarded-For or Client-IP in order to properly identify
     * the visitor's IP address.
     *
     * You need to set a proxy IP address or IP address with subnets and
     * the HTTP header for the client IP address.
     *
     * Here are some examples:
     *     [
     *         '10.0.1.200'     => 'X-Forwarded-For',
     *         '192.168.5.0/24' => 'X-Real-IP',
     *     ]
     *
     * @var array<string, string>
     */
    public array $proxyIPs = [];

    public string $CSRFTokenName = 'csrf_test_name';

    public string $CSRFHeaderName = 'X-CSRF-TOKEN';

    public string $CSRFCookieName = 'csrf_cookie_name';

    public int $CSRFExpire = 7200;

    public bool $CSRFRegenerate = true;

    public bool $CSRFRedirect = true;

    public string $CSRFSameSite = 'Lax';

    public bool $CSPEnabled = false;

    /**
     * --------------------------------------------------------------------------
     * Allowed Hostnames
     * --------------------------------------------------------------------------
     *
     * When using the allowedHostnames feature, you must specify the hosts
     * on which the requests to your application will be made.
     *
     * @var list<string>
     */
    public array $allowedHostnames = [];

    /**
     * --------------------------------------------------------------------------
     * Allowed URL Characters
     * --------------------------------------------------------------------------
     *
     * This lets you specify which characters are permitted within your URLs.
     * When someone tries to submit a URL with disallowed characters they will
     * get a warning message.
     *
     * As a security measure you are STRONGLY encouraged to restrict URLs to
     * as few characters as possible.
     *
     * By default, only these are allowed: `a-z 0-9~%.:_-`
     *
     * Set an empty string to allow all characters -- but only if you are insane.
     *
     * The configured value is actually a regular expression character group
     * and it will be used as: '/\A[<permittedURIChars>]+\z/iu'
     *
     * DO NOT CHANGE THIS UNLESS YOU FULLY UNDERSTAND THE REPERCUSSIONS!!
     *
     * @var string
     */
    public string $permittedURIChars = 'a-z 0-9~%.:_\-';
}



