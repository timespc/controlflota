<?php

/**
 * Helper para respuestas JSON unificadas.
 *
 * Estándar: { success: bool, message?: string, data?: mixed, errors?: array, id?: mixed, total?: int }
 * - success: siempre presente.
 * - message: mensaje para el usuario (éxito o error).
 * - data: listado o registro (listar/obtener).
 * - errors: errores de validación (guardar).
 * - id: id del registro creado/actualizado (guardar).
 * - total: cantidad total (total).
 */

if (! function_exists('json_response')) {
    /**
     * Construye el array estándar de respuesta JSON.
     *
     * @param bool  $success
     * @param array $options Claves opcionales: message, data, errors, id, total
     * @return array<string, mixed>
     */
    function json_response(bool $success, array $options = []): array
    {
        $out = ['success' => $success];
        foreach ($options as $key => $value) {
            $out[$key] = $value;
        }
        return $out;
    }
}
