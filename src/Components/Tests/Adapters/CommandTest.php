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
    private $setupCommand;

    /**
     * @var string
     */
    private $tearDownCommand;

    /**
     * @var string
     */
    private $expected;

    /**
     * @var string
     */
    private $notExpected;

    /**
     * @var array<mixed>
     */
    private $expectedAnd;

    /**
     * @var array<mixed>
     */
    private $expectedOr;


    /**
     * @param string $name
     * @param string $specFile
     * @param string $command
     * @param string $expected
     * @param array<mixed> $expectedAnd
     * @param array<mixed> $expectedOr
     * @param string $notExpected
     * @param string $setupCommand
     * @param string $tearDownCommand
     */
    public function __construct(string $name, string $specFile, string $command, string $expected, array $expectedAnd, array $expectedOr, string $notExpected, string $setupCommand, string $tearDownCommand)
    {
        $this->name = $name;
        $this->specFile = $specFile;
        $this->command = $command;
        $this->expected = $expected;
        $this->expectedAnd = $expectedAnd;
        $this->expectedOr = $expectedOr;
        $this->notExpected = $notExpected;
        $this->setupCommand = $setupCommand;
        $this->tearDownCommand = $tearDownCommand;
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
     * @throws \Exception
     */
    public function executeTest(TestRunnerInterface $runner): TestResult
    {
        if ($this->expected === '' && $this->notExpected === '' && count($this->expectedAnd) === 0 && count($this->expectedOr) === 0) {
            throw new \Exception("Command test has an invalid configuration without an expected value: " . $this->name);
        }

        if ($this->setupCommand !== '') {
            $runner->runTest($this->setupCommand);
        }

        $output = $runner->runTest($this->command);

        if ($this->tearDownCommand !== '') {
            $runner->runTest($this->tearDownCommand);
        }

        # remove all new lines
        # and also trim the output for a better comparison
        $output = str_replace("\r\n", '', $output);
        $output = str_replace("\r", '', $output);
        $output = str_replace("\n", '', $output);
        $output = trim($output);

        $expectedText = '';

        $success = false;

        if ($this->expected != "") {
            $expectedText = 'Should contain: ' . $this->expected;

            $success = $this->containsString($this->expected, $output);
        } else if (count($this->expectedAnd) > 0) {

            $expectedText = 'Should contain ALL of these: ' . implode(', ', $this->expectedAnd);

            $allFound = true;
            # ALL conditions need to be met
            foreach ($this->expectedAnd as $condition) {
                if (!$this->containsString($condition, $output)) {
                    $allFound = false;
                    break;
                }
            }

            $success = $allFound;

        } else if (count($this->expectedOr) > 0) {

            $expectedText = 'Should contain 1 of these: ' . implode(', ', $this->expectedAnd);

            # 1 CONDITION needs to be met
            foreach ($this->expectedOr as $condition) {
                if ($this->containsString($condition, $output)) {
                    $success = true;
                    break;
                }
            }

        } else {
            $expectedText = 'Should not contain: ' . $this->notExpected;
            $success = !$this->containsString($this->notExpected, $output);
        }

        return new TestResult(
            $this,
            $this->specFile,
            $success,
            1,
            $expectedText,
            $output
        );
    }

}
