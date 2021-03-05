<?php

namespace SVRUnit\Components\Tests;

use SVRUnit\Components\Runner\TestRunnerInterface;


interface TestInterface
{

    /**
     * @return string
     */
    public function getName() : string;

    /**
     * @param TestRunnerInterface $runner
     * @return TestResultInterface
     */
    public function executeTest(TestRunnerInterface $runner) : TestResultInterface;

}
