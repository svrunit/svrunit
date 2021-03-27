<?php

namespace SVRUnit\Components\Tests\Adapters;

use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\Results\TestResult;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Traits\StringTrait;


class PhpModuleTest implements TestInterface
{

    use StringTrait;


    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $expected;


    /**
     * PhpModuleTest constructor.
     * @param string $name
     * @param string $expectedModule
     */
    public function __construct(string $name, string $expectedModule)
    {
        $this->name = $name;
        $this->expected = $expectedModule;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param TestRunnerInterface $runner
     * @return TestResult
     */
    public function executeTest(TestRunnerInterface $runner): TestResult
    {
        $command = 'php -m';

        $output = $runner->runTest($command);

        $success = $this->stringContains($this->expected, $output);

        if ($success) {
            $output = 'Module exists';
        } else {
            $output = 'Module does not exist';
        }

        return new TestResult(
            $this,
            $success,
            1,
            $this->expected,
            $output
        );
    }

}
