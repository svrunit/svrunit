<?php

namespace SVRUnit\Components\Tests\Adapters;


use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\Results\TestResult;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Traits\StringTrait;

class FilePermissionTest implements TestInterface
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
    private $file;

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
     * @param string $file
     * @param string $expected
     * @param string $notExpected
     */
    public function __construct(string $name, string $specFile, string $file, string $expected, string $notExpected)
    {
        $this->name = $name;
        $this->specFile = $specFile;
        $this->file = $file;
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
        $command = 'stat -c %a ' . $this->file;

        $output = $runner->runTest($command);

        if (!empty($this->expected)) {
            $success = $this->containsString($this->expected, $output);
        } else {
            $success = !$this->containsString($this->notExpected, $output);
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