<?php

namespace SVRUnit\Components\Tests\Adapters;


use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\Results\TestResult;
use SVRUnit\Components\Tests\TestInterface;


class FileExistsTest implements TestInterface
{

    /**
     * @var string
     */
    private $name;

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
     * @param string $file
     * @param bool $expected
     */
    public function __construct(string $name, string $file, bool $expected)
    {
        $this->name = $name;
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
     */
    public function executeTest(TestRunnerInterface $runner): TestResult
    {
        $command = '[ -f ' . $this->file . ' ] && echo svrunit-file-exists || echo svrunit-file-not-existing';

        $output = $runner->runTest($command);

        $isExisting = $this->stringContains('svrunit-file-exists', $output);

        if ($this->expected) {
            $success = $isExisting;
        } else {
            $success = !$isExisting;
        }

        if ($isExisting) {
            $output = 'file existing';
        } else {
            $output = 'file not existing';
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



