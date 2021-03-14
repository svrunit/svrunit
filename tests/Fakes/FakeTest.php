<?php

namespace SVRUnit\Tests\Fakes;


use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Components\Tests\TestResultInterface;

class FakeTest implements TestInterface
{

    public function getName(): string
    {
        // TODO: Implement getName() method.
    }

    public function executeTest(TestRunnerInterface $runner): TestResultInterface
    {
        // TODO: Implement executeTest() method.
    }

}
