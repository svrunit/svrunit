<?php

namespace SVRUnit\Components\Tests\Adapters;

use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\Results\TestResult;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Traits\StringTrait;


class CommandTest implements TestInterface
{

    use StringTrait;


    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $specFile;

    /**
     * @var string
     */
    private $command;

    /**
     * @var string
     */
    private $expected;

    /**
     * @var string
     */
    private $notExpected;


    /**
     * @param string $name
     * @param string $specFile
     * @param string $command
     * @param string $expected
     * @param string $notExpected
     */
    public function __construct(string $name, string $specFile, string $command, string $expected, string $notExpected)
    {
        $this->name = $name;
        $this->specFile = $specFile;
        $this->command = $command;
        $this->expected = $expected;
        $this->notExpected = $notExpected;
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
        $output = $runner->runTest($this->command);

        # remove all new lines
        # and also trim the output for a better comparison
        $output = str_replace("\r\n", '', $output);
        $output = str_replace("\r", '', $output);
        $output = str_replace("\n", '', $output);
        $output = trim($output);


        if ($this->expected != "") {
            $success = $this->stringContains($this->expected, $output);
        } else {
            $success = !$this->stringContains($this->notExpected, $output);
        }

        return new TestResult(
            $this,
            $this->specFile,
            $success,
            1,
            $this->expected,
            $output
        );
    }

}
