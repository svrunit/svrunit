<?php

namespace SVRUnit\Tests\Fakes;

use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\Results\TestResult;
use SVRUnit\Components\Tests\TestInterface;


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
     * @return TestResult
     */
    public function executeTest(TestRunnerInterface $runner): TestResult
    {
        return new TestResult(
            $this,
            true,
            1,
            '',
            ''
        );
    }

}
