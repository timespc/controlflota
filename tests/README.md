# Tests de la aplicación

Suite básica de tests con PHPUnit (CodeIgniter 4).

## Cómo ejecutar

Desde la raíz del proyecto:

```bash
vendor\bin\phpunit
```

O con el script de composer:

```bash
composer test
```

### Code coverage

Para ver el reporte de cobertura de código hace falta **Xdebug** (o PCOV) con modo coverage. Luego:

```bash
# Windows (PowerShell)
$env:XDEBUG_MODE="coverage"; vendor\bin\phpunit --coverage-text

# O solo generar HTML en build/coverage
$env:XDEBUG_MODE="coverage"; vendor\bin\phpunit
```

Se genera:
- **Texto** en consola (resumen por clase).
- **HTML** en `build/coverage/` (abrir `index.html` en el navegador).

**Alcance de la cobertura:** En `phpunit.xml.dist` se excluyen `app/Views`, `app/Database` (migraciones y seeds), `app/Commands` y `app/Config/Routes.php`. El porcentaje de "clases" y "métodos" en PHPUnit se refiere a clases/métodos con cobertura **total** (100 %); para subir esos porcentajes hace falta cubrir por completo más clases (p. ej. con tests que usen BD de tests o excluir más código del informe).

### Cobertura hasta 2026-02-21

Ejecutando `$env:XDEBUG_MODE="coverage"; vendor\bin\phpunit --coverage-text` (141 tests):

| Métrica  | Cobertura | Detalle        |
|----------|-----------|----------------|
| Clases   | 18,64 %   | 11/59 al 100 % |
| Métodos  | 24,90 %   | 61/245         |
| Líneas   | 16,74 %   | 499/2980       |

**Clases al 100 % (líneas):** BaseController, BanderasModel, **BaseModel**, CalibradoresModel, CubiertasModel, MarcasModel, MarcasSensorModel, NacionesModel, Database, CustomRules. ReglasModel ~97,5 % líneas. Controladores: CrudBaseController ~90 %, Reglas ~83 %, Auth ~54 %. Calibracion ~6 %, Reportes ~8 %, Equipos ~14 % (flujos index/listar/obtener cubiertos con mocks). CustomAuth, CheckLogin con cobertura parcial.

### Cómo seguir mejorando la cobertura

Prioridad sugerida (más impacto o menos esfuerzo primero):

1. ~~**ReglasModel al 100 %**~~ — Hecho: tabla `usuarios` en `ReglasModelTest`, tests para `usuario_creacion_nombre`, branch empty($ids), usuario inexistente y deshabilitar otras al guardar habilitada. ReglasModel ~97,5 % líneas. En el modelo se usa `$this->db` en `listarTodos()` para que en tests se use la misma BD.
2. ~~**CustomRules al 100 %**~~ — Hecho: añadido `testValidEmailMultipleConSegmentosVacios` para el branch `empty($email) -> continue`. CustomRules 100 % líneas.
3. ~~**CrudBaseController al 100 %**~~ — Hecho en gran parte: en BanderasTest se añadieron tests para obtener/eliminar sin ID (error "ID no proporcionado"), y para los catch de listar, obtener, guardar, eliminar y total cuando el modelo lanza (BanderasModelThrowingMock). CrudBaseController ~90 % líneas. Las ~8 líneas restantes son el branch de total() cuando crudTotalMethod === '' (ningún CRUD lo usa) y posiblemente getCrudValidationRules/config.
4. ~~**Auth y CheckLogin**~~ — Hecho: en AuthLoginTest se añadieron tests de rutas protegidas con sesión (dashboard y banderas devuelven 200), y de redirección cuando ya se está logueado (GET auth/login con sesión redirige a home). Se inyecta UserGroupModelMock para que las vistas con sidebar no toquen la BD. Sube cobertura de Auth (~53 %) y del filtro CheckLogin (~48 %).
5. ~~**BaseModel**~~ — Hecho: en `tests/Unit/Models/BaseModelTest.php` se usa una tabla SQLite `test_base` y un modelo anónimo que extiende BaseModel con `DBGroup = 'tests'`. Se testean `getAll` (sin y con paginado), `store` (insert y update), `get`, `destroy` y `countAll`. BaseModel ~93 % líneas (rama paginate cubierta).
6. ~~**Calibración, Reportes, Equipos (flujos críticos)**~~ — Hecho: tests Feature que mockean modelos. **CalibracionTest**: index (ReglasModel mock), listar y obtener (CalibracionModelMock); obtener sin ID e inexistente. **ReportesTest**: index y reportes/calibraciones con sesión (CalibradoresModel mock). **EquiposTest**: index con sesión (TransportistasModel, PaisModel, BanderasModel, MarcasModel, CubiertasModel mocks). Los controladores Reportes y Equipos pasan a usar `model()` en el constructor para permitir inyección de mocks. Mocks nuevos: CalibracionModelMock, TransportistasModelMock, PaisModelMock; ReglasModelMock ampliado con `obtenerHabilitada()`.

Para ver exactamente qué falta en una clase: abrir `build/coverage/index.html` después de `$env:XDEBUG_MODE="coverage"; vendor\bin\phpunit` y entrar en la clase; las líneas en rojo son las no cubiertas.

## Estructura

- **tests/Unit/** — Tests unitarios.
  - `Models/`: tests de modelos CRUD contra **SQLite en memoria** (grupo `tests`). Cada uno crea solo su tabla con SQL compatible con SQLite y prueba listar, guardar, obtener, actualizar, eliminar, total. No usan la BD de desarrollo ni producción.
    - `BanderasModelTest.php`, `MarcasModelTest.php`, `CubiertasModelTest.php`, `NacionesModelTest.php`, `CalibradoresModelTest.php`, `MarcasSensorModelTest.php`, `ReglasModelTest.php` (Reglas además prueba `obtenerHabilitada()`), `BaseModelTest.php` (get/getAll/store/destroy/countAll con tabla test_base).
  - `JsonResponseTest.php`: helper `json_response()`.
  - `Config/CrudValidationTest.php`, `Config/CamionesTest.php`: configuraciones.
  - `Controllers/BaseControllerTest.php`: helpers del controlador base.
  - `Filters/CsrfWithExemptionsTest.php`: filtro CSRF en entorno testing.
  - `Validation/CustomRulesTest.php`: regla `valid_email_multiple`.
- **tests/Feature/** — Tests de integración / HTTP.
  - `AuthLoginTest.php`: login accesible, redirección sin sesión, **con sesión** dashboard y banderas 200, y GET auth/login con sesión redirige a home.
  - `AuthTest.php`: logout, login POST inválido, vistas pendiente/rechazado.
  - `BanderasTest.php`: CRUD Banderas con **modelo mockeado** (listar, total, obtener, guardar, eliminar, validación).
  - `CalibracionTest.php`: index, listar, obtener (con CalibracionModelMock y ReglasModelMock).
  - `ReportesTest.php`: index y reportes/calibraciones con sesión (CalibradoresModel mock).
  - `EquiposTest.php`: index con sesión (mocks de transportistas, países, banderas, marcas, cubiertas).
  - `CalibradoresTest.php`, `CubiertasTest.php`, `MarcasSensorTest.php`, `MarcasTest.php`, `NacionTest.php`, `ReglasTest.php`: CRUD con modelo mockeado (mismo patrón que Banderas).
  - `DashboardTest.php`: redirección sin login.
- **tests/_support/Mocks/** — Mocks para tests sin BD.
  - `BanderasModelMock.php`, `CalibradoresModelMock.php`, `CubiertasModelMock.php`, `MarcasModelMock.php`, `MarcasSensorModelMock.php`, `NacionesModelMock.php`, `ReglasModelMock.php` (incl. `obtenerHabilitada()`): datos fijos para listar/obtener/total y simulan guardar/eliminar.
  - `CalibracionModelMock.php`: listarParaDataTable, obtenerPorIdConDetalle, find; usado en CalibracionTest.
  - `TransportistasModelMock.php`, `PaisModelMock.php`: listarTodos/listarSoloConEquipos y obtenerTodos; usados en EquiposTest.
  - `UserGroupModelMock.php`: usado en ReglasTest y en tests con sesión (sidebar) para que `usuario_actual()` no toque la BD.
  - `BanderasModelThrowingMock.php`: lanza en el método indicado; usado en BanderasTest para cubrir los catch de CrudBaseController.

## Mockear la base de datos

Para no depender de la BD en los tests, se mockean los modelos:

1. **CrudBaseController** obtiene el modelo con `model($this->crudModelClass)` (no `new`), así se puede inyectar un mock vía `Factories::injectMock('models', ModelClass::class, $mock)`.

2. **Mock del modelo:** En `tests/_support/Mocks/` se define una clase que implementa los métodos que usa el controlador (p. ej. `listarTodos()`, `obtenerPorId()`, `guardarBandera()`, `eliminarBandera()`, `obtenerTotal()`, `getInsertID()`), devolviendo datos fijos o simulando éxito.

3. **En el test:** En `setUp()` o en el método de test se inyecta el mock antes de `$this->call(...)`; en `tearDown()` se hace `Factories::reset('models')` para no afectar otros tests.

Ejemplo (como en `BanderasTest.php`):

```php
use CodeIgniter\Config\Factories;
use App\Models\BanderasModel;
use Tests\Support\Mocks\BanderasModelMock;

// En el test:
$this->injectBanderasMock([
    ['id_bandera' => 1, 'bandera' => 'Test 1', 'ult_actualiz' => '2025-01-01 00:00:00'],
]);
$result = $this->withSession(self::loggedInSession())->call('POST', 'banderas/listar');
$result->assertJSONFragment(['success' => true]);
```

Los CRUD Marcas, Cubiertas, Nacion, Calibradores, MarcasSensor y Reglas tienen ya su test Feature y mock en `_support/Mocks/`. Para un nuevo CRUD, crear un mock similar e inyectarlo con `Factories::injectMock('models', ModelClass::class, $mock)` (para modelos usados por nombre corto, p. ej. en helpers, usar el alias `'UserGroupModel'`).

## Tests de modelos (con datos mockeados o BD de test)

Sí se puede testear el código de los modelos (y subir cobertura de esos archivos). Dos enfoques:

1. **BD de test** (recomendado): Configurar en tests una base aparte (p. ej. SQLite en memoria o MySQL de tests), correr migraciones o crear la tabla a mano, insertar datos de prueba y llamar a los métodos del modelo. Ejemplo: `tests/Unit/Models/BanderasModelTest.php`, que usa el grupo **`tests`** de `app/Config/Database.php` (SQLite `:memory:`).
2. **Mock de la conexión**: Inyectar al modelo una conexión falsa que devuelve filas prefijadas cuando el modelo ejecuta queries. Requiere implementar o mockear `CodeIgniter\Database\ConnectionInterface` / resultados de builder.

Los tests Feature actuales mockean el **modelo entero** (el controlador usa un mock), por eso el código del modelo no se ejecuta. Para cubrir los modelos hace falta uno de los dos enfoques de arriba.

### ¿Afecta esto a producción o al deploy?

**No.** La BD de test solo se usa cuando se ejecuta PHPUnit:

- En **producción** la app usa la conexión por defecto (`Database::$default`), que suele leerse de `.env` (host, base, usuario, etc.). Esa configuración no cambia por tener tests.
- El grupo **`tests`** (SQLite `:memory:`) está definido en `app/Config/Database.php` y solo se usa en tests al hacer `Database::connect('tests')` o al usar `DatabaseTestTrait` con `$DBGroup = 'tests'`. El deploy no ejecuta PHPUnit contra la BD de producción; quien hace el deploy puede seguir igual (migraciones, `.env`, etc.). No hace falta tocar nada en producción por estos tests.

## Configuración

- **phpunit.xml.dist** en la raíz: bootstrap de CI4, testsuites Unit y Feature, constantes (HOMEPATH, CONFIGPATH, PUBLICPATH).
- El bootstrap carga las rutas de la app; los tests de feature usan `FeatureTestTrait` y `$this->call('GET', 'ruta')`.

## Añadir más tests

1. **Unit:** Crear `tests/Unit/NombreTest.php` con namespace `Tests\Unit`, extender `CodeIgniter\Test\CIUnitTestCase`.
2. **Feature:** Crear `tests/Feature/NombreTest.php` con namespace `Tests\Feature`, extender `CIUnitTestCase` y usar `FeatureTestTrait` para `$this->call()`, `assertStatus()`, etc.

Si PHP no encuentra la clase de test, ejecutar `composer dump-autoload` y añadir en **composer.json** (autoload-dev.psr-4): `"Tests\\": "tests/"`.
