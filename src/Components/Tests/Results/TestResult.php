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
     * @var string
     */
    private $className;

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
     * @var float
     */
    private $timeSeconds;


    /**
     * @param TestInterface $test
     * @param string $className
     * @param bool $success
     * @param int $assertions
     * @param string $expected
     * @param string $actual
     */
    public function __construct(TestInterface $test, string $className, bool $success, int $assertions, string $expected, string $actual)
    {
        $this->test = $test;
        $this->className = $className;
        $this->success = $success;
        $this->assertions = $assertions;
        $this->expected = $expected;
        $this->actual = $actual;

        $this->timeSeconds = 0;
    }

    /**
     * @return TestInterface
     */
    public function getTest(): TestInterface
    {
        return $this->test;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
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
     * @return float
     */
    public function getTimeSeconds(): float
    {
        return $this->timeSeconds;
    }

    /**
     * @param float $time
     */
    public function setTimeSeconds(float $time): void
    {
        $this->timeSeconds = $time;
    }

}
