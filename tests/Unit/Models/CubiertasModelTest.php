<?php

namespace Tests\Unit\Models;

use App\Models\CubiertasModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Database;

/**
 * Test de CubiertasModel contra SQLite en memoria (grupo "tests").
 */
class CubiertasModelTest extends CIUnitTestCase
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
        $prefix = $this->db->getPrefix();
        $this->db->query("CREATE TABLE IF NOT EXISTS {$prefix}cubiertas (
            id_cubierta INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            medida VARCHAR(255) NOT NULL,
            created_at DATETIME NULL,
            updated_at DATETIME NULL
        )");
    }

    private function vaciarTabla(): void
    {
        $prefix = $this->db->getPrefix();
        $this->db->query("DELETE FROM {$prefix}cubiertas");
    }

    private function model(): CubiertasModel
    {
        return new CubiertasModel($this->db);
    }

    public function testListarTodosVacio(): void
    {
        $model = $this->model();
        $this->assertCount(0, $model->listarTodos());
    }

    public function testGuardarCubiertaInsertaYListarDevuelveUno(): void
    {
        $model = $this->model();
        $model->guardarCubierta(['medida' => '295/80 x 22.5']);
        $lista = $model->listarTodos();
        $this->assertCount(1, $lista);
        $this->assertSame('295/80 x 22.5', $lista[0]['medida']);
        $this->assertArrayHasKey('id_cubierta', $lista[0]);
    }

    public function testObtenerPorId(): void
    {
        $model = $this->model();
        $model->guardarCubierta(['medida' => '1100X20']);
        $id  = (int) $model->listarTodos()[0]['id_cubierta'];
        $row = $model->obtenerPorId($id);
        $this->assertNotNull($row);
        $this->assertSame('1100X20', $row['medida']);
    }

    public function testGuardarCubiertaActualiza(): void
    {
        $model = $this->model();
        $model->guardarCubierta(['medida' => 'Original']);
        $id = (int) $model->listarTodos()[0]['id_cubierta'];
        $model->guardarCubierta(['id_cubierta' => $id, 'medida' => 'Actualizado']);
        $this->assertSame('Actualizado', $model->obtenerPorId($id)['medida']);
    }

    public function testEliminarCubierta(): void
    {
        $model = $this->model();
        $model->guardarCubierta(['medida' => 'X']);
        $id = (int) $model->listarTodos()[0]['id_cubierta'];
        $model->eliminarCubierta($id);
        $this->assertNull($model->obtenerPorId($id));
        $this->assertCount(0, $model->listarTodos());
    }

    public function testObtenerTotal(): void
    {
        $model = $this->model();
        $this->assertSame(0, $model->obtenerTotal());
        $model->guardarCubierta(['medida' => 'A']);
        $this->assertSame(1, $model->obtenerTotal());
        $model->guardarCubierta(['medida' => 'B']);
        $this->assertSame(2, $model->obtenerTotal());
    }
}
