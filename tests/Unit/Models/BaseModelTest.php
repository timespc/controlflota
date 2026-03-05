<?php

namespace Tests\Unit\Models;

use App\Models\BaseModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Database;

/**
 * Test de los métodos genéricos de BaseModel (get, getAll, store, destroy, countAll).
 * Se usa un modelo concreto con tabla test_base e id como PK para no depender de BD real.
 */
class BaseModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = false;
    protected $db;

    /** Modelo concreto que usa tabla test_base y primaryKey id (como espera BaseModel::get). */
    private function model(): BaseModel
    {
        $m = new class extends BaseModel {
            protected $table         = 'test_base';
            protected $primaryKey   = 'id';
            protected $allowedFields = ['name'];
            protected $DBGroup      = 'tests';
        };
        return $m;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = Database::connect('tests');
        $prefix   = $this->db->getPrefix();
        $this->db->query("CREATE TABLE IF NOT EXISTS {$prefix}test_base (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            name VARCHAR(255) NULL
        )");
        $this->db->query("DELETE FROM {$prefix}test_base");
    }

    public function testGetAllVacio(): void
    {
        $lista = $this->model()->getAll(false, 'id', 'ASC');
        $this->assertIsArray($lista);
        $this->assertCount(0, $lista);
    }

    public function testGetAllConPaginado(): void
    {
        $this->model()->store(['name' => 'A']);
        $this->model()->store(['name' => 'B']);
        $pager = $this->model()->getAll(true, 'id', 'ASC');
        $this->assertIsArray($pager);
        $this->assertCount(1, $pager); // paginate(1) devuelve solo 1 por página
    }

    public function testStoreInsertaYGetAllDevuelveUno(): void
    {
        $this->model()->store(['name' => 'Uno']);
        $lista = $this->model()->getAll(false, 'id', 'ASC');
        $this->assertCount(1, $lista);
        $this->assertSame('Uno', $lista[0]['name']);
    }

    public function testGetDevuelveRegistro(): void
    {
        $this->model()->store(['name' => 'Dos']);
        $id  = (int) $this->db->query("SELECT id FROM " . $this->db->getPrefix() . "test_base LIMIT 1")->getRow()->id;
        $row = $this->model()->get($id);
        $this->assertNotNull($row);
        $this->assertSame('Dos', $row['name']);
    }

    public function testStoreActualiza(): void
    {
        $this->model()->store(['name' => 'Original']);
        $id = (int) $this->db->query("SELECT id FROM " . $this->db->getPrefix() . "test_base LIMIT 1")->getRow()->id;
        $this->model()->store(['id' => $id, 'name' => 'Actualizado']);
        $this->assertSame('Actualizado', $this->model()->get($id)['name']);
    }

    public function testDestroyElimina(): void
    {
        $this->model()->store(['name' => 'X']);
        $id = (int) $this->db->query("SELECT id FROM " . $this->db->getPrefix() . "test_base LIMIT 1")->getRow()->id;
        $this->model()->destroy($id);
        $this->assertNull($this->model()->get($id));
    }

    public function testCountAll(): void
    {
        $this->assertSame(0, $this->model()->countAll());
        $this->model()->store(['name' => 'A']);
        $this->assertSame(1, $this->model()->countAll());
        $this->model()->store(['name' => 'B']);
        $this->assertSame(2, $this->model()->countAll());
    }
}
