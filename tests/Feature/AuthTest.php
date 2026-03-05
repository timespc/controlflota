<?php

namespace Tests\Feature;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * Tests de Auth: login POST, logout, vistas públicas.
 */
class AuthTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpMethods = array_diff($this->setUpMethods ?? [], ['mockSession']);
    }

    public function testLogoutRedirectsToLogin(): void
    {
        $result = $this->call('GET', 'auth/logout');
        $result->assertRedirect();
        $this->assertStringContainsString('auth/login', $result->response()->getHeaderLine('Location'));
    }

    public function testLoginPostWithInvalidCredentials(): void
    {
        $result = $this->call('POST', 'auth/login', [
            'identity' => 'noexiste@test.com',
            'password' => 'wrong',
        ]);
        $result->assertRedirect();
    }

    public function testPendientePage(): void
    {
        $result = $this->call('GET', 'auth/pendiente');
        $result->assertStatus(200);
    }

    public function testRechazadoPage(): void
    {
        $result = $this->call('GET', 'auth/rechazado');
        $result->assertStatus(200);
    }
}
