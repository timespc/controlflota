<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Crea las tablas de Ion Auth: groups, users, users_groups, login_attempts.
 * Mismo esquema que Itdelfoscampus (Ion Auth).
 */
class IonAuthTables extends Migration
{
    private array $tables;

    public function __construct()
    {
        $config = config('IonAuth');
        $this->DBGroup = empty($config->databaseGroupName) ? '' : $config->databaseGroupName;
        parent::__construct();
        $this->tables = $config->tables;
    }

    public function up()
    {
        $this->db->disableForeignKeyChecks();

        $this->forge->dropTable($this->tables['groups'], true);
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => '20'],
            'description' => ['type' => 'VARCHAR', 'constraint' => '100'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable($this->tables['groups']);

        $this->forge->dropTable($this->tables['users'], true);
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true, 'null' => false],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => '45'],
            'username' => ['type' => 'VARCHAR', 'constraint' => '100'],
            'password' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'email' => ['type' => 'VARCHAR', 'constraint' => '254', 'unique' => true],
            'activation_selector' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true, 'unique' => true],
            'activation_code' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'forgotten_password_selector' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true, 'unique' => true],
            'forgotten_password_code' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'forgotten_password_time' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'remember_selector' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true, 'unique' => true],
            'remember_code' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'created_on' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'last_login' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'active' => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true, 'null' => true],
            'first_name' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'last_name' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'company' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'phone' => ['type' => 'VARCHAR', 'constraint' => '20', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable($this->tables['users'], false);

        $this->forge->dropTable($this->tables['users_groups'], true);
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'group_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', $this->tables['users'], 'id', 'NO ACTION', 'CASCADE');
        $this->forge->addForeignKey('group_id', $this->tables['groups'], 'id', 'NO ACTION', 'CASCADE');
        $this->forge->createTable($this->tables['users_groups']);

        $this->forge->dropTable($this->tables['login_attempts'], true);
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => '45'],
            'login' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'time' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable($this->tables['login_attempts']);

        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->db->disableForeignKeyChecks();
        $this->forge->dropTable($this->tables['users_groups'], true);
        $this->forge->dropTable($this->tables['users'], true);
        $this->forge->dropTable($this->tables['groups'], true);
        $this->forge->dropTable($this->tables['login_attempts'], true);
        $this->db->enableForeignKeyChecks();
    }
}
