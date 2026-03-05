<?php

namespace Tests\Support\Mocks;

/** Mock de TransportistasModel para tests sin BD (p. ej. Equipos::index). */
class TransportistasModelMock
{
    private array $listarTodos = [];
    private array $listarSoloConEquipos = [];

    public function __construct(array $listarTodos = [], array $listarSoloConEquipos = [])
    {
        $this->listarTodos = $listarTodos;
        $this->listarSoloConEquipos = $listarSoloConEquipos;
    }

    public function listarTodos(): array
    {
        return $this->listarTodos;
    }

    public function listarSoloConEquipos(): array
    {
        return $this->listarSoloConEquipos;
    }
}
