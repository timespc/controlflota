<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class BaseController extends Controller
{
    protected $helpers = ['auth', 'json_response'];

    public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
    }

    /**
     * Verifica que $valor sea una fecha válida en formato Y-m-d.
     * Devuelve false si el valor está vacío/null (se considera opcional por defecto).
     */
    protected function esFechaYmdValida(?string $valor): bool
    {
        if ($valor === null || $valor === '') {
            return true;
        }
        $d = \DateTime::createFromFormat('Y-m-d', $valor);
        return $d !== false && $d->format('Y-m-d') === $valor;
    }

    /**
     * Verifica que $valor sea un entero positivo (> 0) si está presente.
     * Devuelve true si el valor está vacío/null (campo opcional).
     */
    protected function esIdPositivo($valor): bool
    {
        if ($valor === null || $valor === '') {
            return true;
        }
        return ctype_digit((string) $valor) && (int) $valor > 0;
    }
}




