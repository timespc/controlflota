<?php

namespace Tests\Unit\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use App\Controllers\BaseController;

class BaseControllerTest extends CIUnitTestCase
{
    public function testHelpersLoaded(): void
    {
        $controller = new class () extends BaseController {
            public function getHelpers(): array
            {
                return $this->helpers;
            }
        };
        $helpers = $controller->getHelpers();
        $this->assertContains('auth', $helpers);
        $this->assertContains('json_response', $helpers);
    }
}
