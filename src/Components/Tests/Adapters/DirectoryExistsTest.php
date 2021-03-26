<?php

namespace SVRUnit\Components\Tests\Adapters;

use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\Results\TestResult;
use SVRUnit\Components\Tests\TestInterface;


class DirectoryExistsTest implements TestInterface
{

    /**
     * @var string
     */
    private $name;

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
     * @param string $directory
     * @param bool $expected
     */
    public function __construct(string $name, string $directory, bool $expected)
    {
        $this->name = $name;
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
     */
    public function executeTest(TestRunnerInterface $runner): TestResult
    {
        $command = '[ -d ' . $this->directory . ' ] && echo yes || echo no';

        $output = $runner->runTest($command);

        $isExisting = $this->stringContains('yes', $output);

        if ($this->expected) {
            $isSuccess = $isExisting;
        } else {
            $isSuccess = !$isExisting;
        }

        if ($isExisting) {
            $output = 'directory existing';
        } else {
            $output = 'directory not existing';
        }

        return new TestResult(
            $this,
            $isSuccess,
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
