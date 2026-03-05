<?php

namespace Tests\Feature;

use App\Models\MarcasSensorModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Mocks\MarcasSensorModelMock;

/** Tests Feature CRUD MarcasSensor con modelo mockeado. */
class MarcasSensorTest extends CIUnitTestCase
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
        Factories::injectMock('models', MarcasSensorModel::class, new MarcasSensorModelMock($items));
    }

    public function testListarWithMock(): void
    {
        $this->injectMock([['id_marca_sensor' => 1, 'marca' => 'Sensor A', 'ult_actualiz' => '2025-01-01 00:00:00']]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'marcas-sensor/listar');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertCount(1, $json['data']);
        $this->assertSame('Sensor A', $json['data'][0]['marca']);
    }

    public function testTotalWithMock(): void
    {
        $this->injectMock([['id_marca_sensor' => 1, 'marca' => 'AR'], ['id_marca_sensor' => 2, 'marca' => 'UY']]);
        $result = $this->withSession(self::loggedInSession())->call('GET', 'marcas-sensor/total');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertSame(2, $json['total']);
    }

    public function testObtenerWithMock(): void
    {
        $this->injectMock([['id_marca_sensor' => 3, 'marca' => 'Sensor B', 'created_at' => null, 'updated_at' => null]]);
        $result = $this->withSession(self::loggedInSession())->call('GET', 'marcas-sensor/obtener/3');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertSame('Sensor B', $json['data']['marca']);
    }

    public function testGuardarCrearWithMock(): void
    {
        $this->injectMock([]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'marcas-sensor/guardar', ['marca' => 'Sensor Nuevo']);
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertArrayHasKey('id', $json);
    }

    public function testEliminarWithMock(): void
    {
        $this->injectMock([['id_marca_sensor' => 1, 'marca' => 'X']]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'marcas-sensor/eliminar/1');
        $result->assertStatus(200);
        $result->assertJSONFragment(['success' => true]);
    }

    public function testGuardarValidacionFalla(): void
    {
        $result = $this->withSession(self::loggedInSession())->call('POST', 'marcas-sensor/guardar', ['marca' => '']);
        $result->assertStatus(200);
        $result->assertJSONFragment(['success' => false]);
    }
}
