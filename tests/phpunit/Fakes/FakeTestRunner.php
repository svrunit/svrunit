<?php

namespace SVRUnit\Tests\Fakes;

use SVRUnit\Components\Runner\TestRunnerInterface;

class FakeTestRunner implements TestRunnerInterface
{

    /**
     * @var array
     */
    private $runCommands;


    /**
     *
     */
    public function __construct()
    {
        $this->runCommands = [];
    }

    /**
     * @return array
     */
    public function getRunCommands(): array
    {
        return $this->runCommands;
    }


    public function setUp(): void
    {
        // TODO: Implement setUp() method.
    }

    public function runTest(string $command): string
    {
        $this->runCommands[] = $command;

        return '';
    }

    public function tearDown(): void
    {
        // TODO: Implement tearDown() method.
    }

}