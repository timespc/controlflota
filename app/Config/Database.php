<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    /**
     * The directory that holds the Migrations
     * and Seeds directories.
     */ 
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

    /**
     * Lets you choose which connection group to
     * use if no other is specified.
     */
    public string $defaultGroup = 'default';

    /**
     * The default database connection.
     *
     * @var array<string, mixed>
     */
    public array $default = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'root',
        'password'     => '',
        'database'     => 'montajes_campana',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => true,
        'charset'      => 'utf8',
        'DBCollat'     => 'utf8_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
    ];

    /**
    * This database connection is used when
    * running PHPUnit database tests.
    *
    * @var array<string, mixed>
    */
    public array $tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'database'    => ':memory:',
        'DBDriver'    => 'SQLite3',
        'DBPrefix'    => 'db_',
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => 'utf8_general_ci',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => false,
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000,
    ];

    /**
     * Conexión a la base de datos del sistema viejo (calibraciones)
     * para el comando de migración migrate:olddb.
     *
     * @var array<string, mixed>
     */
    public array $old = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'root',
        'password'     => '',
        'database'     => 'calibraciones',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => false,
        'charset'      => 'utf8',
        'DBCollat'     => 'utf8_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
    ];

    /**
     * Conexión a la base de datos del sistema camiones (equipos).
     * Usada por el comando equipos:completar-desde-camiones para cruzar datos.
     * Ajustar hostname, username, password y database según el servidor camiones.
     *
     * @var array<string, mixed>
     */
    public array $camiones = [
        'DSN'          => '',
        'hostname'     => 'localhost',
        'username'     => 'root',
        'password'     => '',
        'database'     => 'camiones',
        'DBDriver'     => 'MySQLi',
        'DBPrefix'     => '',
        'pConnect'     => false,
        'DBDebug'      => false,
        'charset'      => 'utf8',
        'DBCollat'     => 'utf8_general_ci',
        'swapPre'      => '',
        'encrypt'      => false,
        'compress'     => false,
        'strictOn'     => false,
        'failover'     => [],
        'port'         => 3306,
        'numberNative' => false,
    ];

    public function __construct()
    {
        $this->default['hostname'] = env('database.default.hostname', 'localhost');
        $this->default['username'] = env('database.default.username', 'root');
        $this->default['password'] = env('database.default.password', '');
        $this->default['database'] = env('database.default.database', 'montajes_campana');
        $this->default['DBDriver'] = env('database.default.DBDriver', 'MySQLi');
        $this->default['port']     = (int) env('database.default.port', 3306);

        $this->old['hostname'] = env('database.old.hostname', 'localhost');
        $this->old['username'] = env('database.old.username', 'root');
        $this->old['password'] = env('database.old.password', '');
        $this->old['database'] = env('database.old.database', 'calibraciones');
        $this->old['DBDriver'] = env('database.old.DBDriver', 'MySQLi');
        $this->old['port']     = (int) env('database.old.port', 3306);

        $this->camiones['hostname'] = env('database.camiones.hostname', 'localhost');
        $this->camiones['username'] = env('database.camiones.username', 'root');
        $this->camiones['password'] = env('database.camiones.password', '');
        $this->camiones['database'] = env('database.camiones.database', 'camiones');
        $this->camiones['DBDriver'] = env('database.camiones.DBDriver', 'MySQLi');
        $this->camiones['port']     = (int) env('database.camiones.port', 3306);

        parent::__construct();
    }
}


