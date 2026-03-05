<?php

namespace Tests\Support\Mocks;

/**
 * Mock de UserGroupModel para tests sin BD.
 * getRolesPorUserId devuelve array vacío para que getRoles() no toque BD.
 */
class UserGroupModelMock
{
    public function getRolesPorUserId(int $user_id): array
    {
        return [];
    }
}
