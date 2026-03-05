<?php

namespace Tests\Unit\Models;

use App\Models\CalibradoresModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Database;

/**
 * Test de CalibradoresModel contra SQLite en memoria (grupo "tests").
 */
class CalibradoresModelTest extends CIUnitTestCase
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
        $this->db->query("CREATE TABLE IF NOT EXISTS {$prefix}calibradores (
            id_calibrador INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            calibrador VARCHAR(255) NOT NULL,
            created_at DATETIME NULL,
            updated_at DATETIME NULL
        )");
    }

    private function vaciarTabla(): void
    {
        $prefix = $this->db->getPrefix();
        $this->db->query("DELETE FROM {$prefix}calibradores");
    }

    private function model(): CalibradoresModel
    {
        return new CalibradoresModel($this->db);
    }

    public function testListarTodosVacio(): void
    {
        $model = $this->model();
        $this->assertCount(0, $model->listarTodos());
    }

    public function testGuardarCalibradorInsertaYListarDevuelveUno(): void
    {
        $model = $this->model();
        $model->guardarCalibrador(['calibrador' => 'Calibrador A']);
        $lista = $model->listarTodos();
        $this->assertCount(1, $lista);
        $this->assertSame('Calibrador A', $lista[0]['calibrador']);
        $this->assertArrayHasKey('id_calibrador', $lista[0]);
    }

    public function testObtenerPorId(): void
    {
        $model = $this->model();
        $model->guardarCalibrador(['calibrador' => 'Otro']);
        $id  = (int) $model->listarTodos()[0]['id_calibrador'];
        $row = $model->obtenerPorId($id);
        $this->assertNotNull($row);
        $this->assertSame('Otro', $row['calibrador']);
    }

    public function testGuardarCalibradorActualiza(): void
    {
        $model = $this->model();
        $model->guardarCalibrador(['calibrador' => 'Original']);
        $id = (int) $model->listarTodos()[0]['id_calibrador'];
        $model->guardarCalibrador(['id_calibrador' => $id, 'calibrador' => 'Actualizado']);
        $this->assertSame('Actualizado', $model->obtenerPorId($id)['calibrador']);
    }

    public function testEliminarCalibrador(): void
    {
        $model = $this->model();
        $model->guardarCalibrador(['calibrador' => 'X']);
        $id = (int) $model->listarTodos()[0]['id_calibrador'];
        $model->eliminarCalibrador($id);
        $this->assertNull($model->obtenerPorId($id));
        $this->assertCount(0, $model->listarTodos());
    }

    public function testObtenerTotal(): void
    {
        $model = $this->model();
        $this->assertSame(0, $model->obtenerTotal());
        $model->guardarCalibrador(['calibrador' => 'A']);
        $this->assertSame(1, $model->obtenerTotal());
        $model->guardarCalibrador(['calibrador' => 'B']);
        $this->assertSame(2, $model->obtenerTotal());
    }
}
