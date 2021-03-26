<?php

namespace SVRUnit\Components\Tests\Adapters;

use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\Results\TestResult;
use SVRUnit\Components\Tests\TestInterface;


class PhpModuleTest implements TestInterface
{

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

    /**
     * @param $expected
     * @param $text
     * @return bool
     */
    private function stringContains($expected, $text): bool
    {
        if (strpos($text, $expected) !== false) {
            return true;
        }
        return false;
    }

}
