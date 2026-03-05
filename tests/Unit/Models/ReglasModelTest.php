<?php

namespace Tests\Unit\Models;

use App\Models\ReglasModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Config\Database;

/**
 * Test de ReglasModel contra SQLite en memoria (grupo "tests").
 * Incluye tabla users mínima para cubrir el branch de listarTodos() que rellena usuario_creacion_nombre.
 */
class ReglasModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = false;
    protected $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = Database::connect('tests');
        $this->crearTablaReglas();
        $this->crearTablaUsers();
        $this->vaciarTablas();
    }

    private function crearTablaReglas(): void
    {
        $prefix = $this->db->getPrefix();
        $this->db->query("CREATE TABLE IF NOT EXISTS {$prefix}reglas (
            id_regla INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            numero_regla VARCHAR(100) NOT NULL,
            habilitada INTEGER NOT NULL DEFAULT 1,
            id_usuario_creacion INTEGER NULL,
            created_at DATETIME NULL,
            updated_at DATETIME NULL
        )");
    }

    private function crearTablaUsers(): void
    {
        $prefix = $this->db->getPrefix();
        $this->db->query("CREATE TABLE IF NOT EXISTS {$prefix}users (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            first_name VARCHAR(50) NULL,
            last_name VARCHAR(50) NULL,
            email VARCHAR(255) NULL
        )");
    }

    private function vaciarTablas(): void
    {
        $prefix = $this->db->getPrefix();
        $this->db->query("DELETE FROM {$prefix}reglas");
        $this->db->query("DELETE FROM {$prefix}users");
    }

    private function model(): ReglasModel
    {
        return new ReglasModel($this->db);
    }

    public function testListarTodosVacio(): void
    {
        $model = $this->model();
        $this->assertCount(0, $model->listarTodos());
    }

    public function testGuardarReglaInsertaYListarDevuelveUno(): void
    {
        $model = $this->model();
        $model->guardarRegla(['numero_regla' => 'R001', 'habilitada' => 1]);
        $lista = $model->listarTodos();
        $this->assertCount(1, $lista);
        $this->assertSame('R001', $lista[0]['numero_regla']);
        $this->assertArrayHasKey('id_regla', $lista[0]);
        $this->assertArrayHasKey('habilitada', $lista[0]);
    }

    public function testObtenerPorId(): void
    {
        $model = $this->model();
        $model->guardarRegla(['numero_regla' => 'R002', 'habilitada' => 0]);
        $id  = (int) $model->listarTodos()[0]['id_regla'];
        $row = $model->obtenerPorId($id);
        $this->assertNotNull($row);
        $this->assertSame('R002', $row['numero_regla']);
        $this->assertSame(0, (int) $row['habilitada']);
    }

    public function testGuardarReglaActualiza(): void
    {
        $model = $this->model();
        $model->guardarRegla(['numero_regla' => 'Original', 'habilitada' => 1]);
        $id = (int) $model->listarTodos()[0]['id_regla'];
        $model->guardarRegla(['id_regla' => $id, 'numero_regla' => 'Actualizado', 'habilitada' => 0]);
        $row = $model->obtenerPorId($id);
        $this->assertSame('Actualizado', $row['numero_regla']);
        $this->assertSame(0, (int) $row['habilitada']);
    }

    public function testEliminarRegla(): void
    {
        $model = $this->model();
        $model->guardarRegla(['numero_regla' => 'X', 'habilitada' => 1]);
        $id = (int) $model->listarTodos()[0]['id_regla'];
        $model->eliminarRegla($id);
        $this->assertNull($model->obtenerPorId($id));
        $this->assertCount(0, $model->listarTodos());
    }

    public function testObtenerTotal(): void
    {
        $model = $this->model();
        $this->assertSame(0, $model->obtenerTotal());
        $model->guardarRegla(['numero_regla' => 'A', 'habilitada' => 1]);
        $this->assertSame(1, $model->obtenerTotal());
        $model->guardarRegla(['numero_regla' => 'B', 'habilitada' => 0]);
        $this->assertSame(2, $model->obtenerTotal());
    }

    public function testObtenerHabilitada(): void
    {
        $model = $this->model();
        $this->assertNull($model->obtenerHabilitada());
        $model->guardarRegla(['numero_regla' => 'Unica', 'habilitada' => 1]);
        $r = $model->obtenerHabilitada();
        $this->assertNotNull($r);
        $this->assertSame('Unica', $r['numero_regla']);
        $this->assertSame(1, (int) $r['habilitada']);
    }

    /** Cubre el branch de listarTodos() que rellena usuario_creacion_nombre desde tabla users */
    public function testListarTodosConUsuarioCreacionNombre(): void
    {
        $prefix = $this->db->getPrefix();
        $this->db->query("INSERT INTO {$prefix}users (id, first_name, last_name, email) VALUES (1, 'Usuario', 'Test', 'test@test.com')");
        $model = $this->model();
        $model->guardarRegla(['numero_regla' => 'R-Usuario', 'habilitada' => 1, 'id_usuario_creacion' => 1]);
        $lista = $model->listarTodos();
        $this->assertCount(1, $lista);
        $this->assertArrayHasKey('usuario_creacion_nombre', $lista[0]);
        $this->assertSame('Usuario Test', $lista[0]['usuario_creacion_nombre']);
    }

    /** Cuando first_name/last_name están vacíos, listarTodos usa email para usuario_creacion_nombre */
    public function testListarTodosUsuarioSinNombreUsaEmail(): void
    {
        $prefix = $this->db->getPrefix();
        $this->db->query("INSERT INTO {$prefix}users (id, first_name, last_name, email) VALUES (2, '', '', 'email@fallback.com')");
        $model = $this->model();
        $model->guardarRegla(['numero_regla' => 'R-SinNombre', 'habilitada' => 0, 'id_usuario_creacion' => 2]);
        $lista = $model->listarTodos();
        $this->assertSame('email@fallback.com', $lista[0]['usuario_creacion_nombre']);
    }

    /** Tabla users existe pero todas las reglas tienen id_usuario_creacion null -> empty($ids) -> usuario_creacion_nombre null */
    public function testListarTodosReglasSinUsuarioCreacionDevuelveNullEnNombre(): void
    {
        $prefix = $this->db->getPrefix();
        $this->db->query("INSERT INTO {$prefix}users (id, first_name, last_name, email) VALUES (1, 'Uno', '', 'u@u.com')");
        $model = $this->model();
        $model->guardarRegla(['numero_regla' => 'SinCreador', 'habilitada' => 1]);
        $lista = $model->listarTodos();
        $this->assertSame(null, $lista[0]['usuario_creacion_nombre']);
    }

    /** id_usuario_creacion apunta a un usuario que no está en la tabla -> map no tiene clave -> usuario_creacion_nombre null */
    public function testListarTodosUsuarioInexistenteDevuelveNullEnNombre(): void
    {
        $prefix = $this->db->getPrefix();
        $this->db->query("INSERT INTO {$prefix}users (id, first_name, last_name, email) VALUES (1, 'Solo', 'Uno', 'one@test.com')");
        $model = $this->model();
        $model->guardarRegla(['numero_regla' => 'R-Orphan', 'habilitada' => 0, 'id_usuario_creacion' => 999]);
        $lista = $model->listarTodos();
        $this->assertSame(null, $lista[0]['usuario_creacion_nombre']);
    }

    /** Al guardar una regla como habilitada=1, las demás pasan a habilitada=0 (solo una habilitada a la vez) */
    public function testGuardarReglaHabilitadaDeshabilitaLasDemas(): void
    {
        $model = $this->model();
        $model->guardarRegla(['numero_regla' => 'Primera', 'habilitada' => 1]);
        $model->guardarRegla(['numero_regla' => 'Segunda', 'habilitada' => 0]);
        $lista     = $model->listarTodos();
        $idPrimera = (int) $lista[0]['id_regla'];
        $idSegunda = (int) $lista[1]['id_regla'];
        $this->assertSame(1, (int) $model->obtenerPorId($idPrimera)['habilitada']);
        $model->guardarRegla(['id_regla' => $idSegunda, 'numero_regla' => 'Segunda', 'habilitada' => 1]);
        $this->assertSame(0, (int) $model->obtenerPorId($idPrimera)['habilitada']);
        $this->assertSame(1, (int) $model->obtenerPorId($idSegunda)['habilitada']);
    }
}
