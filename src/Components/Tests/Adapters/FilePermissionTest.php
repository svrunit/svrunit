<?php

namespace SVRUnit\Components\Tests\Adapters;


use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Components\Tests\TestResult;
use SVRUnit\Components\Tests\TestResultInterface;

class FilePermissionTest implements TestInterface
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
     * @var string
     */
    private $expected;

    /**
     * FilePermissionTest constructor.
     * @param string $name
     * @param string $file
     * @param string $expected
     */
    public function __construct(string $name, string $file, string $expected)
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
     * @return TestResultInterface
     */
    public function executeTest(TestRunnerInterface $runner): TestResultInterface
    {
        $result = new TestResult($this, $this->expected);

        $command = 'stat -c %a ' . $this->file;

        $output = $runner->runTest($command);

        $result->setOutput($output);

        if (!$this->stringContains($this->expected, $output)) {
            $result->setSuccess(false);
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