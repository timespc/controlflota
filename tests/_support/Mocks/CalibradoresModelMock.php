<?php

namespace Tests\Support\Mocks;

class CalibradoresModelMock
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
            if ((string) ($row['id_calibrador'] ?? '') === (string) $id) {
                return $row;
            }
        }
        return null;
    }

    public function guardarCalibrador(array $data): bool
    {
        $id = $data['id_calibrador'] ?? null;
        if ($id && (int) $id > 0) {
            return true;
        }
        $max = array_column($this->items, 'id_calibrador');
        $this->lastInsertId = (int) ($max ? max($max) : 0) + 1;
        $this->items[] = [
            'id_calibrador' => $this->lastInsertId,
            'calibrador' => $data['calibrador'] ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        return true;
    }

    public function eliminarCalibrador($id): bool
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
