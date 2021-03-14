<?php

namespace SVRUnit\Tests\Fakes;

use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Components\Tests\TestResult;
use SVRUnit\Components\Tests\TestResultInterface;

class FakeTest implements TestInterface
{

    /**
     * @return string
     */
    public function getName(): string
    {
        return "Fake Test";
    }

    /**
     * @param TestRunnerInterface $runner
     * @return TestResultInterface
     */
    public function executeTest(TestRunnerInterface $runner): TestResultInterface
    {
        $result = new TestResult($this, '');
        $result->setSuccess(true);

        return $result;
    }

}
