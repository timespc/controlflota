<?php

namespace Tests\Feature;

use App\Models\BanderasModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Mocks\BanderasModelMock;
use Tests\Support\Mocks\BanderasModelThrowingMock;

/**
 * Tests Feature del CRUD Banderas.
 * Los tests que tocan BD (listar, total, obtener, guardar, eliminar) usan BanderasModelMock
 * inyectado vía Factories::injectMock para no depender de la BD real.
 */
class BanderasTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    /** Sesión mínima para que Ion Auth considere al usuario logueado (recheckTimer=0). */
    private static function loggedInSession(): array
    {
        return [
            'user_id'  => 1,
            'identity' => 'test@test.com',
            'email'    => 'test@test.com',
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpMethods = array_diff($this->setUpMethods ?? [], ['mockSession']);
    }

    protected function tearDown(): void
    {
        Factories::reset('models');
        parent::tearDown();
    }

    /** Inyecta un mock de BanderasModel con datos opcionales. */
    private function injectBanderasMock(array $items = []): void
    {
        $mock = new BanderasModelMock($items);
        Factories::injectMock('models', BanderasModel::class, $mock);
    }

    public function testBanderasIndexRequiresLogin(): void
    {
        $result = $this->call('GET', 'banderas');
        $this->assertTrue(
            in_array($result->response()->getStatusCode(), [302, 403], true),
            'Sin sesión debe redirigir o prohibir'
        );
    }

    public function testBanderasListarWithMock(): void
    {
        $this->injectBanderasMock([
            ['id_bandera' => 1, 'bandera' => 'Test 1', 'ult_actualiz' => '2025-01-01 00:00:00'],
        ]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'banderas/listar');
        $result->assertStatus(200);
        $result->assertJSONFragment(['success' => true]);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertArrayHasKey('data', $json);
        $this->assertCount(1, $json['data']);
        $this->assertSame('Test 1', $json['data'][0]['bandera']);
    }

    public function testBanderasTotalWithMock(): void
    {
        $this->injectBanderasMock([
            ['id_bandera' => 1, 'bandera' => 'A'],
            ['id_bandera' => 2, 'bandera' => 'B'],
        ]);
        $result = $this->withSession(self::loggedInSession())->call('GET', 'banderas/total');
        $result->assertStatus(200);
        $result->assertJSONFragment(['success' => true]);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertSame(2, $json['total']);
    }

    public function testBanderasObtenerWithMock(): void
    {
        $this->injectBanderasMock([
            ['id_bandera' => 42, 'bandera' => 'Bandera 42', 'created_at' => null, 'updated_at' => null],
        ]);
        $result = $this->withSession(self::loggedInSession())->call('GET', 'banderas/obtener/42');
        $result->assertStatus(200);
        $result->assertJSONFragment(['success' => true]);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertSame('Bandera 42', $json['data']['bandera']);
    }

    public function testBanderasObtenerInexistenteWithMock(): void
    {
        $this->injectBanderasMock([]);
        $result = $this->withSession(self::loggedInSession())->call('GET', 'banderas/obtener/999');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertFalse($json['success']);
    }

    public function testBanderasGuardarCrearWithMock(): void
    {
        $this->injectBanderasMock([]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'banderas/guardar', [
            'bandera' => 'Nueva Bandera',
        ]);
        $result->assertStatus(200);
        $result->assertJSONFragment(['success' => true]);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertArrayHasKey('id', $json);
        $this->assertGreaterThan(0, $json['id']);
    }

    public function testBanderasEliminarWithMock(): void
    {
        $this->injectBanderasMock([['id_bandera' => 1, 'bandera' => 'X']]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'banderas/eliminar/1');
        $result->assertStatus(200);
        $result->assertJSONFragment(['success' => true]);
    }

    public function testBanderasGuardarValidacionFalla(): void
    {
        $result = $this->withSession(self::loggedInSession())
            ->call('POST', 'banderas/guardar', [
                'bandera' => '', // vacío no permitido
            ]);
        $result->assertStatus(200);
        $result->assertJSONFragment(['success' => false]);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertArrayHasKey('errors', $json);
    }

    /** CrudBaseController: obtener sin ID devuelve error */
    public function testBanderasObtenerSinIdDevuelveError(): void
    {
        $this->injectBanderasMock([]);
        $result = $this->withSession(self::loggedInSession())->call('GET', 'banderas/obtener/0');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertFalse($json['success']);
        $this->assertStringContainsString('ID no proporcionado', $json['message'] ?? '');
    }

    /** CrudBaseController: eliminar sin ID devuelve error */
    public function testBanderasEliminarSinIdDevuelveError(): void
    {
        $this->injectBanderasMock([]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'banderas/eliminar/0');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertFalse($json['success']);
        $this->assertStringContainsString('ID no proporcionado', $json['message'] ?? '');
    }

    /** CrudBaseController: catch en listar cuando el modelo lanza */
    public function testBanderasListarCuandoModeloLanzaDevuelveError(): void
    {
        Factories::injectMock('models', BanderasModel::class, new BanderasModelThrowingMock('listarTodos'));
        $result = $this->withSession(self::loggedInSession())->call('POST', 'banderas/listar');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertFalse($json['success']);
        $this->assertArrayHasKey('message', $json);
    }

    /** CrudBaseController: catch en obtener cuando el modelo lanza */
    public function testBanderasObtenerCuandoModeloLanzaDevuelveError(): void
    {
        Factories::injectMock('models', BanderasModel::class, new BanderasModelThrowingMock('obtenerPorId'));
        $result = $this->withSession(self::loggedInSession())->call('GET', 'banderas/obtener/1');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertFalse($json['success']);
        $this->assertStringContainsString('Error al obtener', $json['message'] ?? '');
    }

    /** CrudBaseController: catch en guardar cuando el modelo lanza */
    public function testBanderasGuardarCuandoModeloLanzaDevuelveError(): void
    {
        Factories::injectMock('models', BanderasModel::class, new BanderasModelThrowingMock('guardarBandera'));
        $result = $this->withSession(self::loggedInSession())->call('POST', 'banderas/guardar', ['bandera' => 'X']);
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertFalse($json['success']);
        $this->assertArrayHasKey('message', $json);
    }

    /** CrudBaseController: catch en eliminar cuando el modelo lanza */
    public function testBanderasEliminarCuandoModeloLanzaDevuelveError(): void
    {
        Factories::injectMock('models', BanderasModel::class, new BanderasModelThrowingMock('eliminarBandera'));
        $result = $this->withSession(self::loggedInSession())->call('POST', 'banderas/eliminar/1');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertFalse($json['success']);
        $this->assertArrayHasKey('message', $json);
    }

    /** CrudBaseController: catch en total cuando el modelo lanza */
    public function testBanderasTotalCuandoModeloLanzaDevuelveError(): void
    {
        Factories::injectMock('models', BanderasModel::class, new BanderasModelThrowingMock('obtenerTotal'));
        $result = $this->withSession(self::loggedInSession())->call('GET', 'banderas/total');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertFalse($json['success']);
        $this->assertArrayHasKey('message', $json);
    }
}
