<?php

namespace Tests\Feature;

use CodeIgniter\Config\Factories;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Mocks\UserGroupModelMock;

/**
 * Tests de la pantalla de login, acceso sin sesión y rutas protegidas con sesión.
 */
class AuthLoginTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    /** Sesión mínima para que el filtro/ion auth consideren al usuario logueado (en testing no se consulta BD). */
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

    public function testLoginPageIsAccessible(): void
    {
        $result = $this->call('GET', 'auth/login');
        $result->assertStatus(200);
        $body = $result->response()->getBody();
        $this->assertNotEmpty($body);
        $this->assertMatchesRegularExpression('/form|identity|password|Iniciar|Montajes/i', (string) $body);
    }

    public function testDashboardRedirectsWhenNotLoggedIn(): void
    {
        $result = $this->call('GET', 'dashboard');
        $this->assertTrue(
            in_array($result->response()->getStatusCode(), [302, 403], true),
            'Se esperaba redirección (302) o prohibido (403) al acceder a dashboard sin login'
        );
    }

    /** Con sesión válida, la ruta protegida dashboard responde 200 (CheckLogin deja pasar en testing). */
    public function testDashboardWithSessionReturnsOk(): void
    {
        $result = $this->withSession(self::loggedInSession())->call('GET', 'dashboard');
        $result->assertStatus(200);
    }

    /** Con sesión válida, GET auth/login redirige a home (Auth::login() si ya está logueado). */
    public function testLoginPageWhenLoggedInRedirectsToHome(): void
    {
        $result = $this->withSession(self::loggedInSession())->call('GET', 'auth/login');
        $result->assertRedirect();
        $location = $result->response()->getHeaderLine('Location');
        $this->assertTrue(
            strpos($location, 'auth/login') === false || parse_url($location, PHP_URL_PATH) === '/',
            'Con sesión, login debe redirigir a home, no a login'
        );
    }

    /** Ruta protegida (banderas index) con sesión responde 200. */
    public function testBanderasIndexWithSessionReturnsOk(): void
    {
        $result = $this->withSession(self::loggedInSession())->call('GET', 'banderas');
        $result->assertStatus(200);
    }
}
