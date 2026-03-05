<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Tests del helper json_response()
 */
class JsonResponseTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('json_response');
    }

    public function testJsonResponseSuccessWithData(): void
    {
        $out = json_response(true, ['data' => [1, 2, 3]]);
        $this->assertIsArray($out);
        $this->assertTrue($out['success']);
        $this->assertArrayHasKey('data', $out);
        $this->assertSame([1, 2, 3], $out['data']);
    }

    public function testJsonResponseFailureWithMessage(): void
    {
        $out = json_response(false, ['message' => 'Error de prueba']);
        $this->assertFalse($out['success']);
        $this->assertSame('Error de prueba', $out['message']);
    }

    public function testJsonResponseOnlyAllowedKeys(): void
    {
        $out = json_response(true, [
            'message' => 'OK',
            'data'    => ['id' => 1],
            'extra'   => 'ignored',
        ]);
        $this->assertArrayHasKey('message', $out);
        $this->assertArrayHasKey('data', $out);
        $this->assertArrayNotHasKey('extra', $out);
    }

    public function testJsonResponseWithErrors(): void
    {
        $errors = ['campo' => 'El campo es requerido'];
        $out    = json_response(false, ['message' => 'Validación', 'errors' => $errors]);
        $this->assertArrayHasKey('errors', $out);
        $this->assertSame($errors, $out['errors']);
    }
}
