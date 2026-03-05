<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Configuración de calibraciones (dashboard, KPI, reportes).
 * Podés mover estos valores a una tabla/UI de parámetros si querés editarlos desde el sistema.
 */
class Calibraciones extends BaseConfig
{
    /**
     * Meses desde el vencimiento a partir de los cuales una calibración vencida
     * deja de contarse en el dashboard y en el KPI "Equipos vencidos".
     * Ej: 24 = no contar como vencidas las que llevan más de 2 años vencidas.
     * 0 = sin límite (todas las vencidas cuentan).
     */
    public int $mesesVenidaMaxKpi = 24;
}
