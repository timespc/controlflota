<?php

namespace Tests\Support\Mocks;

/** Mock de CubiertasModel para tests sin BD. */
class CubiertasModelMock
{
    public int $lastInsertId = 1;
    private array $items = [];

    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    public function listarTodos(): array
    {
        return $this->items;
    }

    public function obtenerPorId($id)
    {
        foreach ($this->items as $row) {
            if ((string) ($row['id_cubierta'] ?? '') === (string) $id) {
                return $row;
            }
        }
        return null;
    }

    public function guardarCubierta(array $data): bool
    {
        $id = $data['id_cubierta'] ?? null;
        if ($id && (int) $id > 0) {
            return true;
        }
        $this->lastInsertId = (int) max(array_column($this->items, 'id_cubierta') ?: [0]) + 1;
        $this->items[] = [
            'id_cubierta' => $this->lastInsertId,
            'medida'      => $data['medida'] ?? '',
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];
        return true;
    }

    public function eliminarCubierta($id): bool
    {
        return true;
    }

    public function obtenerTotal(): int
    {
        return count($this->items);
    }

    public function getInsertID(): int
    {
        return $this->lastInsertId;
    }
}
