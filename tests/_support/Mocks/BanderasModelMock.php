<?php

namespace Tests\Support\Mocks;

/**
 * Mock de BanderasModel para tests sin BD.
 * Devuelve datos fijos en listarTodos, obtenerPorId, obtenerTotal;
 * guardarBandera y eliminarBandera no tocan BD pero simulan éxito.
 */
class BanderasModelMock
{
    /** ID que simula el último insert (para guardar) */
    public int $lastInsertId = 1;

    /** Registros "en memoria" para listar/obtener */
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
            $pk = $row['id_bandera'] ?? $row['id'] ?? null;
            if ((string) $pk === (string) $id) {
                return $row;
            }
        }
        return null;
    }

    public function guardarBandera(array $data): bool
    {
        $id = $data['id_bandera'] ?? null;
        $bandera = $data['bandera'] ?? '';
        if ($id && (int) $id > 0) {
            return true; // simula update
        }
        $this->lastInsertId = (int) max(array_column($this->items, 'id_bandera') ?: [0]) + 1;
        $this->items[] = [
            'id_bandera' => $this->lastInsertId,
            'bandera'    => $bandera,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        return true;
    }

    public function eliminarBandera($id): bool
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
