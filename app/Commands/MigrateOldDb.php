<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Migra datos desde la base de datos del sistema viejo (calibraciones)
 * a la base del sistema actual (montajes_campana).
 *
 * Requiere que las migraciones del sistema actual estén ejecutadas
 * y que exista la conexión 'old' en Config\Database apuntando a la BD vieja.
 */
class MigrateOldDb extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'migrate:olddb';
    protected $description = 'Migra datos desde la BD del sistema viejo (calibraciones) al sistema actual.';
    protected $usage       = 'migrate:olddb [options]';

    /** @var array<string, string> */
    protected $arguments = [];

    /** @var array<string, string> */
    protected $options = [
        '--dry-run'   => 'Solo mostrar qué se haría, sin escribir en la BD nueva.',
        '--truncate'  => 'Vaciar tablas destino antes de insertar (respeta orden por FKs).',
        '--only'      => 'Solo ejecutar pasos indicados (ej. --only=transportistas,calibraciones,calibracion_detalle,calibracion_multiflecha).',
    ];

    /** Conexión a la BD vieja */
    private ?\CodeIgniter\Database\BaseConnection $oldDb = null;

    /** Conexión a la BD nueva (default) */
    private ?\CodeIgniter\Database\BaseConnection $newDb = null;

    /**
     * Inyectar conexiones (para uso desde MigrateFullTwoBasesToOne).
     * Si no se llama, run() conecta normalmente.
     */
    public function setConnections(?\CodeIgniter\Database\BaseConnection $old = null, ?\CodeIgniter\Database\BaseConnection $new = null): void
    {
        if ($old !== null) {
            $this->oldDb = $old;
        }
        if ($new !== null) {
            $this->newDb = $new;
        }
    }

    /** Si es simulación (dry-run) */
    private bool $dryRun = false;

    /** Si se debe truncar antes */
    private bool $truncate = false;

    /** Mapa patente -> id_equipo después de migrar unidades */
    private array $patenteToIdUnidad = [];

    /** Mapa Pcia (viejo) -> provincia_id (nuevo) para Argentina */
    private array $pciaToProvinciaId = [];

    /** Mapa IdNacion (viejo) -> pais_id (nuevo) */
    private array $nacionToPaisId = [];

    /**
     * Asignar mapa patente -> id_equipo para migrateCalibraciones (p. ej. construido desde equipos en montajes).
     */
    public function setPatenteToIdUnidad(array $map): void
    {
        $this->patenteToIdUnidad = $map;
    }

    /** Para invocación programática: no truncar al llamar migrateTransportistas/migrateCalibraciones. */
    public function setTruncate(bool $truncate): void
    {
        $this->truncate = $truncate;
    }

    /** Para invocación programática. */
    public function setDryRun(bool $dryRun): void
    {
        $this->dryRun = $dryRun;
    }

    public function run(array $params): int
    {
        $this->dryRun   = (bool) CLI::getOption('dry-run');
        $this->truncate = (bool) CLI::getOption('truncate');
        $onlyOpt = CLI::getOption('only');
        $onlySteps = $onlyOpt !== null && $onlyOpt !== ''
            ? array_map('trim', explode(',', (string) $onlyOpt))
            : null;

        if ($this->dryRun) {
            CLI::write('Modo dry-run: no se escribirá en la BD nueva.', 'yellow');
        }

        try {
            if ($this->oldDb === null) {
                $this->oldDb = \Config\Database::connect('old');
            }
            if ($this->newDb === null) {
                $this->newDb = \Config\Database::connect();
            }
        } catch (\Throwable $e) {
            CLI::error('Error conectando a las bases de datos: ' . $e->getMessage());
            return 1;
        }

        $steps = [
            'banderas'     => [$this, 'migrateBanderas'],
            'calibradores' => [$this, 'migrateCalibradores'],
            'cubiertas'    => [$this, 'migrateCubiertas'],
            'marcas'       => [$this, 'migrateMarcas'],
            'naciones'     => [$this, 'migrateNaciones'],
            'transportistas' => [$this, 'migrateTransportistas'],
            'unidades'     => [$this, 'migrateUnidades'],
            'calibraciones' => [$this, 'migrateCalibraciones'],
            'calibracion_detalle' => [$this, 'migrateCalibracionDetalle'],
            'calibracion_multiflecha' => [$this, 'migrateCalibracionMultiflecha'],
        ];

        foreach ($steps as $name => $callable) {
            if ($onlySteps !== null && ! in_array($name, $onlySteps, true)) {
                continue;
            }
            CLI::write("--- {$name} ---", 'cyan');
            try {
                $callable();
            } catch (\Throwable $e) {
                CLI::error("Error en {$name}: " . $e->getMessage());
                CLI::write($e->getTraceAsString(), 'light_gray');
                return 1;
            }
        }

        CLI::write('Migración finalizada.', 'green');
        return 0;
    }

    private function migrateBanderas(): void
    {
        if ($this->truncate && ! $this->dryRun) {
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 0');
            $this->newDb->table('banderas')->truncate();
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 1');
        }

        $rows = $this->oldDb->table('banderas')->get()->getResultArray();
        $batch = [];
        foreach ($rows as $r) {
            $batch[] = [
                'id_bandera' => (int) $r['IdBandera'],
                'bandera'    => $r['Bandera'],
                'created_at' => $this->ts($r['UltActualiz'] ?? null),
                'updated_at' => $this->ts($r['UltActualiz'] ?? null),
            ];
        }
        $this->insertBatch('banderas', $batch, 'id_bandera');
        CLI::write('  ' . count($batch) . ' banderas.', 'green');
    }

    private function migrateCalibradores(): void
    {
        if ($this->truncate && ! $this->dryRun) {
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 0');
            $this->newDb->table('calibradores')->truncate();
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 1');
        }

        $rows = $this->oldDb->table('calibradores')->get()->getResultArray();
        $batch = [];
        foreach ($rows as $r) {
            $batch[] = [
                'id_calibrador' => (int) $r['IdCalibrador'],
                'calibrador'    => $r['Calibrador'],
                'created_at'    => $this->ts($r['UltActualiz'] ?? null),
                'updated_at'    => $this->ts($r['UltActualiz'] ?? null),
            ];
        }
        $this->insertBatch('calibradores', $batch, 'id_calibrador');
        CLI::write('  ' . count($batch) . ' calibradores.', 'green');
    }

    private function migrateCubiertas(): void
    {
        if ($this->truncate && ! $this->dryRun) {
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 0');
            $this->newDb->table('cubiertas')->truncate();
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 1');
        }

        $rows = $this->oldDb->table('cubiertas')->get()->getResultArray();
        $batch = [];
        foreach ($rows as $r) {
            $batch[] = [
                'id_cubierta' => (int) $r['IdCubierta'],
                'medida'      => $r['Medida'],
                'created_at'  => $this->ts($r['UltActualiz'] ?? null),
                'updated_at'  => $this->ts($r['UltActualiz'] ?? null),
            ];
        }
        $this->insertBatch('cubiertas', $batch, 'id_cubierta');
        CLI::write('  ' . count($batch) . ' cubiertas.', 'green');
    }

    private function migrateMarcas(): void
    {
        if ($this->truncate && ! $this->dryRun) {
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 0');
            $this->newDb->table('marcas')->truncate();
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 1');
        }

        $rows = $this->oldDb->table('marcas')->get()->getResultArray();
        $batch = [];
        foreach ($rows as $r) {
            $batch[] = [
                'id_marca'   => (int) $r['IdMarca'],
                'marca'     => $r['Marca'] ?? '',
                'created_at' => $this->ts($r['UltActualiz'] ?? null),
                'updated_at' => $this->ts($r['UltActualiz'] ?? null),
            ];
        }
        $this->insertBatch('marcas', $batch, 'id_marca');
        CLI::write('  ' . count($batch) . ' marcas.', 'green');
    }

    private function migrateNaciones(): void
    {
        if ($this->truncate && ! $this->dryRun) {
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 0');
            $this->newDb->table('naciones')->truncate();
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 1');
        }

        $rows = $this->oldDb->table('nacion')->get()->getResultArray();
        $batch = [];
        foreach ($rows as $r) {
            $idNacion = (int) $r['IdNac'];
            $batch[] = [
                'id_nacion'  => $idNacion,
                'nacion'     => $r['Nacion'],
                'created_at' => $this->ts($r['UltActualiz'] ?? null),
                'updated_at' => $this->ts($r['UltActualiz'] ?? null),
            ];
        }
        $this->insertBatch('naciones', $batch, 'id_nacion');
        CLI::write('  ' . count($batch) . ' naciones.', 'green');

        // Construir mapa IdNacion -> pais_id (nuevo) por nombre de país
        if (! $this->dryRun) {
            $paises = $this->newDb->table('paises')->get()->getResultArray();
            foreach ($rows as $r) {
                $idNac = (int) $r['IdNac'];
                $nombre = $this->normalizePaisNombre($r['Nacion']);
                foreach ($paises as $p) {
                    if ($this->normalizePaisNombre($p['nombre']) === $nombre) {
                        $this->nacionToPaisId[$idNac] = (int) $p['id'];
                        break;
                    }
                }
            }
        }
    }

    public function migrateTransportistas(): void
    {
        if ($this->truncate && ! $this->dryRun) {
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 0');
            $this->newDb->table('transportistas')->truncate();
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 1');
        }

        // Si se ejecuta --only=transportistas, construir nacionToPaisId desde old nacion y new paises
        if (empty($this->nacionToPaisId) && ! $this->dryRun) {
            $rows = $this->oldDb->table('nacion')->get()->getResultArray();
            $paises = $this->newDb->table('paises')->get()->getResultArray();
            foreach ($rows as $r) {
                $idNacion = (int) $r['IdNac'];
                $nombre   = $this->normalizePaisNombre($r['Nacion']);
                foreach ($paises as $p) {
                    if ($this->normalizePaisNombre($p['nombre']) === $nombre) {
                        $this->nacionToPaisId[$idNacion] = (int) $p['id'];
                        break;
                    }
                }
            }
        }

        // Mapa Pcia (viejo) -> provincia_id (nuevo): cargar pais_provincias de Argentina
        if (empty($this->pciaToProvinciaId) && ! $this->dryRun) {
            $argentina = $this->newDb->table('paises')->where('nombre', 'Argentina')->get()->getRowArray();
            if ($argentina) {
                $provincias = $this->newDb->table('paises_provincias')
                    ->where('pais_id', $argentina['id'])
                    ->get()->getResultArray();
                $map = [
                    'C.A.B.A.' => 'Buenos Aires',
                    'CORDOBA'  => 'Córdoba',
                    'ENTRE RIOS' => 'Entre Ríos',
                    'LA PAMPA' => 'La Pampa',
                    'LA RIOJA' => 'La Rioja',
                    'MISIONES' => 'Misiones',
                    'NEUQUEN'  => 'Neuquén',
                    'RIO NEGRO' => 'Río Negro',
                    'SANTIAGO DEL ESTERO' => 'Santiago del Estero',
                    'TIERRA DEL FUEGO' => 'Tierra del Fuego',
                    'TUCUMAN'  => 'Tucumán',
                ];
                foreach ($provincias as $pp) {
                    $key = mb_strtoupper(trim($pp['nombre']));
                    $this->pciaToProvinciaId[$key] = (int) $pp['id'];
                }
                foreach ($map as $old => $new) {
                    foreach ($provincias as $pp) {
                        if (mb_strtoupper($pp['nombre']) === mb_strtoupper($new)) {
                            $this->pciaToProvinciaId[mb_strtoupper($old)] = (int) $pp['id'];
                            break;
                        }
                    }
                }
            }
        }

        $rows = $this->oldDb->table('transportistas')->get()->getResultArray();
        $nacionRows = $this->oldDb->table('nacion')->get()->getResultArray();
        $nacionById = [];
        foreach ($nacionRows as $n) {
            $nacionById[(int) $n['IdNac']] = $n['Nacion'];
        }

        $batch = [];
        foreach ($rows as $r) {
            $idNacion = isset($r['IdNacion']) ? (int) $r['IdNacion'] : null;
            $pcia = isset($r['Pcia']) ? trim((string) $r['Pcia']) : null;
            $provinciaId = null;
            if ($pcia !== null && $pcia !== '') {
                $key = mb_strtoupper($pcia);
                $provinciaId = $this->pciaToProvinciaId[$key] ?? null;
            }
            $paisId = $idNacion !== null ? ($this->nacionToPaisId[$idNacion] ?? null) : null;
            $nombreNacion = $idNacion !== null ? ($nacionById[$idNacion] ?? null) : null;

            $batch[] = [
                'id_tta'        => (int) $r['IdTta'],
                'transportista' => $r['Transportista'],
                'direccion'    => $r['Domicilio'] ?? null,
                'localidad'    => $r['Localidad'] ?? null,
                'codigo_postal' => $r['CodPostal'] ?? null,
                'provincia'    => $pcia,
                'nacion'       => $nombreNacion,
                'pais_id'      => $paisId,
                'provincia_id' => $provinciaId,
                'mail_contacto' => $r['Mail'] ?? null,
                'telefono'     => $r['Telefono'] ?? null,
                'comentarios'  => $r['Observaciones'] ?? null,
                'created_at'   => $this->ts($r['UltActualiz'] ?? null),
                'updated_at'   => $this->ts($r['UltActualiz'] ?? null),
            ];
        }
        $this->insertBatch('transportistas', $batch, 'id_tta');
        CLI::write('  ' . count($batch) . ' transportistas.', 'green');
    }

    private function migrateUnidades(): void
    {
        if ($this->truncate && ! $this->dryRun) {
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 0');
            $this->newDb->table('calibracion_detalle')->truncate();
            $this->newDb->table('calibraciones')->truncate();
            $this->newDb->table('equipos')->truncate();
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 1');
        }

        $tipoMap = [
            1 => 'ACOPLADO',
            2 => 'CHASIS',
            3 => 'SEMI', // viejo SEMIRREMOLQUE -> nuevo SEMI
        ];

        $rows = $this->oldDb->table('unidades')->get()->getResultArray();

        // Detectar bitrenes: mismo IdTta + mismo PatenteTractor + mismo día (UltActualiz) con 2+ Patente distintas
        $groups = [];
        foreach ($rows as $r) {
            $tractor = trim((string) ($r['PatenteTractor'] ?? ''));
            $patente = trim((string) $r['Patente']);
            if ($tractor === '' || $patente === '') {
                continue;
            }
            $fecha = isset($r['UltActualiz']) ? substr((string) $r['UltActualiz'], 0, 10) : '';
            $key   = (int) $r['IdTta'] . '|' . strtoupper(str_replace(' ', '', $tractor)) . '|' . $fecha;
            if (! isset($groups[$key])) {
                $groups[$key] = ['patentes' => [], 'rows' => []];
            }
            if (! in_array($patente, $groups[$key]['patentes'], true)) {
                $groups[$key]['patentes'][] = $patente;
                $groups[$key]['rows'][$patente] = $r;
            }
        }
        $bitrenMain = []; // patente principal -> [patente_semi_trasero, ...] (solo primera para el campo; todas para mapear)
        $bitrenSecondaryToMain = []; // patente secundaria -> patente principal (para patenteToIdUnidad)
        foreach ($groups as $key => $g) {
            if (count($g['patentes']) < 2) {
                continue;
            }
            $main = $g['patentes'][0];
            $bitrenMain[$main] = $g['patentes'];
            for ($i = 1; $i < count($g['patentes']); $i++) {
                $bitrenSecondaryToMain[$g['patentes'][$i]] = $main;
            }
        }

        $batch = [];
        foreach ($rows as $r) {
            $patente = trim((string) $r['Patente']);
            if (isset($bitrenSecondaryToMain[$patente])) {
                continue;
            }
            $idTipo = isset($r['IdTipoUnidad']) ? (int) $r['IdTipoUnidad'] : 1;
            $tipoUnidad = $tipoMap[$idTipo] ?? 'ACOPLADO';
            $bitren = 'NO';
            $patenteSemiTrasero = null;
            if (isset($bitrenMain[$patente])) {
                $bitren = 'SI';
                $patenteSemiTrasero = $bitrenMain[$patente][1] ?? null;
            }

            $batch[] = [
                'patente_semi_delantero' => $patente,
                'id_tta'              => (int) $r['IdTta'],
                'tipo_unidad'         => $tipoUnidad,
                'patente_tractor'     => $r['PatenteTractor'] ?? null,
                'bitren'              => $bitren,
                'patente_semi_trasero' => $patenteSemiTrasero,
                'id_bandera'          => isset($r['IdBandera']) ? (int) $r['IdBandera'] : null,
                'id_marca'            => isset($r['IdMarca']) ? (int) $r['IdMarca'] : null,
                'semi_delantero_anio_modelo' => isset($r['AnioFab']) ? (int) $r['AnioFab'] : null,
                'semi_delantero_tara'  => isset($r['Tara']) ? (float) $r['Tara'] : null,
                'cubierta_tractor_eje1' => isset($r['CubEje1Tractor']) ? (int) $r['CubEje1Tractor'] : null,
                'cubierta_tractor_eje2' => isset($r['CubEje2Tractor']) ? (int) $r['CubEje2Tractor'] : null,
                'cubierta_tractor_eje3' => isset($r['CubEje3Tractor']) ? (int) $r['CubEje3Tractor'] : null,
                'ejes_tractor'        => isset($r['EjesTractor']) ? (int) $r['EjesTractor'] : null,
                'cubierta_semi_delantero_eje1' => isset($r['CubEje1']) ? (int) $r['CubEje1'] : null,
                'cubierta_semi_delantero_eje2' => isset($r['CubEje2']) ? (int) $r['CubEje2'] : null,
                'cubierta_semi_delantero_eje3' => isset($r['CubEje3']) ? (int) $r['CubEje3'] : null,
                'ejes_semi_delantero'  => isset($r['Ejes']) ? (int) $r['Ejes'] : null,
                'cota_delantero'      => isset($r['Cota_Del']) ? (float) $r['Cota_Del'] : null,
                'cota_trasero'        => isset($r['Cota_Tras']) ? (float) $r['Cota_Tras'] : null,
                'observaciones'       => $r['ComenUnidad'] ?? null,
                'created_at'          => $this->ts($r['UltActualiz'] ?? null),
                'updated_at'          => $this->ts($r['UltActualiz'] ?? null),
            ];
        }

        if ($this->dryRun) {
            CLI::write('  ' . count($batch) . ' unidades (' . count($bitrenMain) . ' bitrenes fusionados) (dry-run).', 'green');
            return;
        }

        foreach ($batch as $row) {
            $this->newDb->table('equipos')->insert($row);
            $idUnidad = (int) $this->newDb->insertID();
            $this->patenteToIdUnidad[$row['patente_semi_delantero']] = $idUnidad;
            if (! empty($row['patente_semi_trasero'])) {
                $this->patenteToIdUnidad[$row['patente_semi_trasero']] = $idUnidad;
            }
        }
        foreach ($bitrenSecondaryToMain as $sec => $main) {
            if (isset($this->patenteToIdUnidad[$main]) && ! isset($this->patenteToIdUnidad[$sec])) {
                $this->patenteToIdUnidad[$sec] = $this->patenteToIdUnidad[$main];
            }
        }
        CLI::write('  ' . count($batch) . ' unidades (' . count($bitrenMain) . ' bitrenes fusionados).', 'green');
    }

    public function migrateCalibraciones(): void
    {
        if ($this->truncate && ! $this->dryRun) {
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 0');
            $this->newDb->table('calibracion_detalle')->truncate();
            $this->newDb->table('calibraciones')->truncate();
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 1');
        }

        $rows = $this->oldDb->table('calib')->get()->getResultArray();
        if ($this->dryRun) {
            CLI::write('  ' . count($rows) . ' calibraciones (dry-run).', 'green');
            return;
        }
        $batch = [];
        foreach ($rows as $r) {
            $patente = $r['Patente'];
            $idEquipo = $this->patenteToIdUnidad[$patente] ?? null;
            if ($idEquipo === null && ! $this->dryRun) {
                $idEquipo = $this->newDb->table('equipos')->where('patente_semi_delantero', $patente)->get()->getRowArray()['id_equipo'] ?? null;
            }

            $batch[] = [
                'id_calibracion' => (int) $r['CodCalib'],
                'patente'       => $patente,
                'id_equipo'     => $idEquipo,
                'fecha_calib'   => $r['FecCalib'],
                'vto_calib'     => $r['VtoCalib'],
                'id_calibrador' => (int) $r['IdCalibrador'],
                'temp_agua'     => isset($r['TempAgua']) ? (float) $r['TempAgua'] : null,
                'valvulas'      => isset($r['EstadoValvulas']) ? (string) $r['EstadoValvulas'] : null,
                'observaciones' => $r['ComenCalib'] ?? null,
                'multi_flecha'  => ! empty($r['Multiflecha']) ? 'SI' : 'NO',
                'n_regla'       => $r['NroRegla'] ?? null,
                'created_at'    => $this->ts($r['UltActualiz'] ?? null),
                'updated_at'    => $this->ts($r['UltActualiz'] ?? null),
            ];
        }
        $this->insertBatch('calibraciones', $batch, 'id_calibracion');
        CLI::write('  ' . count($batch) . ' calibraciones.', 'green');
    }

    public function migrateCalibracionDetalle(): void
    {
        if ($this->truncate && ! $this->dryRun) {
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 0');
            $this->newDb->table('calibracion_multiflecha')->truncate();
            $this->newDb->table('calibracion_detalle')->truncate();
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 1');
        }

        $cisternas = $this->oldDb->table('cisternas')->get()->getResultArray();
        $batch = [];
        foreach ($cisternas as $r) {
            $batch[] = [
                'id_calibracion'   => (int) $r['CodCalib'],
                'numero_linea'     => (int) $r['Cisterna'],
                'mflec'            => 'NO',
                'capacidad'        => (float) $r['Capacidad'],
                'enrase'           => isset($r['Enrase']) ? (float) $r['Enrase'] : 0,
                'referen'         => isset($r['Ref']) ? (float) $r['Ref'] : 0,
                'vacio_calc'       => isset($r['Vacio']) ? (float) $r['Vacio'] : null,
                'vacio_lts'        => isset($r['VacioLts']) ? (float) $r['VacioLts'] : null,
                'precinto_campana' => $r['Prec_Campana'] ?? null,
                'precinto_soporte' => $r['Prec_Soporte'] ?? null,
                'precinto_hombre'  => $r['Prec_Hombre'] ?? null,
                'created_at'       => $this->ts($r['UltActualiz'] ?? null),
                'updated_at'       => $this->ts($r['UltActualiz'] ?? null),
            ];
        }

        $this->insertBatch('calibracion_detalle', $batch);
        CLI::write('  ' . count($batch) . ' líneas de detalle (cisternas).', 'green');
    }

    /**
     * Migrar cisternas_multi (viejo) → calibracion_multiflecha (nuevo).
     * Detalle de compartimientos multiflecha por cisterna.
     */
    public function migrateCalibracionMultiflecha(): void
    {
        if ($this->truncate && ! $this->dryRun) {
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 0');
            $this->newDb->table('calibracion_multiflecha')->truncate();
            $this->newDb->query('SET FOREIGN_KEY_CHECKS = 1');
        }

        $multi = $this->oldDb->table('cisternas_multi')->get()->getResultArray();
        $batch = [];
        foreach ($multi as $r) {
            $batch[] = [
                'id_calibracion'     => (int) $r['CodCalib'],
                'numero_linea'       => (int) $r['Cisterna'],
                'numero_multiflecha' => (int) $r['NroMultiflecha'],
                'capacidad'          => (float) ($r['Capacidad'] ?? 0),
                'enrase'             => isset($r['Enrase']) ? (float) $r['Enrase'] : 0,
                'referen'            => isset($r['Ref']) ? (float) $r['Ref'] : 0,
                'vacio_calc'         => isset($r['Vacio']) ? (float) $r['Vacio'] : null,
                'vacio_lts'          => isset($r['VacioLts']) ? (float) $r['VacioLts'] : null,
                'precinto_campana'   => $r['Prec_Campana'] ?? null,
                'precinto_soporte'   => $r['Prec_Soporte'] ?? null,
                'precinto_hombre'    => $r['Prec_Hombre'] ?? null,
                'created_at'         => $this->ts($r['UltActualiz'] ?? null),
                'updated_at'         => $this->ts($r['UltActualiz'] ?? null),
            ];
        }

        $this->insertBatch('calibracion_multiflecha', $batch);
        CLI::write('  ' . count($batch) . ' filas multiflecha (compartimientos por cisterna).', 'green');
    }

    /**
     * Inserta en lote usando INSERT IGNORE para no fallar si ya existen
     * filas con la misma PK (re-ejecuciones de la migración).
     */
    private function insertBatch(string $table, array $rows, ?string $primaryKey = null): void
    {
        if ($this->dryRun || empty($rows)) {
            return;
        }
        $cols = array_keys($rows[0]);
        $tableEsc = $this->newDb->protectIdentifiers($table, true, null, false);
        $colList = implode(', ', array_map(function ($c) {
            return $this->newDb->escapeIdentifiers((string) $c);
        }, $cols));
        $valueSets = [];
        foreach ($rows as $row) {
            $vals = [];
            foreach ($cols as $col) {
                $v = $row[$col] ?? null;
                $vals[] = $v === null ? 'NULL' : $this->newDb->escape($v);
            }
            $valueSets[] = '(' . implode(', ', $vals) . ')';
        }
        $batchSize = 100;
        foreach (array_chunk($valueSets, $batchSize) as $chunk) {
            $sql = 'INSERT IGNORE INTO ' . $tableEsc . ' (' . $colList . ') VALUES ' . implode(', ', $chunk);
            $this->newDb->query($sql);
        }
    }

    private function ts($v): ?string
    {
        if ($v === null || $v === '') {
            return null;
        }
        if (is_numeric($v)) {
            return date('Y-m-d H:i:s', (int) $v);
        }
        return (string) $v;
    }

    private function normalizePaisNombre(string $nombre): string
    {
        $n = mb_strtoupper(trim($nombre));
        $map = [
            'ARGENTINA' => 'Argentina',
            'BOLIVIA'   => 'Bolivia',
            'BRASIL'    => 'Brasil',
            'CHILE'     => 'Chile',
            'COLOMBIA'  => 'Colombia',
            'ECUADOR'   => 'Ecuador',
            'PARAGUAY'  => 'Paraguay',
            'PERU'      => 'Perú',
            'URUGUAY'   => 'Uruguay',
            'VENEZUELA' => 'Venezuela',
        ];
        foreach ($map as $key => $val) {
            if ($n === $key) {
                return $val;
            }
        }
        return $nombre;
    }
}

