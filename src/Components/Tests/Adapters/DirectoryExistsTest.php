<?php

namespace SVRUnit\Components\Tests\Adapters;

use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\Results\TestResult;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Traits\StringTrait;


class DirectoryExistsTest implements TestInterface
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
    private $directory;

    /**
     * @var bool
     */
    private $expected;


    /**
     * @param string $name
     * @param string $specFile
     * @param string $directory
     * @param bool $expected
     */
    public function __construct(string $name, string $specFile, string $directory, bool $expected)
    {
        $this->name = $name;
        $this->specFile = $specFile;
        $this->directory = $directory;
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
     * @return TestResult
     * @throws \Exception
     */
    public function executeTest(TestRunnerInterface $runner): TestResult
    {
        if ($this->directory === '') {
            throw new \Exception("DirectoryExists test has an invalid configuration without a directory");
        }

        $command = '[ -d ' . $this->directory . ' ] && echo yes || echo no';

        $output = $runner->runTest($command);

        $isExisting = $this->containsString('yes', $output);

        if ($this->expected) {
            $isSuccess = $isExisting;
        } else {
            $isSuccess = !$isExisting;
        }

        if ($this->expected) {
            $expectedString = 'directory should exist: ' . $this->directory;
        } else {
            $expectedString = 'directory should not exist: ' . $this->directory;
        }

        if ($isExisting) {
            $output = 'directory existing';
        } else {
            $output = 'directory not existing';
        }

        return new TestResult(
            $this,
            $this->specFile,
            $isSuccess,
            1,
            $expectedString,
            $output
        );
    }

}
