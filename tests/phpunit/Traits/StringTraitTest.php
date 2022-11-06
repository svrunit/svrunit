<?php

namespace SVRUnit\Tests\Traits;

use PHPUnit\Framework\TestCase;
use SVRUnit\Traits\StringTrait;

class StringTraitTest extends TestCase
{

    use StringTrait;

    /**
     * @return void
     */
    public function testContains(): void
    {
        $contains = $this->containsString('svrunit', 'this is a svrunit test');

        $this->assertEquals(true, $contains);
    }

    /**
     * @return void
     */
    public function testNotContains(): void
    {
        $contains = $this->containsString('svrunit', 'this is a test');

        $this->assertEquals(false, $contains);
    }

}
