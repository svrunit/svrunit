<?php

namespace SVRUnit\Components\Tests\Adapters;


use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\Results\TestResult;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Traits\StringTrait;


class FileExistsTest implements TestInterface
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
     * @var bool
     */
    private $expected;


    /**
     * @param string $name
     * @param string $specFile
     * @param string $file
     * @param bool $expected
     */
    public function __construct(string $name, string $specFile, string $file, bool $expected)
    {
        $this->name = $name;
        $this->specFile = $specFile;
        $this->file = $file;
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
        if ($this->file === '') {
            throw new \Exception("FileExists test has an invalid configuration without a file");
        }

        $command = '[ -f ' . $this->file . ' ] && echo svrunit-file-exists || echo svrunit-file-not-existing';

        $output = $runner->runTest($command);

        $isExisting = $this->containsString('svrunit-file-exists', $output);

        if ($this->expected) {
            $success = $isExisting;
        } else {
            $success = !$isExisting;
        }

        if ($this->expected) {
            $expectedString = 'file should exist';
        } else {
            $expectedString = 'file should not exist';
        }

        if ($isExisting) {
            $output = 'file existing';
        } else {
            $output = 'file not existing';
        }

        return new TestResult(
            $this,
            $this->specFile,
            $success,
            1,
            $expectedString,
            $output
        );
    }

}



