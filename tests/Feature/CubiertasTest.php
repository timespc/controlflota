<?php

namespace Tests\Feature;

use App\Models\CubiertasModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Mocks\CubiertasModelMock;

/** Tests Feature CRUD Cubiertas con modelo mockeado. */
class CubiertasTest extends CIUnitTestCase
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
        Factories::injectMock('models', CubiertasModel::class, new CubiertasModelMock($items));
    }

    public function testListarWithMock(): void
    {
        $this->injectMock([['id_cubierta' => 1, 'medida' => '20m', 'ult_actualiz' => '2025-01-01']]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'cubiertas/listar');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertCount(1, $json['data']);
        $this->assertSame('20m', $json['data'][0]['medida']);
    }

    public function testTotalWithMock(): void
    {
        $this->injectMock([['id_cubierta' => 1, 'medida' => 'A'], ['id_cubierta' => 2, 'medida' => 'B']]);
        $result = $this->withSession(self::loggedInSession())->call('GET', 'cubiertas/total');
        $result->assertStatus(200);
        $this->assertSame(2, json_decode($result->response()->getBody(), true)['total']);
    }

    public function testObtenerWithMock(): void
    {
        $this->injectMock([['id_cubierta' => 5, 'medida' => '30m', 'created_at' => null, 'updated_at' => null]]);
        $result = $this->withSession(self::loggedInSession())->call('GET', 'cubiertas/obtener/5');
        $result->assertStatus(200);
        $this->assertSame('30m', json_decode($result->response()->getBody(), true)['data']['medida']);
    }

    public function testGuardarCrearWithMock(): void
    {
        $this->injectMock([]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'cubiertas/guardar', ['medida' => '25m']);
        $result->assertStatus(200);
        $this->assertArrayHasKey('id', json_decode($result->response()->getBody(), true));
    }

    public function testEliminarWithMock(): void
    {
        $this->injectMock([['id_cubierta' => 1, 'medida' => 'X']]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'cubiertas/eliminar/1');
        $result->assertStatus(200);
        $result->assertJSONFragment(['success' => true]);
    }

    public function testGuardarValidacionFalla(): void
    {
        $result = $this->withSession(self::loggedInSession())->call('POST', 'cubiertas/guardar', ['medida' => '']);
        $result->assertStatus(200);
        $result->assertJSONFragment(['success' => false]);
    }
}
