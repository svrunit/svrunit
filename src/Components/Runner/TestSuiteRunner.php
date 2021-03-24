<?php

namespace SVRUnit\Components\Runner;

use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Components\Tests\TestResult;
use SVRUnit\Services\OutputWriter\OutputWriterInterface;

class TestSuiteRunner
{

    /**
     * @var array
     */
    private $tests = array();

    /**
     * @var array
     */
    private $passedTests = array();

    /**
     * @var array
     */
    private $failedTests = array();

    /**
     * @var int
     */
    private $setupTimeSeconds;


    /**
     * TestSuiteRunner constructor.
     * @param array $tests
     * @param int $setupTimeSeconds
     */
    public function __construct(array $tests, int $setupTimeSeconds)
    {
        $this->tests = $tests;
        $this->setupTimeSeconds = $setupTimeSeconds;
    }

    /**
     * @return int
     */
    public function getAllTestsCount()
    {
        return count($this->tests);
    }

    /**
     * @return int
     */
    public function getPassedTestsCount()
    {
        return count($this->passedTests);
    }

    /**
     * @return int
     */
    public function getFailedTestsCount()
    {
        return count($this->failedTests);
    }

    /**
     * @return array
     */
    public function getFailedTests()
    {
        return $this->failedTests;
    }

    /**
     * @return array
     */
    public function getAllResults()
    {
        return array_merge($this->failedTests, $this->passedTests);
    }

    /**
     * @param TestRunnerInterface $runner
     * @param OutputWriterInterface $outputWriter
     * @param bool $debugMode
     */
    public function testAll(TestRunnerInterface $runner, OutputWriterInterface $outputWriter, bool $debugMode)
    {
        $runner->setUp();
        sleep($this->setupTimeSeconds);

        $this->failedTests = array();
        $this->passedTests = array();

        /** @var TestInterface $test */
        foreach ($this->tests as $test) {

            /** @var TestResult $result */
            $result = $test->executeTest($runner);

            if ($result->isSuccess()) {
                $this->passedTests[] = $result;
            } else {
                $this->failedTests[] = $result;
            }
        }

        # teardown runner
        $runner->tearDown();
    }

}
