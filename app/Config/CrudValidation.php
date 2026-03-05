<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Reglas de validación centralizadas para CRUD simples (CrudBaseController).
 * Clave = nombre del campo principal del formulario (bandera, marca, etc.).
 * Reglas con lógica condicional (ej. Reglas con habilitada) se definen en el controlador.
 */
class CrudValidation extends BaseConfig
{
    /**
     * Reglas por campo principal.
     *
     * @var array<string, array<string, string>>
     */
    public array $rules = [
        'bandera'   => ['bandera'   => 'required|min_length[1]|max_length[255]'],
        'marca'     => ['marca'     => 'required|min_length[1]|max_length[255]'],
        'calibrador'=> ['calibrador'=> 'required|min_length[1]|max_length[255]'],
        'medida'    => ['medida'    => 'required|min_length[1]|max_length[255]'],
        'nacion'    => ['nacion'    => 'required|min_length[1]|max_length[255]'],
    ];
}
