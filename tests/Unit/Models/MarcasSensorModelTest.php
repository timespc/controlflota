<?php

namespace Tests\Unit\Models;

use App\Models\MarcasSensorModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Database;

/** Test de MarcasSensorModel contra SQLite en memoria (grupo "tests"). */
class MarcasSensorModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = false;
    protected $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = Database::connect('tests');
        $this->crearTabla();
        $this->vaciarTabla();
    }

    private function crearTabla(): void
    {
        $p = $this->db->getPrefix();
        $this->db->query("CREATE TABLE IF NOT EXISTS {$p}marcas_sensor (id_marca_sensor INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, marca VARCHAR(255) NOT NULL, created_at DATETIME NULL, updated_at DATETIME NULL)");
    }

    private function vaciarTabla(): void
    {
        $this->db->query("DELETE FROM " . $this->db->getPrefix() . "marcas_sensor");
    }

    private function model(): MarcasSensorModel
    {
        return new MarcasSensorModel($this->db);
    }

    public function testListarTodosVacio(): void
    {
        $this->assertCount(0, $this->model()->listarTodos());
    }

    public function testGuardarMarcaInsertaYListarDevuelveUno(): void
    {
        $this->model()->guardarMarca(['marca' => 'Sensor A']);
        $lista = $this->model()->listarTodos();
        $this->assertCount(1, $lista);
        $this->assertSame('Sensor A', $lista[0]['marca']);
        $this->assertArrayHasKey('id_marca_sensor', $lista[0]);
    }

    public function testObtenerPorId(): void
    {
        $this->model()->guardarMarca(['marca' => 'Otro']);
        $id = (int) $this->model()->listarTodos()[0]['id_marca_sensor'];
        $row = $this->model()->obtenerPorId($id);
        $this->assertNotNull($row);
        $this->assertSame('Otro', $row['marca']);
    }

    public function testGuardarMarcaActualiza(): void
    {
        $this->model()->guardarMarca(['marca' => 'Original']);
        $id = (int) $this->model()->listarTodos()[0]['id_marca_sensor'];
        $this->model()->guardarMarca(['id_marca_sensor' => $id, 'marca' => 'Actualizado']);
        $this->assertSame('Actualizado', $this->model()->obtenerPorId($id)['marca']);
    }

    public function testEliminarMarca(): void
    {
        $this->model()->guardarMarca(['marca' => 'X']);
        $id = (int) $this->model()->listarTodos()[0]['id_marca_sensor'];
        $this->model()->eliminarMarca($id);
        $this->assertNull($this->model()->obtenerPorId($id));
        $this->assertCount(0, $this->model()->listarTodos());
    }

    public function testObtenerTotal(): void
    {
        $m = $this->model();
        $this->assertSame(0, $m->obtenerTotal());
        $m->guardarMarca(['marca' => 'A']);
        $this->assertSame(1, $m->obtenerTotal());
        $m->guardarMarca(['marca' => 'B']);
        $this->assertSame(2, $m->obtenerTotal());
    }
}
