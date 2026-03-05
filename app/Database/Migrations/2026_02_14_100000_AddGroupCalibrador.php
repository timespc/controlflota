<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Añade el grupo "calibrador" (rol por defecto para usuarios).
 * Admin = solo administradores; Calibrador = rol por defecto.
 */
class AddGroupCalibrador extends Migration
{
    public function up()
    {
        $config = config('IonAuth');
        $tables = $config->tables;

        $row = $this->db->table($tables['groups'])->where('name', 'calibrador')->get()->getRow();
        if ($row) {
            $calibradorId = (int) $row->id;
        } else {
            $this->db->table($tables['groups'])->insert([
                'name'        => 'calibrador',
                'description' => 'Calibrador',
            ]);
            $calibradorId = (int) $this->db->insertID();
        }

        // Usuarios que tienen "members" (id 2) y aún no tienen "calibrador": asignarles calibrador
        $membersId = 2;
        $userIdsWithMembers = $this->db->table($tables['users_groups'])
            ->select('user_id')
            ->where('group_id', $membersId)
            ->get()
            ->getResultArray();
        foreach ($userIdsWithMembers as $row) {
            $userId = (int) $row['user_id'];
            $yaTiene = $this->db->table($tables['users_groups'])
                ->where('user_id', $userId)
                ->where('group_id', $calibradorId)
                ->countAllResults();
            if ($yaTiene === 0) {
                $this->db->table($tables['users_groups'])->insert([
                    'user_id'  => $userId,
                    'group_id' => $calibradorId,
                ]);
            }
        }
    }

    public function down()
    {
        $config = config('IonAuth');
        $tables = $config->tables;
        $row    = $this->db->table($tables['groups'])->where('name', 'calibrador')->get()->getRow();
        if ($row) {
            $this->db->table($tables['users_groups'])->where('group_id', $row->id)->delete();
            $this->db->table($tables['groups'])->where('name', 'calibrador')->delete();
        }
    }
}
