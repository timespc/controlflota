<?php

namespace Tests\Feature;

use App\Models\NacionesModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Mocks\NacionesModelMock;

/** Tests Feature CRUD Nacion con modelo mockeado. */
class NacionTest extends CIUnitTestCase
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
        Factories::injectMock('models', NacionesModel::class, new NacionesModelMock($items));
    }

    public function testListarWithMock(): void
    {
        $this->injectMock([['id_nacion' => 1, 'nacion' => 'Argentina', 'ult_actualiz' => '2025-01-01 00:00:00']]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'nacion/listar');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertCount(1, $json['data']);
        $this->assertSame('Argentina', $json['data'][0]['nacion']);
    }

    public function testTotalWithMock(): void
    {
        $this->injectMock([['id_nacion' => 1, 'nacion' => 'AR'], ['id_nacion' => 2, 'nacion' => 'UY']]);
        $result = $this->withSession(self::loggedInSession())->call('GET', 'nacion/total');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertSame(2, $json['total']);
    }

    public function testObtenerWithMock(): void
    {
        $this->injectMock([['id_nacion' => 3, 'nacion' => 'Chile', 'created_at' => null, 'updated_at' => null]]);
        $result = $this->withSession(self::loggedInSession())->call('GET', 'nacion/obtener/3');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertSame('Chile', $json['data']['nacion']);
    }

    public function testGuardarCrearWithMock(): void
    {
        $this->injectMock([]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'nacion/guardar', ['nacion' => 'Brasil']);
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertArrayHasKey('id', $json);
    }

    public function testEliminarWithMock(): void
    {
        $this->injectMock([['id_nacion' => 1, 'nacion' => 'X']]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'nacion/eliminar/1');
        $result->assertStatus(200);
        $result->assertJSONFragment(['success' => true]);
    }

    public function testGuardarValidacionFalla(): void
    {
        $result = $this->withSession(self::loggedInSession())->call('POST', 'nacion/guardar', ['nacion' => '']);
        $result->assertStatus(200);
        $result->assertJSONFragment(['success' => false]);
    }
}
