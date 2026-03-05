<?php

namespace Tests\Unit\Models;

use App\Models\BanderasModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Database;

/**
 * Test de BanderasModel contra SQLite en memoria (grupo "tests").
 * No toca la BD de desarrollo ni producción: solo se usa la conexión
 * configurada en Database::$tests ( :memory: ). La tabla se crea aquí
 * con SQL compatible con SQLite para no depender de migraciones MySQL.
 */
class BanderasModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    /** No ejecutar migraciones de App (están hechas para MySQL) */
    protected $migrate = false;

    /** Conexión tests (SQLite :memory:) */
    protected $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = Database::connect('tests');
        $this->crearTablaBanderas();
        $this->vaciarBanderas();
    }

    /** Crea solo la tabla banderas con SQL válido en SQLite */
    private function crearTablaBanderas(): void
    {
        $prefix = $this->db->getPrefix();
        $sql    = "CREATE TABLE IF NOT EXISTS {$prefix}banderas (
            id_bandera INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            bandera VARCHAR(255) NOT NULL,
            created_at DATETIME NULL,
            updated_at DATETIME NULL
        )";
        $this->db->query($sql);
    }

    /** Deja la tabla vacía para que cada test arranque aislado */
    private function vaciarBanderas(): void
    {
        $prefix = $this->db->getPrefix();
        $this->db->query("DELETE FROM {$prefix}banderas");
    }

    /** Modelo usando la BD de test para no tocar default/producción */
    private function model(): BanderasModel
    {
        return new BanderasModel($this->db);
    }

    public function testListarTodosVacío(): void
    {
        $model = $this->model();
        $lista = $model->listarTodos();
        $this->assertIsArray($lista);
        $this->assertCount(0, $lista);
    }

    public function testGuardarBanderaInsertaYListarTodosDevuelveUno(): void
    {
        $model = $this->model();
        $model->guardarBandera(['bandera' => 'Test bandera']);
        $lista = $model->listarTodos();
        $this->assertCount(1, $lista);
        $this->assertSame('Test bandera', $lista[0]['bandera']);
        $this->assertArrayHasKey('id_bandera', $lista[0]);
        $this->assertArrayHasKey('ult_actualiz', $lista[0]);
    }

    public function testObtenerPorId(): void
    {
        $model = $this->model();
        $model->guardarBandera(['bandera' => 'Otra']);
        $lista = $model->listarTodos();
        $id    = (int) $lista[0]['id_bandera'];
        $row   = $model->obtenerPorId($id);
        $this->assertNotNull($row);
        $this->assertSame('Otra', $row['bandera']);
        $this->assertSame($id, (int) $row['id_bandera']);
    }

    public function testGuardarBanderaActualiza(): void
    {
        $model = $this->model();
        $model->guardarBandera(['bandera' => 'Original']);
        $lista = $model->listarTodos();
        $id    = (int) $lista[0]['id_bandera'];
        $model->guardarBandera(['id_bandera' => $id, 'bandera' => 'Actualizado']);
        $row = $model->obtenerPorId($id);
        $this->assertSame('Actualizado', $row['bandera']);
    }

    public function testEliminarBandera(): void
    {
        $model = $this->model();
        $model->guardarBandera(['bandera' => 'Para borrar']);
        $lista = $model->listarTodos();
        $id    = (int) $lista[0]['id_bandera'];
        $model->eliminarBandera($id);
        $this->assertNull($model->obtenerPorId($id));
        $this->assertCount(0, $model->listarTodos());
    }

    public function testObtenerTotal(): void
    {
        $model = $this->model();
        $this->assertSame(0, $model->obtenerTotal());
        $model->guardarBandera(['bandera' => 'A']);
        $this->assertSame(1, $model->obtenerTotal());
        $model->guardarBandera(['bandera' => 'B']);
        $this->assertSame(2, $model->obtenerTotal());
    }
}
