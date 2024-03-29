<?php

namespace SVRUnit\Components\Tests\Adapters;


use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\Results\TestResult;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Traits\StringTrait;


class FileContentTest implements TestInterface
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
    private $filename;

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
     * @param string $filename
     * @param string $expected
     * @param string $notExpected
     */
    public function __construct(string $name, string $specFile, string $filename, string $expected, string $notExpected)
    {
        $this->name = $name;
        $this->specFile = $specFile;
        $this->filename = $filename;
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
     * @throws \Exception
     */
    public function executeTest(TestRunnerInterface $runner): TestResult
    {
        if ($this->filename === '') {
            throw new \Exception("FileContent test has an invalid configuration without a file");
        }

        if ($this->expected === '' && $this->notExpected === '') {
            throw new \Exception("FileContent test has an invalid configuration without an expected or unexpected value");
        }

        $output = $runner->runTest('cat ' . $this->filename);

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
