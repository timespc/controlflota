<?php

namespace Tests\Feature;

use App\Models\CalibradoresModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Mocks\CalibradoresModelMock;

/** Tests Feature CRUD Calibradores con modelo mockeado. */
class CalibradoresTest extends CIUnitTestCase
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
        Factories::injectMock('models', CalibradoresModel::class, new CalibradoresModelMock($items));
    }

    public function testListarWithMock(): void
    {
        $this->injectMock([['id_calibrador' => 1, 'calibrador' => 'Calibrador A', 'ult_actualiz' => '2025-01-01 00:00:00']]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'calibradores/listar');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertCount(1, $json['data']);
        $this->assertSame('Calibrador A', $json['data'][0]['calibrador']);
    }

    public function testTotalWithMock(): void
    {
        $this->injectMock([['id_calibrador' => 1, 'calibrador' => 'AR'], ['id_calibrador' => 2, 'calibrador' => 'UY']]);
        $result = $this->withSession(self::loggedInSession())->call('GET', 'calibradores/total');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertSame(2, $json['total']);
    }

    public function testObtenerWithMock(): void
    {
        $this->injectMock([['id_calibrador' => 3, 'calibrador' => 'Calibrador B', 'created_at' => null, 'updated_at' => null]]);
        $result = $this->withSession(self::loggedInSession())->call('GET', 'calibradores/obtener/3');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertSame('Calibrador B', $json['data']['calibrador']);
    }

    public function testGuardarCrearWithMock(): void
    {
        $this->injectMock([]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'calibradores/guardar', ['calibrador' => 'Calibrador Nuevo']);
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertArrayHasKey('id', $json);
    }

    public function testEliminarWithMock(): void
    {
        $this->injectMock([['id_calibrador' => 1, 'calibrador' => 'X']]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'calibradores/eliminar/1');
        $result->assertStatus(200);
        $result->assertJSONFragment(['success' => true]);
    }

    public function testGuardarValidacionFalla(): void
    {
        $result = $this->withSession(self::loggedInSession())->call('POST', 'calibradores/guardar', ['calibrador' => '']);
        $result->assertStatus(200);
        $result->assertJSONFragment(['success' => false]);
    }
}
