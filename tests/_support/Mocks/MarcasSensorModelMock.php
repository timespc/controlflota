<?php

namespace Tests\Support\Mocks;

/** Mock de MarcasSensorModel para tests sin BD. */
class MarcasSensorModelMock
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

    public function obtenerPorId($id): ?array
    {
        foreach ($this->items as $row) {
            if ((string) ($row['id_marca_sensor'] ?? '') === (string) $id) {
                return $row;
            }
        }
        return null;
    }

    public function guardarMarca(array $data): bool
    {
        $id = $data['id_marca_sensor'] ?? null;
        if ($id && (int) $id > 0) {
            return true;
        }
        $this->lastInsertId = (int) max(array_column($this->items, 'id_marca_sensor') ?: [0]) + 1;
        $this->items[] = [
            'id_marca_sensor' => $this->lastInsertId,
            'marca' => $data['marca'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        return true;
    }

    public function eliminarMarca($id): bool
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
