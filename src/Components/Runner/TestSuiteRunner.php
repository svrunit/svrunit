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
     * @var bool
     */
    private $stopOnErrors;


    /**
     * TestSuiteRunner constructor.
     * @param TestSuite $suite
     * @param array $tests
     * @param int $setupTimeSeconds
     * @param bool $stopOnErrors
     */
    public function __construct(TestSuite $suite, array $tests, int $setupTimeSeconds, bool $stopOnErrors)
    {
        $this->suite = $suite;
        $this->tests = $tests;
        $this->setupTimeSeconds = $setupTimeSeconds;
        $this->stopOnErrors = $stopOnErrors;

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
            $result = $test->executeTest($runner);

            # stop our timing, so we now how much
            # time that test took
            $this->stopwatch->stop();

            # ...and finally set the time
            # for our test run
            $seconds = $this->stopwatch->getMilliseconds() / 1000.0;
            $result->setTimeSeconds($seconds);

            $this->allResults[] = $result;

            # if we should stop on errors
            # then quit our test run now
            if (!$result->isSuccess() && $this->stopOnErrors) {
                break;
            }
        }

        # last but not least,
        # invoke the teardown function
        $runner->tearDown();
    }

}
