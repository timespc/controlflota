<?php

namespace Config;

/**
 * Configuración de qué secciones/campos se muestran en el módulo Equipos.
 * Cambiá a false lo que no quieras ver.
 */
class EquiposVista extends \CodeIgniter\Config\BaseConfig
{
    /** Información general: fecha alta, modo carga */
    public bool $mostrar_info_general = true;

    /** Sección de vehículos: 3 cards (Tractor, Semi Delantera, Semi Trasero) con taras/PBT */
    public bool $mostrar_seccion_vehiculos = true;

    /** Totales: tara total, peso máximo */
    public bool $mostrar_totales = true;

    /** Capacidades de cisternas C1–C10 y capacidad total */
    public bool $mostrar_cisternas = true;

    /** Cubiertas del tractor y de la unidad, ejes, cotas (vista original unidades) */
    public bool $mostrar_cubiertas_cotas = true;

    /** Observaciones / comentarios */
    public bool $mostrar_observaciones = true;
}
