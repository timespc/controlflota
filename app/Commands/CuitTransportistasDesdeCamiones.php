<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Sincroniza el CUIT de los transportistas de montajes-campana con la tabla ttas de la BD camiones.
 * Compara por nombre del transportista (normalizado: trim, mayúsculas, espacios colapsados).
 * Requiere conexión 'camiones' en Config\Database. Nombres de tabla/columnas en app/Config/Camiones.php.
 */
class CuitTransportistasDesdeCamiones extends BaseCommand
{
    protected $group       = 'Transportistas';
    protected $name        = 'transportistas:cuit-desde-camiones';
    protected $description = 'Obtiene CUIT de la BD camiones (tabla ttas) y actualiza transportistas por nombre.';
    protected $usage       = 'transportistas:cuit-desde-camiones [options]';

    /** @var array<string, string> */
    protected $options = [
        '--dry-run'        => 'Solo mostrar qué se actualizaría, sin modificar la BD.',
        '--verbose'       => 'Listar cada transportista y si hubo match o no.',
        '--similares'     => 'Listar transportistas sin match que se parecen a algún tta (umbral y cantidad con --umbral y --max).',
        '--usar-similares' => 'Si no hay match exacto, usar el mejor parecido con similitud >= 90% para actualizar CUIT y email.',
        '--umbral=N'      => 'Porcentaje mínimo de similitud para --similares y --usar-similares (default 50).',
        '--umbral-max=N'  => 'Con --similares: solo listar si similitud <= N (ej. --umbral=30 --umbral-max=60 para rango 30-60%).',
        '--max=N'         => 'Máximo de filas a listar con --similares (default 50).',
    ];

    private \CodeIgniter\Database\BaseConnection $db;
    private \CodeIgniter\Database\BaseConnection $camionesDb;
    private bool $dryRun  = false;
    private bool $verbose = false;
    private bool $listarSimilares = false;
    private bool $usarSimilares = false;
    private int $umbralSimilitud = 50;
    private ?int $umbralMax = null;
    private int $maxSimilares = 50;

    /**
     * Obtiene el valor de una opción CLI. CodeIgniter con --nombre=valor guarda la clave como "nombre=valor";
     * este método acepta tanto getOption('nombre') como esa forma.
     */
    private function parseOptionValue(string $name): mixed
    {
        $val = CLI::getOption($name);
        if ($val !== null && $val !== false && $val !== true) {
            return $val;
        }
        foreach (CLI::getOptions() as $key => $value) {
            if (strpos($key, $name . '=') === 0) {
                return substr($key, strlen($name) + 1);
            }
        }
        return null;
    }

    /**
     * Normaliza el nombre para comparación: trim, mayúsculas, espacios múltiples a uno.
     */
    private function normalizarNombre(?string $s): string
    {
        if ($s === null || $s === '') {
            return '';
        }
        $s = trim($s);
        $s = mb_strtoupper($s, 'UTF-8');
        $s = (string) preg_replace('/\s+/u', ' ', $s);
        return $s;
    }

    public function run(array $params): int
    {
        $this->dryRun   = (bool) CLI::getOption('dry-run');
        $this->verbose  = (bool) CLI::getOption('verbose');
        $this->listarSimilares = (bool) CLI::getOption('similares');
        $this->usarSimilares = (bool) CLI::getOption('usar-similares');
        $umbral = $this->parseOptionValue('umbral');
        $this->umbralSimilitud = is_numeric($umbral) ? (int) $umbral : 50;
        $umbralMax = $this->parseOptionValue('umbral-max');
        $this->umbralMax = ($umbralMax !== null && $umbralMax !== '' && is_numeric($umbralMax)) ? (int) $umbralMax : null;
        $max = $this->parseOptionValue('max');
        $this->maxSimilares = is_numeric($max) ? (int) $max : 50;

        if ($this->dryRun) {
            CLI::write('Modo dry-run: no se actualizará la BD.', 'yellow');
        }

        try {
            $this->db         = \Config\Database::connect();
            $this->camionesDb = \Config\Database::connect('camiones');
        } catch (\Throwable $e) {
            CLI::error('Error conectando a las bases de datos: ' . $e->getMessage());
            CLI::write('Verificá que la conexión "camiones" exista en app/Config/Database.php.', 'light_gray');
            return 1;
        }

        $config   = config('Camiones');
        $tableTtas = $config->tableTtas ?? 'ttas';
        $colNombre = $config->columnTtasNombre ?? 'Transportista';
        $colCuit   = $config->columnTtasCuit ?? 'Cuit';
        $colEmail  = trim((string) ($config->columnTtasEmail ?? ''));
        $selectTtas = "{$colNombre} as nombre_tta, {$colCuit} as cuit_tta";
        if ($colEmail !== '') {
            $selectTtas .= ", {$colEmail} as email_tta";
        }

        if (! $this->camionesDb->tableExists($tableTtas)) {
            CLI::error("La tabla \"{$tableTtas}\" no existe en la BD camiones.");
            CLI::write("Si la tabla tiene otro nombre, configurá tableTtas en app/Config/Camiones.php.", 'light_gray');
            return 1;
        }

        CLI::write("Cargando ttas desde camiones (tabla {$tableTtas}, columnas: {$colNombre}, {$colCuit}" . ($colEmail !== '' ? ", {$colEmail}" : '') . ")...", 'cyan');
        $builderTtas = $this->camionesDb->table($tableTtas)->select($selectTtas);
        $resultTtas  = $builderTtas->get();
        if ($resultTtas === false) {
            $error = $this->camionesDb->error();
            CLI::error('Error al leer ttas en camiones: ' . ($error['message'] ?? 'consulta fallida'));
            CLI::write("Revisá los nombres de columnas en app/Config/Camiones.php.", 'light_gray');
            return 1;
        }
        $rowsTtas = $resultTtas->getResultArray();

        /** @var array<string, array{cuit: string, email: string}> */
        $ttasPorNombre = [];
        foreach ($rowsTtas as $r) {
            $nombre = $this->normalizarNombre($r['nombre_tta'] ?? '');
            if ($nombre === '') {
                continue;
            }
            $cuit = trim((string) ($r['cuit_tta'] ?? ''));
            if ($cuit !== '' && ! isset($ttasPorNombre[$nombre])) {
                $email = $colEmail !== '' ? trim((string) ($r['email_tta'] ?? '')) : '';
                $ttasPorNombre[$nombre] = ['cuit' => $cuit, 'email' => $email];
            }
        }
        CLI::write('  ' . count($ttasPorNombre) . ' ttas con CUIT indexados por nombre.', 'green');

        CLI::write('Cargando transportistas de montajes-campana...', 'cyan');
        $resultTrans = $this->db->table('transportistas')->select('id_tta, transportista, cuit, mail_contacto')->get();
        if ($resultTrans === false) {
            $error = $this->db->error();
            CLI::error('Error al leer transportistas: ' . ($error['message'] ?? 'consulta fallida'));
            return 1;
        }
        $transportistas = $resultTrans->getResultArray();
        CLI::write('  ' . count($transportistas) . ' transportistas.', 'green');

        $actualizados = 0;
        $actualizadosPorSimilar = 0;
        $sinMatch = 0;
        $yaTienenCuit = 0;
        /** @var list<array{id_tta: int, nombre: string, nombre_norm: string}> */
        $sinMatchList = [];
        $nombresTtas = array_keys($ttasPorNombre);

        foreach ($transportistas as $t) {
            $idTta   = (int) $t['id_tta'];
            $nombre  = trim((string) ($t['transportista'] ?? ''));
            $nombreNorm = $this->normalizarNombre($nombre);
            $cuitActual = trim((string) ($t['cuit'] ?? ''));
            $emailTtas = '';

            if ($nombreNorm === '') {
                if ($this->verbose) {
                    CLI::write("  id_tta={$idTta}: sin nombre, omitido.", 'light_gray');
                }
                continue;
            }

            $dataTta = $ttasPorNombre[$nombreNorm] ?? null;
            $cuitTtas = $dataTta ? $dataTta['cuit'] : null;
            if ($dataTta !== null) {
                $emailTtas = $dataTta['email'] ?? '';
            }
            $porSimilar = false;

            if ($cuitTtas === null && $this->usarSimilares) {
                $mejor = $this->mejorParecido($nombreNorm, $nombresTtas, $ttasPorNombre);
                if ($mejor !== null && $mejor['pct'] >= 90) {
                    $cuitTtas = $mejor['cuit'];
                    $emailTtas = $mejor['email'] ?? '';
                    $porSimilar = true;
                }
            }

            if ($cuitTtas === null) {
                $sinMatch++;
                if ($this->listarSimilares) {
                    $sinMatchList[] = ['id_tta' => $idTta, 'nombre' => $nombre, 'nombre_norm' => $nombreNorm];
                }
                if ($this->verbose) {
                    CLI::write("  id_tta={$idTta} \"{$nombre}\": sin match en ttas.", 'yellow');
                }
                continue;
            }

            if ($cuitActual !== '' && $cuitActual === $cuitTtas) {
                $yaTienenCuit++;
                if ($emailTtas !== '' && trim((string) ($t['mail_contacto'] ?? '')) === '' && ! $this->dryRun) {
                    $this->db->table('transportistas')->where('id_tta', $idTta)->update(['mail_contacto' => $emailTtas]);
                }
                if ($this->verbose) {
                    CLI::write("  id_tta={$idTta} \"{$nombre}\": ya tiene CUIT igual.", 'light_gray');
                }
                continue;
            }

            $dataUpdate = ['cuit' => $cuitTtas];
            if ($emailTtas !== '') {
                $dataUpdate['mail_contacto'] = $emailTtas;
            }
            if (! $this->dryRun) {
                $ok = $this->db->table('transportistas')->where('id_tta', $idTta)->update($dataUpdate);
                if ($ok) {
                    $actualizados++;
                    if ($porSimilar) {
                        $actualizadosPorSimilar++;
                    }
                    if ($this->verbose) {
                        $sufijo = $porSimilar ? ' (por parecido)' : '';
                        CLI::write("  id_tta={$idTta} \"{$nombre}\": CUIT actualizado a \"{$cuitTtas}\"{$sufijo}.", 'green');
                    }
                }
            } else {
                $actualizados++;
                if ($porSimilar) {
                    $actualizadosPorSimilar++;
                }
                if ($this->verbose) {
                    $sufijo = $porSimilar ? ' (por parecido)' : '';
                    CLI::write("  id_tta={$idTta} \"{$nombre}\": se actualizaría CUIT a \"{$cuitTtas}\"{$sufijo}.", 'cyan');
                }
            }
        }

        if (! $this->verbose) {
            CLI::write('Actualizados: ' . $actualizados . '.', 'green');
            if ($actualizadosPorSimilar > 0) {
                CLI::write('  (por parecido: ' . $actualizadosPorSimilar . ')', 'light_gray');
            }
            if ($sinMatch > 0) {
                CLI::write('Sin match en ttas: ' . $sinMatch . '.', 'yellow');
            }
            if ($yaTienenCuit > 0) {
                CLI::write('Ya tenían el mismo CUIT: ' . $yaTienenCuit . '.', 'light_gray');
            }
        }

        if ($this->dryRun && $actualizados > 0) {
            CLI::write('Ejecutá sin --dry-run para aplicar los cambios.', 'yellow');
        }

        if ($this->listarSimilares && $sinMatchList !== []) {
            $this->listarParecidos($sinMatchList, $nombresTtas);
        }

        return 0;
    }

    /**
     * Devuelve el tta más parecido por nombre (similar_text) con su CUIT y email, o null si ninguno tiene similitud.
     * Solo aplica si similitud >= 90%.
     * @param array<string, array{cuit: string, email: string}> $ttasPorNombre
     * @return array{cuit: string, email: string, nombre_tta: string, pct: float}|null
     */
    private function mejorParecido(string $nombreNorm, array $nombresTtas, array $ttasPorNombre): ?array
    {
        $mejorPct = 0.0;
        $mejorNombre = '';
        foreach ($nombresTtas as $nombreTta) {
            similar_text($nombreNorm, $nombreTta, $pct);
            if ($pct > $mejorPct) {
                $mejorPct = (float) $pct;
                $mejorNombre = $nombreTta;
            }
        }
        if ($mejorNombre === '') {
            return null;
        }
        $data = $ttasPorNombre[$mejorNombre] ?? null;
        if ($data === null || ($data['cuit'] ?? '') === '') {
            return null;
        }
        return [
            'cuit' => $data['cuit'],
            'email' => $data['email'] ?? '',
            'nombre_tta' => $mejorNombre,
            'pct' => $mejorPct,
        ];
    }

    /**
     * Para cada transportista sin match, encuentra el tta más parecido por similar_text y lista hasta max con % >= umbral.
     * @param list<array{id_tta: int, nombre: string, nombre_norm: string}> $sinMatchList
     * @param list<string> $nombresTtas
     */
    private function listarParecidos(array $sinMatchList, array $nombresTtas): void
    {
        CLI::newLine();
        CLI::write('Similitud (sin match exacto, ordenado por % de parecido):', 'cyan');
        $rangoTexto = $this->umbralMax !== null
            ? 'Entre ' . $this->umbralSimilitud . '% y ' . $this->umbralMax . '%.'
            : 'Mínimo ' . $this->umbralSimilitud . '%.';
        CLI::write($rangoTexto . ' Mostrando hasta ' . $this->maxSimilares . '.', 'light_gray');

        $candidatos = [];
        foreach ($sinMatchList as $item) {
            $mejorPct = 0.0;
            $mejorTta = '';
            foreach ($nombresTtas as $nombreTta) {
                similar_text($item['nombre_norm'], $nombreTta, $pct);
                if ($pct > $mejorPct) {
                    $mejorPct = (float) $pct;
                    $mejorTta = $nombreTta;
                }
            }
            $cumpleMin = $mejorPct >= $this->umbralSimilitud;
            $cumpleMax = $this->umbralMax === null || $mejorPct <= $this->umbralMax;
            if ($mejorTta !== '' && $cumpleMin && $cumpleMax) {
                $candidatos[] = [
                    'id_tta'   => $item['id_tta'],
                    'nombre'   => $item['nombre'],
                    'tta_nom'  => $mejorTta,
                    'similitud' => round($mejorPct, 1),
                ];
            }
        }

        usort($candidatos, static fn ($a, $b) => $b['similitud'] <=> $a['similitud']);
        $candidatos = array_slice($candidatos, 0, $this->maxSimilares);

        if ($candidatos === []) {
            $sugerencia = $this->umbralMax !== null
                ? 'Probá cambiar --umbral o --umbral-max.'
                : 'Probá bajar --umbral (ej. 40).';
            CLI::write('Ninguno en este rango. ' . $sugerencia, 'yellow');
            return;
        }

        $n = count($candidatos);
        $rangoLabel = $this->umbralMax !== null
            ? "entre {$this->umbralSimilitud}% y {$this->umbralMax}%"
            : ">= {$this->umbralSimilitud}%";
        CLI::write("  {$n} con parecido {$rangoLabel}:", 'green');
        CLI::newLine();
        foreach ($candidatos as $c) {
            CLI::write(sprintf(
                '  id_tta=%d  %s  →  "%s"  (%s%%)',
                $c['id_tta'],
                $c['nombre'],
                $c['tta_nom'],
                (string) $c['similitud']
            ), 'light_gray');
        }
    }
}
