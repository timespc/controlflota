<?php

namespace Tests\Feature;

use App\Models\ReglasModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Mocks\ReglasModelMock;
use Tests\Support\Mocks\UserGroupModelMock;

/** Tests Feature CRUD Reglas con modelo mockeado. */
class ReglasTest extends CIUnitTestCase
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
        Factories::injectMock('models', ReglasModel::class, new ReglasModelMock($items));
        Factories::injectMock('models', 'UserGroupModel', new UserGroupModelMock());
    }

    public function testListarWithMock(): void
    {
        $this->injectMock([['id_regla' => 1, 'numero_regla' => 'Regla 1', 'ult_actualiz' => '2025-01-01 00:00:00']]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'reglas/listar');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertCount(1, $json['data']);
        $this->assertSame('Regla 1', $json['data'][0]['numero_regla']);
    }

    public function testTotalWithMock(): void
    {
        $this->injectMock([['id_regla' => 1, 'numero_regla' => 'AR'], ['id_regla' => 2, 'numero_regla' => 'UY']]);
        $result = $this->withSession(self::loggedInSession())->call('GET', 'reglas/total');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertSame(2, $json['total']);
    }

    public function testObtenerWithMock(): void
    {
        $this->injectMock([['id_regla' => 3, 'numero_regla' => 'Regla 2', 'created_at' => null, 'updated_at' => null]]);
        $result = $this->withSession(self::loggedInSession())->call('GET', 'reglas/obtener/3');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertSame('Regla 2', $json['data']['numero_regla']);
    }

    public function testGuardarCrearWithMock(): void
    {
        $this->injectMock([]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'reglas/guardar', ['numero_regla' => 'Regla Nueva']);
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertArrayHasKey('id', $json);
    }

    public function testEliminarWithMock(): void
    {
        $this->injectMock([['id_regla' => 1, 'numero_regla' => 'X']]);
        $result = $this->withSession(self::loggedInSession())->call('POST', 'reglas/eliminar/1');
        $result->assertStatus(200);
        $result->assertJSONFragment(['success' => true]);
    }

    public function testGuardarValidacionFalla(): void
    {
        $result = $this->withSession(self::loggedInSession())->call('POST', 'reglas/guardar', ['numero_regla' => '']);
        $result->assertStatus(200);
        $result->assertJSONFragment(['success' => false]);
    }
}
