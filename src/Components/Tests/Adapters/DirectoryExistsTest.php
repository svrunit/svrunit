<?php

namespace SVRUnit\Components\Tests\Adapters;

use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Components\Tests\TestResult;
use SVRUnit\Components\Tests\TestResultInterface;


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
     * @return TestResultInterface
     */
    public function executeTest(TestRunnerInterface $runner): TestResultInterface
    {
        $result = new TestResult($this, 'exists');

        $command = '[ -d ' . $this->directory . ' ] && echo yes || echo no';

        $output = $runner->runTest($command);

        $isExisting = $this->stringContains('yes', $output);

        if ($this->expected) {
            $result->setSuccess($isExisting);
        } else {
            $result->setSuccess(!$isExisting);
        }

        if ($isExisting) {
            $result->setOutput('directory existing');
        } else {
            $result->setOutput('directory not existing');
        }

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
