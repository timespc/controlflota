<?php

namespace Tests\Support\Mocks;

/**
 * Mock de CalibracionModel para tests Feature de Calibración (listar, obtener).
 */
class CalibracionModelMock
{
    /** Filas devueltas por listarParaDataTable */
    private array $listarData = [];

    /** Mapa id_calibracion => fila para obtenerPorIdConDetalle / find */
    private array $porId = [];

    public function __construct(array $listarData = [], array $porId = [])
    {
        $this->listarData = $listarData;
        $this->porId      = $porId;
    }

    public function listarParaDataTable(?string $numero = null, ?string $patente = null): array
    {
        return $this->listarData;
    }

    public function obtenerPorIdConDetalle(int $id): ?array
    {
        return $this->porId[$id] ?? null;
    }

    public function find($id)
    {
        return $this->porId[(int) $id] ?? null;
    }
}
