<?php

namespace SVRUnit\Components\Tests;


class TestResult implements TestResultInterface
{

    /**
     * @var bool
     */
    private $success;

    /**
     * @var TestInterface
     */
    private $test;

    /**
     * @var string
     */
    private $expected;

    /**
     * @var string
     */
    private $output;


    /**
     * TestResult constructor.
     * @param TestInterface $test
     * @param string $expected
     */
    public function __construct(TestInterface $test, string $expected)
    {
        $this->test = $test;
        $this->expected = $expected;

        $this->success = false;
    }

    /**
     * @param bool $success
     */
    public function setSuccess(bool $success): void
    {
        $this->success = $success;
    }

    /**
     * @return bool
     */
    public function isSuccess() : bool
    {
        return $this->success;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }

    /**
     * @param string $output
     */
    public function setOutput(string $output): void
    {
        $this->output = $output;
    }

    /**
     * @return TestInterface
     */
    public function getTest() : TestInterface
    {
        return $this->test;
    }

    /**
     * @return string
     */
    public function getExpected() : string
    {
        return $this->expected;
    }

}
