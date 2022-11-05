<?php

namespace SVRUnit\Tests\Fakes;

use SVRUnit\Components\Runner\TestRunnerInterface;

class FakeTestRunner implements TestRunnerInterface
{

    /**
     * @var string
     */
    private $ouput;

    /**
     * @var array
     */
    private $runCommands;


    /**
     * @param string $output
     */
    public function __construct(string $output = '')
    {
        $this->ouput = $output;

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

        return $this->ouput;
    }

    public function tearDown(): void
    {
        // TODO: Implement tearDown() method.
    }

}