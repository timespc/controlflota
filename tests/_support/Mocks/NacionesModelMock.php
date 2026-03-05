<?php

namespace Tests\Support\Mocks;

/** Mock de NacionesModel para tests sin BD. */
class NacionesModelMock
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
            if ((string) ($row['id_nacion'] ?? '') === (string) $id) {
                return $row;
            }
        }
        return null;
    }

    public function guardarNacion(array $data): bool
    {
        $id = $data['id_nacion'] ?? null;
        if ($id && (int) $id > 0) {
            return true;
        }
        $col = array_column($this->items, 'id_nacion');
        $this->lastInsertId = (int) (count($col) ? max($col) : 0) + 1;
        $this->items[] = [
            'id_nacion' => $this->lastInsertId,
            'nacion' => $data['nacion'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        return true;
    }

    public function eliminarNacion($id): bool
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
