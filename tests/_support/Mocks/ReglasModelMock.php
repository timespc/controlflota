<?php

namespace Tests\Support\Mocks;

/** Mock de ReglasModel para tests sin BD. */
class ReglasModelMock
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
            if ((string) ($row['id_regla'] ?? '') === (string) $id) {
                return $row;
            }
        }
        return null;
    }

    public function guardarRegla(array $data): bool
    {
        $id = $data['id_regla'] ?? null;
        if ($id && (int) $id > 0) {
            return true;
        }
        $this->lastInsertId = (int) max(array_column($this->items, 'id_regla') ?: [0]) + 1;
        $this->items[] = [
            'id_regla'   => $this->lastInsertId,
            'numero_regla' => $data['numero_regla'] ?? '',
            'habilitada' => (int) ($data['habilitada'] ?? 1),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        return true;
    }

    public function eliminarRegla($id): bool
    {
        return true;
    }

    public function obtenerTotal(): int
    {
        return count($this->items);
    }

    /** Para Calibracion::index (regla habilitada para vista). */
    public function obtenerHabilitada(): ?array
    {
        foreach ($this->items as $row) {
            if ((int) ($row['habilitada'] ?? 0) === 1) {
                return $row;
            }
        }
        return $this->items[0] ?? ['numero_regla' => ''];
    }

    public function getInsertID(): int
    {
        return $this->lastInsertId;
    }
}
