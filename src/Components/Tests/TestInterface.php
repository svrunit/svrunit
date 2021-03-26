<?php

namespace SVRUnit\Components\Tests;

use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\Results\TestResult;


interface TestInterface
{

    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @param TestRunnerInterface $runner
     * @return TestResult
     */
    public function executeTest(TestRunnerInterface $runner) : TestResult;

}
