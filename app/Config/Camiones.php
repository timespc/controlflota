<?php

namespace Config;

/**
 * Configuración para el cruce con la BD camiones (comando equipos:completar-desde-camiones).
 * Si la tabla de equipos en camiones usa otros nombres de columna, definilos aquí.
 * Clave = nombre que usa el comando; valor = nombre real en la BD camiones.
 */
class Camiones extends \CodeIgniter\Config\BaseConfig
{
    /** Nombre de la tabla de equipos en la BD camiones */
    public string $tableEquipos = 'equipos';

    /**
     * Mapeo: nombre interno (usado por el comando) => nombre de columna en la BD camiones.
     * Por defecto se asume que camiones usa IdTta, PatTractor, PatSemi, FecAlta, etc.
     * Si tu tabla usa los mismos nombres que nuestro sistema (id_tta, tractor_patente, ...), dejá vacío [].
     *
     * @var array<string, string>
     */
    public array $columnMap = [
        'id_tta'                  => 'IdTta',
        'tractor_patente'          => 'PatTractor',
        'semi_delan_patente'       => 'PatSemi',
        'fecha_alta'              => 'FecAlta',
        'modo_carga'              => 'ModoCarga',
        'tara_total'              => 'TaraTotal',
        'peso_maximo'              => 'PesoMax',
        'nacion'                  => 'Nacion',
        'tractor_tara'            => 'TaraTractor',
        'tractor_pbt'             => 'PBTTractor',
        'semi_delantero_tara'      => 'TaraSemi',
        'semi_delantera_pbt'       => 'PBTSemiDel',
        'patente_semi_trasero'     => 'PatSemi2',
        'semi_trasero_tara'       => 'TaraSemi2',
        'semi_trasero_pbt'        => 'PBTSemiTras',
        'cisterna_1_capacidad'    => 'C1',
        'cisterna_2_capacidad'    => 'C2',
        'cisterna_3_capacidad'    => 'C3',
        'cisterna_4_capacidad'    => 'C4',
        'cisterna_5_capacidad'    => 'C5',
        'cisterna_6_capacidad'    => 'C6',
        'cisterna_7_capacidad'    => 'C7',
        'cisterna_8_capacidad'    => 'C8',
        'cisterna_9_capacidad'    => 'C9',
        'cisterna_10_capacidad'   => 'C10',
        'capacidad_total'          => 'CapTotal',
        'checklist_asfalto'        => 'ASF',
        'checklist_alcohol'        => 'ALC',
        'checklist_biodiesel'      => 'BIO',
        'checklist_comb_liv'       => 'CLI',
        'checklist_comb_pes'       => 'CPE',
        'checklist_solvente'       => 'SOL',
        'checklist_coke'           => 'COK',
        'checklist_lubes_gra'      => 'LGR',
        'checklist_lubes_env'      => 'LENV',
        'checklist_glp'            => 'GLP',
    ];

    /**
     * Columnas que NO existen en la tabla de camiones: no se incluyen en el SELECT.
     * Si la tabla equipos en camiones no tiene "Nacion", dejá nacion.
     *
     * @var list<string>
     */
    public array $excludeColumns = ['nacion'];

    // --- Sincronización CUIT transportistas (comando transportistas:cuit-desde-camiones) ---

    /** Nombre de la tabla de transportistas (TTAs) en la BD camiones */
    public string $tableTtas = 'ttas';

    /** Nombre de la columna con el nombre/razón social del transportista en ttas */
    public string $columnTtasNombre = 'Transportista';

    /** Nombre de la columna con el CUIT en ttas */
    public string $columnTtasCuit = 'Cuit';

    /** Nombre de la columna con el email en ttas (vacío = no sincronizar email) */
    public string $columnTtasEmail = 'Email';

    /** Nombre de la columna Tipo en ttas (IdTipoTta en camiones; en nuestra BD: tipo) */
    public string $columnTtasTipo = 'IdTipoTta';

    /** Nombre de la columna CodAxion en ttas (en nuestra BD: codigo_axion) */
    public string $columnTtasCodAxion = 'CodAxion';

    /** Nombre de la columna Nación en ttas: IdNacTta (ID). Se usa como pais_id al crear; opcionalmente se completa nacion desde tabla naciones. */
    public string $columnTtasNacion = 'IdNacTta';

    /**
     * Mapeo para traer todos los datos de ttas al crear un transportista nuevo.
     * Clave = nombre de columna en nuestra tabla transportistas; valor = nombre de columna en ttas (camiones).
     * Solo incluir columnas que existan en ttas. Si ttas tiene Direccion, Localidad, etc., agregálas acá.
     *
     * @var array<string, string>
     */
    public array $columnMapTtasToTransportistas = [
        'transportista'   => 'Transportista',
        'cuit'            => 'Cuit',
        'tipo'            => 'IdTipoTta',
        'codigo_axion'    => 'CodAxion',
        'mail_contacto'   => 'Email',
        'pais_id'         => 'IdNacTta',
    ];

    // --- Importación choferes (comando choferes:importar-desde-camiones) ---

    /** Nombre de la tabla de choferes en la BD camiones */
    public string $tableChoferes = 'choferes';

    /**
     * Mapeo columnas choferes: nombre interno => nombre real en BD camiones.
     * Estructura en camiones: DNI, Chofer, IdNacChofer, IdTta, ComenChofer (ART, LNH, UltActualiz no se importan).
     *
     * @var array<string, string>
     */
    public array $columnMapChoferes = [
        'documento'   => 'DNI',
        'nombre'      => 'Chofer',
        'id_tta'      => 'IdTta',
        'id_nacion'   => 'IdNacChofer',
        'comentarios' => 'ComenChofer',
    ];

    /** Columna del ID del transportista en la tabla ttas (para cruce por nombre). Ej: IdTta o id_tta */
    public string $columnTtasId = 'IdTta';

    // --- Importación inspectores (comando inspectores:importar-desde-camiones) ---

    /** Nombre de la tabla de inspectores en la BD camiones */
    public string $tableInspectores = 'inspectores';

    /**
     * Mapeo columnas inspectores: nombre interno => nombre real en BD camiones.
     * Si camiones usa "Inspector" en lugar de "inspector", poné 'inspector' => 'Inspector'.
     * Vacío [] = mismos nombres.
     *
     * @var array<string, string>
     */
    public array $columnMapInspectores = [
        'inspector' => 'Inspector',
    ];

    // --- Importación ítems censo desde camiones (tabla items) ---

    /** Nombre de la tabla de ítems en la BD camiones */
    public string $tableItems = 'items';

    /**
     * Mapeo columnas items (camiones) => nuestra columna. Si en camiones la columna se llama "Item" o "Descripcion", poné 'item' => 'Item'.
     *
     * @var array<string, string>
     */
    public array $columnMapItems = [
        'item' => 'Item',
    ];
}
