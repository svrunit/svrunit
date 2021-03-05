<?php

namespace SVRUnit\Components\Tests\Adapters;

use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Components\Tests\TestResult;
use SVRUnit\Components\Tests\TestResultInterface;


class CommandTest implements TestInterface
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $command;

    /**
     * @var string
     */
    private $expected;


    /**
     * CommandTest constructor.
     * @param string $name
     * @param string $command
     * @param string $expected
     */
    public function __construct(string $name, string $command, string $expected)
    {
        $this->name = $name;
        $this->command = $command;
        $this->expected = $expected;
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
     * @return TestResultInterface
     */
    public function executeTest(TestRunnerInterface $runner): TestResultInterface
    {
        $result = new TestResult($this, $this->expected);

        $output = $runner->runTest($this->command);

        $containsText = $this->stringContains($this->expected, $output);

        $result->setSuccess($containsText);
        $result->setOutput($output);

        return $result;
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
