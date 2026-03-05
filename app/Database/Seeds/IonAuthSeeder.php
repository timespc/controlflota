<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Grupos y usuario admin inicial para Ion Auth (Montajes Campana).
 */
class IonAuthSeeder extends Seeder
{
    public function run()
    {
        $config = config('IonAuth');
        $this->db->initialize();
        $tables = $config->tables;

        $groups = [
            ['id' => 1, 'name' => 'admin', 'description' => 'Administrador'],
            ['id' => 2, 'name' => 'members', 'description' => 'Usuario general (legacy)'],
            ['id' => 3, 'name' => 'calibrador', 'description' => 'Calibrador'],
        ];
        $this->db->table($tables['groups'])->insertBatch($groups);

        $hash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 10]);
        $users = [
            [
                'ip_address'  => '127.0.0.1',
                'username'    => 'admin',
                'password'    => $hash,
                'email'       => 'admin@admin.com',
                'activation_selector' => null,
                'activation_code' => null,
                'forgotten_password_selector' => null,
                'forgotten_password_code' => null,
                'forgotten_password_time' => null,
                'remember_selector' => null,
                'remember_code' => null,
                'created_on'  => time(),
                'last_login'  => time(),
                'active'     => 1,
                'first_name' => 'Admin',
                'last_name'  => 'Sistema',
                'company'    => null,
                'phone'      => null,
            ],
        ];
        $this->db->table($tables['users'])->insertBatch($users);

        $usersGroups = [
            ['user_id' => 1, 'group_id' => 1],
            ['user_id' => 1, 'group_id' => 2],
            ['user_id' => 1, 'group_id' => 3],
        ];
        $this->db->table($tables['users_groups'])->insertBatch($usersGroups);
    }
}
