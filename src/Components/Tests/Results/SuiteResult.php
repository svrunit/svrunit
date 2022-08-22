<?php

namespace SVRUnit\Components\Tests\Results;

use SVRUnit\Components\Tests\TestSuite;


class SuiteResult
{

    /**
     * @var TestSuite
     */
    private $testSuite;

    /**
     * @var TestResult[]
     */
    private $testResults;


    /**
     * @param TestSuite $suite
     * @param array $results
     */
    public function __construct(TestSuite $suite, array $results)
    {
        $this->testSuite = $suite;
        $this->testResults = $results;
    }

    /**
     * @return TestSuite
     */
    public function getTestSuite(): TestSuite
    {
        return $this->testSuite;
    }

    /**
     * @return array|TestResult[]
     */
    public function getAllTestResults(): array
    {
        return $this->testResults;
    }

    /**
     * @return float
     */
    public function getTestTime(): float
    {
        $time = 0;

        foreach ($this->testResults as $result) {
            $time += $result->getTimeSeconds();
        }

        return $time;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        foreach ($this->testResults as $result) {

            if ($result->isSuccess() === false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return TestResult[]
     */
    public function getFailedTests(): array
    {
        $failed = [];

        foreach ($this->testResults as $result) {

            if ($result->isSuccess() === false) {
                $failed[] = $result;
            }
        }

        return $failed;
    }

    /**
     * @return TestResult[]
     */
    public function getPassedTests(): array
    {
        $passed = [];

        foreach ($this->testResults as $result) {

            if ($result->isSuccess()) {
                $passed[] = $result;
            }
        }

        return $passed;
    }

}
