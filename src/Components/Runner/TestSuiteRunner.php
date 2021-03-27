<?php

namespace SVRUnit\Components\Runner;

use SVRUnit\Components\Tests\Results\SuiteResult;
use SVRUnit\Components\Tests\Results\TestResult;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Components\Tests\TestSuite;
use SVRUnit\Services\Stopwatch\Stopwatch;


class TestSuiteRunner
{

    /**
     * @var TestSuite
     */
    private $suite;

    /**
     * @var array
     */
    private $tests;

    /**
     * @var int
     */
    private $setupTimeSeconds;

    /**
     * @var TestResult[]
     */
    private $allResults;

    /**
     * @var Stopwatch
     */
    private $stopwatch;


    /**
     * @param TestSuite $suite
     * @param array $tests
     * @param int $setupTimeSeconds
     */
    public function __construct(TestSuite $suite, array $tests, int $setupTimeSeconds)
    {
        $this->suite = $suite;
        $this->tests = $tests;
        $this->setupTimeSeconds = $setupTimeSeconds;

        $this->allResults = [];
        $this->stopwatch = new Stopwatch();
    }

    /**
     * @return SuiteResult
     */
    public function getResults(): SuiteResult
    {
        return new SuiteResult($this->suite, $this->allResults);
    }

    /**
     * @param TestRunnerInterface $runner
     */
    public function runTestSuite(TestRunnerInterface $runner)
    {
        # first reset our previous results, if existing
        $this->allResults = [];


        # invoke the setup phase
        $runner->setUp();

        # wait the configured bootup time for our test suite
        sleep($this->setupTimeSeconds);


        /** @var TestInterface $test */
        foreach ($this->tests as $test) {

            # start our stopwatch
            # before we run that test
            $this->stopwatch->start();

            # now execute our anonymize test
            # ...whatever it is...
            /** @var TestResult $result */
            $result = $test->executeTest($runner);

            # stop our timing, so we now how much
            # time that test took
            $this->stopwatch->stop();

            # ...and finally set the time
            # for our test run
            $result->setTime($this->stopwatch->getMilliseconds());


            $this->allResults[] = $result;
        }

        # last but not least,
        # invoke the teardown function
        $runner->tearDown();
    }

}
