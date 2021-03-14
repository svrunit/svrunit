<?php

namespace SVRUnit\Tests\Components\Tests;

use PHPUnit\Framework\TestCase;
use SVRUnit\Components\Tests\TestResult;
use SVRUnit\Tests\Fakes\FakeTest;

class TestResultTest extends TestCase
{

    /**
     * This test verifies that our success property
     * is correctly set and returned.
     */
    public function testSuccessProperty()
    {
        $r = new TestResult(new FakeTest(), '');
        $r->setSuccess(true);

        $this->assertEquals(true, $r->isSuccess());
    }

}
