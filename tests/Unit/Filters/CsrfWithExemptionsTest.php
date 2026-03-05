<?php

namespace Tests\Unit\Filters;

use App\Filters\CsrfWithExemptions;
use CodeIgniter\Test\CIUnitTestCase;
use Config\App;
use CodeIgniter\HTTP\URI;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\UserAgent;

class CsrfWithExemptionsTest extends CIUnitTestCase
{
    public function testBeforeInTestingReturnsNull(): void
    {
        $filter  = new CsrfWithExemptions();
        $config  = new App();
        $uri     = new URI('http://localhost/banderas/listar');
        $request = new IncomingRequest($config, $uri, 'php://input', new UserAgent());
        $result  = $filter->before($request, null);
        $this->assertNull($result);
    }
}
