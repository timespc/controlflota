<?php

namespace Tests\Unit\Models;

use App\Models\NacionesModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Database;

/** Test de NacionesModel contra SQLite en memoria (grupo "tests"). */
class NacionesModelTest extends CIUnitTestCase
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
        $this->db->query("CREATE TABLE IF NOT EXISTS {$p}naciones (id_nacion INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nacion VARCHAR(255) NOT NULL, created_at DATETIME NULL, updated_at DATETIME NULL)");
    }

    private function vaciarTabla(): void
    {
        $this->db->query("DELETE FROM " . $this->db->getPrefix() . "naciones");
    }

    private function model(): NacionesModel
    {
        return new NacionesModel($this->db);
    }

    public function testListarTodosVacio(): void
    {
        $this->assertCount(0, $this->model()->listarTodos());
    }

    public function testGuardarNacionInsertaYListarDevuelveUno(): void
    {
        $this->model()->guardarNacion(['nacion' => 'Argentina']);
        $lista = $this->model()->listarTodos();
        $this->assertCount(1, $lista);
        $this->assertSame('Argentina', $lista[0]['nacion']);
        $this->assertArrayHasKey('id_nacion', $lista[0]);
    }

    public function testObtenerPorId(): void
    {
        $this->model()->guardarNacion(['nacion' => 'Chile']);
        $id = (int) $this->model()->listarTodos()[0]['id_nacion'];
        $row = $this->model()->obtenerPorId($id);
        $this->assertNotNull($row);
        $this->assertSame('Chile', $row['nacion']);
    }

    public function testGuardarNacionActualiza(): void
    {
        $this->model()->guardarNacion(['nacion' => 'Original']);
        $id = (int) $this->model()->listarTodos()[0]['id_nacion'];
        $this->model()->guardarNacion(['id_nacion' => $id, 'nacion' => 'Actualizado']);
        $this->assertSame('Actualizado', $this->model()->obtenerPorId($id)['nacion']);
    }

    public function testEliminarNacion(): void
    {
        $this->model()->guardarNacion(['nacion' => 'X']);
        $id = (int) $this->model()->listarTodos()[0]['id_nacion'];
        $this->model()->eliminarNacion($id);
        $this->assertNull($this->model()->obtenerPorId($id));
        $this->assertCount(0, $this->model()->listarTodos());
    }

    public function testObtenerTotal(): void
    {
        $m = $this->model();
        $this->assertSame(0, $m->obtenerTotal());
        $m->guardarNacion(['nacion' => 'A']);
        $this->assertSame(1, $m->obtenerTotal());
        $m->guardarNacion(['nacion' => 'B']);
        $this->assertSame(2, $m->obtenerTotal());
    }
}
