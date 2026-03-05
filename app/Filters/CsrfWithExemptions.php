<?php

namespace App\Filters;

use CodeIgniter\Filters\CSRF;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Security\Exceptions\SecurityException;

/**
 * Filtro CSRF que excluye rutas que no pueden enviar el token (ej. Google Sign-In).
 */
class CsrfWithExemptions extends CSRF
{
    /** Rutas exentas de CSRF (path relativo al baseURL, sin barra inicial). */
    private const EXEMPT_URIS = [
        'auth/google-sign-in',
    ];

    public function before(RequestInterface $request, $arguments = null)
    {
        if (! $request instanceof IncomingRequest) {
            return;
        }

        // En testing no aplicar CSRF para facilitar tests POST
        if (defined('ENVIRONMENT') && ENVIRONMENT === 'testing') {
            return null;
        }

        $uri = $this->normalizeUri($request);
        foreach (self::EXEMPT_URIS as $exempt) {
            if ($uri === $exempt || strpos($uri, $exempt . '/') === 0) {
                return;
            }
        }

        return parent::before($request, $arguments);
    }

    private function normalizeUri(RequestInterface $request): string
    {
        $uri = trim($request->getUri()->getPath(), '/');
        if (strpos($uri, 'index.php/') === 0) {
            $uri = substr($uri, strlen('index.php/'));
        }
        $basePath = trim((string) parse_url(base_url(), PHP_URL_PATH), '/');
        if ($basePath !== '' && $uri === $basePath) {
            $uri = '';
        } elseif ($basePath !== '' && strpos($uri, $basePath . '/') === 0) {
            $uri = substr($uri, strlen($basePath) + 1);
        }
        return $uri;
    }
}
