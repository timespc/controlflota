<?php

namespace Tests\Support\Mocks;

/** Mock de PaisModel para tests sin BD (p. ej. Unidades::index). */
class PaisModelMock
{
    private array $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function obtenerTodos(): array
    {
        return $this->items;
    }
}
