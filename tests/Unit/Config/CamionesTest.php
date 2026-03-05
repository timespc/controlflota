<?php

namespace Tests\Unit\Config;

use CodeIgniter\Test\CIUnitTestCase;
use Config\Camiones;

class CamionesTest extends CIUnitTestCase
{
    public function testDefaultValues(): void
    {
        $config = config(Camiones::class);
        $this->assertInstanceOf(Camiones::class, $config);
        $this->assertSame('equipos', $config->tableEquipos);
        $this->assertArrayHasKey('id_tta', $config->columnMap);
        $this->assertSame('IdTta', $config->columnMap['id_tta']);
        $this->assertContains('nacion', $config->excludeColumns);
        $this->assertSame('ttas', $config->tableTtas);
    }
}
