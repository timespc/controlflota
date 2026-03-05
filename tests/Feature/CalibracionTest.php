<?php

namespace Tests\Feature;

use App\Models\CalibracionModel;
use App\Models\ReglasModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Mocks\CalibracionModelMock;
use Tests\Support\Mocks\ReglasModelMock;
use Tests\Support\Mocks\UserGroupModelMock;

/**
 * Tests Feature de flujos críticos de Calibración: index, listar, obtener.
 * Se mockean ReglasModel, CalibracionModel y UserGroupModel (sidebar) para no depender de BD.
 */
class CalibracionTest extends CIUnitTestCase
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
    }

    protected function tearDown(): void
    {
        Factories::reset('models');
        parent::tearDown();
    }

    public function testCalibracionIndexRequiresLogin(): void
    {
        $result = $this->call('GET', 'calibracion');
        $this->assertTrue(
            in_array($result->response()->getStatusCode(), [302, 403], true),
            'Sin sesión debe redirigir o prohibir'
        );
    }

    public function testCalibracionIndexWithSessionReturnsOk(): void
    {
        Factories::injectMock('models', ReglasModel::class, new ReglasModelMock([
            ['id_regla' => 1, 'numero_regla' => '1', 'habilitada' => 1],
        ]));
        $result = $this->withSession(self::loggedInSession())->call('GET', 'calibracion');
        $result->assertStatus(200);
    }

    public function testCalibracionListarWithMock(): void
    {
        $data = [
            ['id_calibracion' => 1, 'patente' => 'AA000AA', 'fecha_calib' => '2025-01-01'],
        ];
        Factories::injectMock('models', CalibracionModel::class, new CalibracionModelMock($data, []));
        $result = $this->withSession(self::loggedInSession())->call('POST', 'calibracion/listar');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertArrayHasKey('data', $json);
        $this->assertCount(1, $json['data']);
        $this->assertSame('AA000AA', $json['data'][0]['patente']);
    }

    public function testCalibracionObtenerWithMock(): void
    {
        // Sin 'patente' para que enriquecerParaVista no consulte EquiposModel/Marcas/Transportistas
        $cal = [
            'id_calibracion' => 5,
            'patente'        => '',
            'fecha_calib'    => '2025-02-01',
        ];
        Factories::injectMock('models', CalibracionModel::class, new CalibracionModelMock([], [5 => $cal]));
        $result = $this->withSession(self::loggedInSession())->call('GET', 'calibracion/obtener/5');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertTrue($json['success']);
        $this->assertSame('2025-02-01', $json['data']['fecha_calib'] ?? null);
    }

    public function testCalibracionObtenerInexistenteWithMock(): void
    {
        Factories::injectMock('models', CalibracionModel::class, new CalibracionModelMock([], []));
        $result = $this->withSession(self::loggedInSession())->call('GET', 'calibracion/obtener/999');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertFalse($json['success']);
        $this->assertStringContainsString('no encontrada', $json['message'] ?? '');
    }

    public function testCalibracionObtenerSinIdDevuelveError(): void
    {
        Factories::injectMock('models', CalibracionModel::class, new CalibracionModelMock([], []));
        $result = $this->withSession(self::loggedInSession())->call('GET', 'calibracion/obtener/0');
        $result->assertStatus(200);
        $json = json_decode($result->response()->getBody(), true);
        $this->assertFalse($json['success']);
        $this->assertStringContainsString('ID no proporcionado', $json['message'] ?? '');
    }
}
