<?php

namespace Tests\Feature;

use App\Models\MarcasModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Mocks\MarcasModelMock;

/** Tests Feature CRUD Marcas con modelo mockeado. */
class MarcasTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    private static function loggedInSession(): array
    {
        return ['user_id' => 1, 'identity' => 'test@test.com', 'email' => 'test@test.com'];
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

    private function injectMock(array $items = []): void
    {
        Factories::injectMock('models', MarcasModel::class, new MarcasModelMock($items));
    }

    public function testListarWithMock(): void
    {
        $this->injectMock([['id_marca' => 1, 'marca' => 'Marca A', 'ult_actualiz' => '2025-01-01']]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'marcas/listar');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertCount(1, $json['data']);
        $this->assertSame('Marca A', $json['data'][0]['marca']);
    }

    public function testTotalWithMock(): void
    {
        $this->injectMock([['id_marca' => 1, 'marca' => 'A'], ['id_marca' => 2, 'marca' => 'B']]);
        $result = $this->withSession(self::loggedInSession())->call('GET', 'marcas/total');
        $result->assertStatus(200);
        $this->assertSame(2, json_decode($result->response()->getBody(), true)['total']);
    }

    public function testObtenerWithMock(): void
    {
        $this->injectMock([['id_marca' => 5, 'marca' => 'M5', 'created_at' => null, 'updated_at' => null]]);
        $result = $this->withSession(self::loggedInSession())->call('GET', 'marcas/obtener/5');
        $result->assertStatus(200);
        $this->assertSame('M5', json_decode($result->response()->getBody(), true)['data']['marca']);
    }

    public function testGuardarCrearWithMock(): void
    {
        $this->injectMock([]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'marcas/guardar', ['marca' => 'Nueva Marca']);
        $result->assertStatus(200);
        $this->assertArrayHasKey('id', json_decode($result->response()->getBody(), true));
    }

    public function testEliminarWithMock(): void
    {
        $this->injectMock([['id_marca' => 1, 'marca' => 'X']]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'marcas/eliminar/1');
        $result->assertStatus(200);
        $result->assertJSONFragment(['success' => true]);
    }

    public function testGuardarValidacionFalla(): void
    {
        $result = $this->withSession(self::loggedInSession())->call('POST', 'marcas/guardar', ['marca' => '']);
        $result->assertStatus(200);
        $result->assertJSONFragment(['success' => false]);
    }
}
