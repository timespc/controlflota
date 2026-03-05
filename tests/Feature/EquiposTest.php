<?php

namespace Tests\Feature;

use App\Models\BanderasModel;
use App\Models\CubiertasModel;
use App\Models\MarcasModel;
use App\Models\PaisModel;
use App\Models\TransportistasModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Mocks\BanderasModelMock;
use Tests\Support\Mocks\CubiertasModelMock;
use Tests\Support\Mocks\MarcasModelMock;
use Tests\Support\Mocks\PaisModelMock;
use Tests\Support\Mocks\TransportistasModelMock;
use Tests\Support\Mocks\UserGroupModelMock;

/**
 * Tests Feature del módulo Equipos (URL equipos): index (listado).
 * Se mockean los modelos del index para no depender de BD.
 */
class EquiposTest extends CIUnitTestCase
{
    use FeatureTestTrait;

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
        Factories::injectMock('models', 'UserGroupModel', new UserGroupModelMock());
        Factories::injectMock('models', TransportistasModel::class, new TransportistasModelMock([], []));
        Factories::injectMock('models', PaisModel::class, new PaisModelMock([]));
        Factories::injectMock('models', BanderasModel::class, new BanderasModelMock([]));
        Factories::injectMock('models', MarcasModel::class, new MarcasModelMock([]));
        Factories::injectMock('models', CubiertasModel::class, new CubiertasModelMock([]));
    }

    protected function tearDown(): void
    {
        Factories::reset('models');
        parent::tearDown();
    }

    public function testEquiposIndexRequiresLogin(): void
    {
        $result = $this->call('GET', 'equipos');
        $this->assertTrue(
            in_array($result->response()->getStatusCode(), [302, 403], true),
            'Sin sesión debe redirigir o prohibir'
        );
    }

    public function testEquiposIndexWithSessionReturnsOk(): void
    {
        $result = $this->withSession(self::loggedInSession())->call('GET', 'equipos');
        $result->assertStatus(200);
    }
}
