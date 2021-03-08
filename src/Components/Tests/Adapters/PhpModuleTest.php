<?php

namespace SVRUnit\Components\Tests\Adapters;

use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Components\Tests\TestResult;
use SVRUnit\Components\Tests\TestResultInterface;


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
     * @return TestResultInterface
     */
    public function executeTest(TestRunnerInterface $runner) : TestResultInterface
    {
        $result = new TestResult($this, $this->expected);

        $command = 'php -m';

        $output = $runner->runTest($command);

        # do not set the huge list as output
        $result->setOutput('');

        $contains = $this->stringContains($this->expected, $output);

        $result->setSuccess($contains);

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
