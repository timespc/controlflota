<?php

namespace Tests\Support\Mocks;

/**
 * Mock de BanderasModel que lanza excepción en el método indicado (para cubrir catch en CrudBaseController).
 */
class BanderasModelThrowingMock
{
    private string $throwMethod;

    public function __construct(string $throwMethod)
    {
        $this->throwMethod = $throwMethod;
    }

    public function listarTodos(): array
    {
        if ($this->throwMethod === 'listarTodos') {
            throw new \Exception('error listar');
        }
        return [];
    }

    public function obtenerPorId($id)
    {
        if ($this->throwMethod === 'obtenerPorId') {
            throw new \Exception('error obtener');
        }
        return null;
    }

    public function guardarBandera(array $data): bool
    {
        if ($this->throwMethod === 'guardarBandera') {
            throw new \Exception('error guardar');
        }
        return true;
    }

    public function eliminarBandera($id): bool
    {
        if ($this->throwMethod === 'eliminarBandera') {
            throw new \Exception('error eliminar');
        }
        return true;
    }

    public function obtenerTotal(): int
    {
        if ($this->throwMethod === 'obtenerTotal') {
            throw new \Exception('error total');
        }
        return 0;
    }

    public function getInsertID(): int
    {
        return 1;
    }
}
