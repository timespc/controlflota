<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Agrega usuario francurtofrd@gmail.com como admin (Ion Auth).
 * Solo inserta si el email no existe.
 */
class AgregarUsuarioFranSeeder extends Seeder
{
    public function run()
    {
        $config = config('IonAuth');
        $this->db->initialize();
        $tables = $config->tables;

        $email = 'francurtofrd@gmail.com';
        $exists = $this->db->table($tables['users'])->where('email', $email)->countAllResults();
        if ($exists > 0) {
            $this->command ?? null ? $this->command->info('Usuario ' . $email . ' ya existe.') : null;
            return;
        }

        $hash = password_hash(bin2hex(random_bytes(8)), PASSWORD_BCRYPT, ['cost' => 10]);
        $this->db->table($tables['users'])->insert([
            'ip_address'  => '127.0.0.1',
            'username'    => 'francurtofrd',
            'password'    => $hash,
            'email'       => $email,
            'activation_selector' => null,
            'activation_code' => null,
            'forgotten_password_selector' => null,
            'forgotten_password_code' => null,
            'forgotten_password_time' => null,
            'remember_selector' => null,
            'remember_code' => null,
            'created_on'  => time(),
            'last_login'  => null,
            'active'      => 1,
            'first_name'  => 'Fran',
            'last_name'   => null,
            'company'     => null,
            'phone'       => null,
        ]);

        $userId = $this->db->insertID();
        $this->db->table($tables['users_groups'])->insertBatch([
            ['user_id' => $userId, 'group_id' => 1],
            ['user_id' => $userId, 'group_id' => 2],
        ]);
    }
}
