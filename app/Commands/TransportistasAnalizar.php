<?php

declare(strict_types=1);

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * Analiza la tabla transportistas de montajes_campana:
 * - Posibles duplicados (mismo nombre normalizado o nombres muy parecidos).
 * - Registros con información importante faltante (CUIT, tipo, mail, dirección, etc.).
 */
class TransportistasAnalizar extends BaseCommand
{
    protected $group       = 'Transportistas';
    protected $name        = 'transportistas:analizar';
    protected $description = 'Analiza duplicados y datos faltantes en la tabla transportistas.';
    protected $usage       = 'transportistas:analizar [options]';

    /** @var array<string, string> */
    protected $options = [
        '--similares' => 'Incluir parecidos por similitud de nombre (más lento).',
        '--umbral=N'   => 'Porcentaje mínimo de similitud para considerar parecido (default 85).',
    ];

    private \CodeIgniter\Database\BaseConnection $db;

    /** Campos considerados "importantes" para reportar si faltan */
    private const CAMPOS_IMPORTANTES = ['cuit', 'tipo', 'codigo_axion', 'mail_contacto', 'direccion', 'localidad', 'nacion'];

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

    private function similitud(string $a, string $b): int
    {
        similar_text($a, $b, $pct);
        return (int) round($pct);
    }

    public function run(array $params): int
    {
        $listarSimilares = (bool) CLI::getOption('similares');
        $umbral = 85;
        foreach (CLI::getOptions() as $key => $val) {
            if (strpos($key, 'umbral=') === 0) {
                $umbral = (int) substr($key, 7);
                break;
            }
        }
        if ($umbral < 1 || $umbral > 100) {
            $umbral = 85;
        }

        try {
            $this->db = \Config\Database::connect();
        } catch (\Throwable $e) {
            CLI::error('Error conectando a la base de datos: ' . $e->getMessage());
            return 1;
        }

        CLI::write('Cargando transportistas...', 'cyan');
        $rows = $this->db->table('transportistas')
            ->select('id_tta, transportista, cuit, tipo, codigo_axion, direccion, localidad, mail_contacto, nacion')
            ->orderBy('transportista', 'ASC')
            ->get()
            ->getResultArray();
        $total = count($rows);
        CLI::write("  {$total} transportistas.", 'green');
        if ($total === 0) {
            return 0;
        }

        // --- Duplicados por nombre normalizado (exacto) ---
        $porNombreNorm = [];
        foreach ($rows as $r) {
            $nombre = trim((string) ($r['transportista'] ?? ''));
            $norm  = $this->normalizarNombre($nombre);
            if ($norm === '') {
                continue;
            }
            if (! isset($porNombreNorm[$norm])) {
                $porNombreNorm[$norm] = [];
            }
            $porNombreNorm[$norm][] = [
                'id_tta'       => (int) $r['id_tta'],
                'transportista' => $nombre,
                'cuit'         => trim((string) ($r['cuit'] ?? '')),
            ];
        }

        $duplicadosExactos = array_filter($porNombreNorm, static fn ($list) => count($list) > 1);
        CLI::newLine();
        CLI::write('=== POSIBLES DUPLICADOS (mismo nombre normalizado) ===', 'yellow');
        if ($duplicadosExactos === []) {
            CLI::write('No se encontraron duplicados exactos por nombre.', 'green');
        } else {
            foreach ($duplicadosExactos as $nombreNorm => $lista) {
                CLI::write("  \"{$nombreNorm}\":", 'light_gray');
                foreach ($lista as $t) {
                    CLI::write("    id_tta={$t['id_tta']}  cuit=" . ($t['cuit'] ?: '—'), 'white');
                }
                CLI::newLine();
            }
        }

        // --- Parecidos por similitud (opcional) ---
        if ($listarSimilares && $total <= 500) {
            CLI::write('=== POSIBLES DUPLICADOS (nombres parecidos, similitud >= ' . $umbral . '%) ===', 'yellow');
            $nombres = array_column($rows, 'transportista', 'id_tta');
            $reportados = [];
            foreach ($rows as $r) {
                $id   = (int) $r['id_tta'];
                $nombre = trim((string) ($r['transportista'] ?? ''));
                $norm  = $this->normalizarNombre($nombre);
                if ($norm === '') {
                    continue;
                }
                foreach ($nombres as $idOtro => $nombreOtro) {
                    if ($idOtro >= $id) {
                        continue;
                    }
                    $normOtro = $this->normalizarNombre($nombreOtro);
                    if ($normOtro === '' || $norm === $normOtro) {
                        continue;
                    }
                    $pct = $this->similitud($norm, $normOtro);
                    if ($pct >= $umbral) {
                        $key = $id < $idOtro ? "{$id}-{$idOtro}" : "{$idOtro}-{$id}";
                        if (isset($reportados[$key])) {
                            continue;
                        }
                        $reportados[$key] = true;
                        CLI::write("  id_tta={$id} \"{$nombre}\"", 'white');
                        CLI::write("  id_tta={$idOtro} \"{$nombreOtro}\"  → {$pct}% similitud", 'light_gray');
                        CLI::newLine();
                    }
                }
            }
            if ($reportados === []) {
                CLI::write('No se encontraron parecidos por similitud.', 'green');
            }
        } elseif ($listarSimilares && $total > 500) {
            CLI::write('(Omitido parecidos por similitud: muchos registros. Ejecutá con menos datos o subí --umbral.)', 'light_gray');
        }

        // --- Información faltante ---
        CLI::write('=== INFORMACIÓN IMPORTANTE FALTANTE ===', 'yellow');
        CLI::write('Campos considerados importantes: ' . implode(', ', self::CAMPOS_IMPORTANTES), 'light_gray');
        CLI::newLine();

        $sinCuit = [];
        $sinTipo = [];
        $sinCodigoAxion = [];
        $sinMail = [];
        $sinDireccion = [];
        $conVariosFaltantes = [];

        foreach ($rows as $r) {
            $id = (int) $r['id_tta'];
            $nombre = trim((string) ($r['transportista'] ?? ''));
            $cuit   = trim((string) ($r['cuit'] ?? ''));
            $tipo   = trim((string) ($r['tipo'] ?? ''));
            $codAx  = trim((string) ($r['codigo_axion'] ?? ''));
            $mail   = trim((string) ($r['mail_contacto'] ?? ''));
            $dir    = trim((string) ($r['direccion'] ?? ''));
            $loc    = trim((string) ($r['localidad'] ?? ''));
            $nacion = trim((string) ($r['nacion'] ?? ''));

            if ($cuit === '') {
                $sinCuit[] = ['id_tta' => $id, 'nombre' => $nombre];
            }
            if ($tipo === '') {
                $sinTipo[] = ['id_tta' => $id, 'nombre' => $nombre];
            }
            if ($codAx === '') {
                $sinCodigoAxion[] = ['id_tta' => $id, 'nombre' => $nombre];
            }
            if ($mail === '') {
                $sinMail[] = ['id_tta' => $id, 'nombre' => $nombre];
            }
            if ($dir === '') {
                $sinDireccion[] = ['id_tta' => $id, 'nombre' => $nombre];
            }

            $faltantes = 0;
            if ($cuit === '') $faltantes++;
            if ($tipo === '') $faltantes++;
            if ($codAx === '') $faltantes++;
            if ($mail === '') $faltantes++;
            if ($dir === '') $faltantes++;
            if ($loc === '') $faltantes++;
            if ($nacion === '') $faltantes++;
            if ($faltantes >= 4) {
                $conVariosFaltantes[] = ['id_tta' => $id, 'nombre' => $nombre, 'faltantes' => $faltantes];
            }
        }

        $resumen = [
            ['Sin CUIT', $sinCuit],
            ['Sin tipo', $sinTipo],
            ['Sin codigo_axion', $sinCodigoAxion],
            ['Sin mail_contacto', $sinMail],
            ['Sin dirección', $sinDireccion],
        ];
        foreach ($resumen as [$etiqueta, $lista]) {
            $n = count($lista);
            CLI::write("  {$etiqueta}: {$n} registros", $n > 0 ? 'yellow' : 'green');
            if ($n > 0 && $n <= 20) {
                foreach ($lista as $t) {
                    CLI::write("    id_tta={$t['id_tta']}  {$t['nombre']}", 'light_gray');
                }
            } elseif ($n > 20) {
                foreach (array_slice($lista, 0, 10) as $t) {
                    CLI::write("    id_tta={$t['id_tta']}  {$t['nombre']}", 'light_gray');
                }
                CLI::write("    ... y " . ($n - 10) . " más.", 'light_gray');
            }
            CLI::newLine();
        }

        CLI::write('  Con 4 o más campos importantes vacíos:', count($conVariosFaltantes) > 0 ? 'yellow' : 'green');
        if (count($conVariosFaltantes) > 0) {
            foreach (array_slice($conVariosFaltantes, 0, 15) as $t) {
                CLI::write("    id_tta={$t['id_tta']}  {$t['nombre']}  ({$t['faltantes']} campos vacíos)", 'light_gray');
            }
            if (count($conVariosFaltantes) > 15) {
                CLI::write("    ... y " . (count($conVariosFaltantes) - 15) . " más.", 'light_gray');
            }
        }

        CLI::newLine();
        CLI::write('Análisis finalizado.', 'green');
        return 0;
    }
}
