<?php

namespace Tests\Unit\Validation;

use App\Validation\CustomRules;
use CodeIgniter\Test\CIUnitTestCase;

class CustomRulesTest extends CIUnitTestCase
{
    private CustomRules $rules;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rules = new CustomRules();
    }

    public function testValidEmailMultipleEmpty(): void
    {
        $this->assertTrue($this->rules->valid_email_multiple('', null, []));
    }

    public function testValidEmailMultipleSingle(): void
    {
        $this->assertTrue($this->rules->valid_email_multiple('a@b.com', null, []));
    }

    public function testValidEmailMultipleSeveral(): void
    {
        $this->assertTrue($this->rules->valid_email_multiple('a@b.com; c@d.com', null, []));
    }

    public function testValidEmailMultipleInvalid(): void
    {
        $this->assertFalse($this->rules->valid_email_multiple('not-an-email', null, []));
    }

    public function testValidEmailMultipleOneInvalid(): void
    {
        $this->assertFalse($this->rules->valid_email_multiple('a@b.com; invalid', null, []));
    }

    /** Segmentos vacíos entre punto y coma (trim vacío) se ignoran con continue */
    public function testValidEmailMultipleConSegmentosVacios(): void
    {
        $this->assertTrue($this->rules->valid_email_multiple('a@b.com;  ; ; c@d.com', null, []));
    }
}
