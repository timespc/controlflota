<?php

namespace Tests\Unit\Config;

use CodeIgniter\Test\CIUnitTestCase;
use Config\CrudValidation;

class CrudValidationTest extends CIUnitTestCase
{
    public function testRulesExist(): void
    {
        $config = config(CrudValidation::class);
        $this->assertInstanceOf(CrudValidation::class, $config);
        $this->assertArrayHasKey('bandera', $config->rules);
        $this->assertArrayHasKey('marca', $config->rules);
        $this->assertSame('required|min_length[1]|max_length[255]', $config->rules['bandera']['bandera']);
    }
}
