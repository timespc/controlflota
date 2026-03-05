<?php

namespace Tests\Feature;

use App\Models\CalibradoresModel;
use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Mocks\CalibradoresModelMock;
use Tests\Support\Mocks\UserGroupModelMock;

/**
 * Tests Feature de flujos críticos de Reportes: index y página de reporte de calibraciones.
 * Se mockea CalibradoresModel en calibraciones() para no depender de BD.
 */
class ReportesTest extends CIUnitTestCase
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
        Factories::injectMock('models', CalibradoresModel::class, new CalibradoresModelMock([]));
    }

    protected function tearDown(): void
    {
        Factories::reset('models');
        parent::tearDown();
    }

    public function testReportesIndexRequiresLogin(): void
    {
        $result = $this->call('GET', 'reportes');
        $this->assertTrue(
            in_array($result->response()->getStatusCode(), [302, 403], true),
            'Sin sesión debe redirigir o prohibir'
        );
    }

    public function testReportesIndexWithSessionReturnsOk(): void
    {
        $result = $this->withSession(self::loggedInSession())->call('GET', 'reportes');
        $result->assertStatus(200);
    }

    public function testReportesCalibracionesWithSessionReturnsOk(): void
    {
        $result = $this->withSession(self::loggedInSession())->call('GET', 'reportes/calibraciones');
        $result->assertStatus(200);
    }
}
