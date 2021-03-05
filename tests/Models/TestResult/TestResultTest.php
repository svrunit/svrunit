<?php

/**
 * Class TestResultTest
 * @author Christian Dangl
 */
class TestResultTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @test
     * @author Christian Dangl
     */
    public function setSuccess_true()
    {
        $r = new TestResult(new FakeTest(), "");
        $r->setSuccess(true);

        $this->assertEquals(true, $r->isSuccess());
    }

    /**
     * @test
     * @author Christian Dangl
     */
    public function setSuccess_false()
    {
        $r = new TestResult(new FakeTest(), "");
        $r->setSuccess(false);

        $this->assertEquals(false, $r->isSuccess());
    }

    /**
     * @test
     * @author Christian Dangl
     */
    public function setOutput()
    {
        $r = new TestResult(new FakeTest(), "");
        $r->setOutput('test-output');

        $this->assertEquals('test-output', $r->getOutput());
    }

}
