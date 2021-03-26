<?php

namespace SVRUnit\Components\Tests\Results;

class RunResult
{

    /**
     * @var SuiteResult[]
     */
    private $testSuiteResults;


    /**
     */
    public function __construct()
    {
        $this->testSuiteResults = array();
    }

    /**
     * @param SuiteResult $result
     */
    public function addSuiteResult(SuiteResult $result): void
    {
        $this->testSuiteResults[] = $result;
    }

    /**
     * @return SuiteResult[]
     */
    public function getTestSuiteResults(): array
    {
        return $this->testSuiteResults;
    }

    /**
     * @return int
     */
    public function getTestTime(): int
    {
        $time = 0;

        foreach ($this->testSuiteResults as $suiteResult) {
            $time += $suiteResult->getTestTime();
        }

        return $time;
    }

    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        foreach ($this->testSuiteResults as $suiteResult) {

            if ($suiteResult->hasErrors()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return int
     */
    public function getTestCount(): int
    {
        $count = 0;

        foreach ($this->testSuiteResults as $suiteResult) {
            $count .= count($suiteResult->getAllTestResults());
        }

        return $count;
    }

    /**
     * @return int
     */
    public function getErrorCount(): int
    {
        $count = 0;

        foreach ($this->testSuiteResults as $suiteResult) {
            $count .= count($suiteResult->getFailedTests());
        }

        return $count;
    }

}
