<?php

namespace App\Controllers;

use App\Models\DocumentoModel;
use App\Models\TransportistasModel;
use App\Models\EquiposModel;

/**
 * Documentación adjunta a Transportistas y Equipos.
 * Permite subir imágenes/PDFs y verlos en modal.
 * Control por recurso: solo se permite acceso si la entidad (transportista o equipo) existe.
 */
class Documentos extends BaseController
{
    private const TIPOS_PERMITIDOS = ['transportista', 'unidad', 'equipo'];
    private const EXTENSIONES_PERMITIDAS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf'];
    private const MIME_IMAGEN = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private const MAX_SIZE = 10 * 1024 * 1024; // 10 MB

    /**
     * Comprueba que la entidad exista; si no, devuelve respuesta JSON de error o 404.
     */
    private function entidadExiste(string $tipo, int $idEntidad): bool
    {
        if ($tipo === 'transportista') {
            return model(TransportistasModel::class)->find($idEntidad) !== null;
        }
        if ($tipo === 'unidad' || $tipo === 'equipo') {
            return model(EquiposModel::class)->find($idEntidad) !== null;
        }
        return false;
    }

    /** Normaliza tipo de entidad: 'equipo' se trata como 'unidad' para almacenamiento. */
    private function tipoEntidadParaAlmacenamiento(string $tipo): string
    {
        return $tipo === 'equipo' ? 'unidad' : $tipo;
    }

    /**
     * GET documentos/listar/transportista/:id o documentos/listar/equipo/:id
     */
    public function listar(string $tipo = '', int $idEntidad = 0)
    {
        if (! in_array($tipo, self::TIPOS_PERMITIDOS, true) || $idEntidad <= 0) {
            return $this->response->setJSON(json_response(false, ['message' => 'Parámetros inválidos', 'data' => []]));
        }
        if (! $this->entidadExiste($tipo, $idEntidad)) {
            return $this->response->setJSON(json_response(false, ['message' => 'Recurso no encontrado', 'data' => []]));
        }
        $model = model(DocumentoModel::class);
        $tipoAlmacen = $this->tipoEntidadParaAlmacenamiento($tipo);
        $lista = $model->listarPorEntidad($tipoAlmacen, $idEntidad);
        foreach ($lista as &$doc) {
            $doc['url_ver'] = site_url('documentos/ver/' . $doc['id']);
            $doc['es_imagen'] = $doc['mime_type'] && in_array($doc['mime_type'], self::MIME_IMAGEN, true);
        }
        return $this->response->setJSON(json_response(true, ['data' => $lista]));
    }

    /**
     * POST documentos/subir/transportista/:id o documentos/subir/equipo/:id
     * Campo del formulario: 'archivo'
     */
    public function subir(string $tipo = '', int $idEntidad = 0)
    {
        if (! in_array($tipo, self::TIPOS_PERMITIDOS, true) || $idEntidad <= 0) {
            return $this->response->setJSON(json_response(false, ['message' => 'Parámetros inválidos']));
        }
        if (! $this->entidadExiste($tipo, $idEntidad)) {
            return $this->response->setJSON(json_response(false, ['message' => 'Recurso no encontrado']));
        }
        $file = $this->request->getFile('archivo');
        if (! $file || ! $file->isValid()) {
            return $this->response->setJSON(json_response(false, ['message' => 'No se envió ningún archivo o el archivo no es válido.']));
        }
        $ext = strtolower($file->getClientExtension());
        if (! in_array($ext, self::EXTENSIONES_PERMITIDAS, true)) {
            return $this->response->setJSON(json_response(false, ['message' => 'Tipo de archivo no permitido. Use: ' . implode(', ', self::EXTENSIONES_PERMITIDAS)]));
        }
        if ($file->getSize() > self::MAX_SIZE) {
            return $this->response->setJSON(json_response(false, ['message' => 'El archivo supera el tamaño máximo (10 MB).']));
        }
        $tipoAlmacen = $this->tipoEntidadParaAlmacenamiento($tipo);
        $dirBase = WRITEPATH . 'uploads/documentos/' . $tipoAlmacen . '/' . $idEntidad . '/';
        if (! is_dir($dirBase)) {
            mkdir($dirBase, 0755, true);
        }
        $nombreArchivo = uniqid('doc_', true) . '.' . $ext;
        $nombreOriginal = $file->getClientName();
        if (! $file->move($dirBase, $nombreArchivo)) {
            return $this->response->setJSON(json_response(false, ['message' => 'Error al guardar el archivo.']));
        }
        $model = model(DocumentoModel::class);
        $model->insert([
            'tipo_entidad'   => $tipoAlmacen,
            'id_entidad'    => $idEntidad,
            'nombre_original' => $nombreOriginal,
            'nombre_archivo' => $nombreArchivo,
            'extension'     => $ext,
            'mime_type'     => $file->getClientMimeType(),
            'tamano'        => $file->getSize(),
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
        return $this->response->setJSON(json_response(true, ['message' => 'Documento subido correctamente.']));
    }

    /**
     * GET documentos/ver/:id — sirve el archivo para vista previa (inline).
     */
    public function ver(int $id = 0)
    {
        if ($id <= 0) {
            return $this->response->setStatusCode(404);
        }
        $model = model(DocumentoModel::class);
        $doc = $model->getById($id);
        if (! $doc) {
            return $this->response->setStatusCode(404);
        }
        $tipo = $doc['tipo_entidad'] ?? '';
        $idEntidad = (int) ($doc['id_entidad'] ?? 0);
        if (! $this->entidadExiste($tipo, $idEntidad)) {
            return $this->response->setStatusCode(404);
        }
        $path = WRITEPATH . 'uploads/documentos/' . $doc['tipo_entidad'] . '/' . $doc['id_entidad'] . '/' . $doc['nombre_archivo'];
        if (! is_file($path)) {
            return $this->response->setStatusCode(404);
        }
        $mime = $doc['mime_type'] ?: 'application/octet-stream';
        $this->response->setHeader('Content-Type', $mime);
        $this->response->setHeader('Content-Disposition', 'inline; filename="' . str_replace('"', '%22', $doc['nombre_original']) . '"');
        $this->response->setHeader('Content-Length', (string) filesize($path));
        return $this->response->setBody(file_get_contents($path));
    }

    /**
     * POST documentos/eliminar/:id
     */
    public function eliminar(int $id = 0)
    {
        if ($id <= 0) {
            return $this->response->setJSON(json_response(false, ['message' => 'ID inválido']));
        }
        $model = model(DocumentoModel::class);
        $doc = $model->getById($id);
        if (! $doc) {
            return $this->response->setJSON(json_response(false, ['message' => 'Documento no encontrado']));
        }
        $tipo = $doc['tipo_entidad'] ?? '';
        $idEntidad = (int) ($doc['id_entidad'] ?? 0);
        if (! $this->entidadExiste($tipo, $idEntidad)) {
            return $this->response->setJSON(json_response(false, ['message' => 'Recurso no encontrado']));
        }
        $path = WRITEPATH . 'uploads/documentos/' . $doc['tipo_entidad'] . '/' . $doc['id_entidad'] . '/' . $doc['nombre_archivo'];
        if (is_file($path)) {
            @unlink($path);
        }
        $model->delete($id);
        return $this->response->setJSON(json_response(true, ['message' => 'Documento eliminado']));
    }
}
