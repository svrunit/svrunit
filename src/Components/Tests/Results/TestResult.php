<?php

namespace SVRUnit\Components\Tests\Results;

use SVRUnit\Components\Tests\TestInterface;


class TestResult
{

    /**
     * @var TestInterface
     */
    private $test;

    /**
     * @var bool
     */
    private $success;

    /**
     * @var int
     */
    private $assertions;

    /**
     * @var string
     */
    private $expected;

    /**
     * @var string
     */
    private $actual;

    /**
     * @var int
     */
    private $time;


    /**
     * @param TestInterface $test
     * @param bool $success
     * @param int $assertions
     * @param string $expected
     * @param string $actual
     */
    public function __construct(TestInterface $test, bool $success, int $assertions, string $expected, string $actual)
    {
        $this->test = $test;
        $this->success = $success;
        $this->assertions = $assertions;
        $this->expected = $expected;
        $this->actual = $actual;

        $this->time = 0;
    }

    /**
     * @return TestInterface
     */
    public function getTest(): TestInterface
    {
        return $this->test;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * @return int
     */
    public function getAssertions(): int
    {
        return $this->assertions;
    }

    /**
     * @return string
     */
    public function getExpected(): string
    {
        return $this->expected;
    }

    /**
     * @return string
     */
    public function getActual(): string
    {
        return $this->actual;
    }

    /**
     * @return int
     */
    public function getErrors(): int
    {
        if ($this->success) {
            return 0;
        }

        return 1;
    }

    /**
     * @return int
     */
    public function getTime(): int
    {
        return $this->time;
    }

    /**
     * @param int $time
     */
    public function setTime(int $time): void
    {
        $this->time = $time;
    }

}
