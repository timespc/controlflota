<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Tests del Dashboard (acceso sin sesión).
 */
class DashboardTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpMethods = array_diff($this->setUpMethods ?? [], ['mockSession']);
    }

    public function testDashboardWithoutLoginRedirects(): void
    {
        $result = $this->call('GET', 'dashboard');
        $this->assertTrue(
            in_array($result->response()->getStatusCode(), [302, 403], true),
            'Sin sesión debe redirigir o prohibir'
        );
    }
}
