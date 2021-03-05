<?php

namespace SVRUnit\Components\Tests\Adapters;


use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Components\Tests\TestResult;
use SVRUnit\Components\Tests\TestResultInterface;


class FileContentTest implements TestInterface
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $expected;

    /**
     * FileContentTest constructor.
     * @param string $name
     * @param string $filename
     * @param string $expected
     */
    public function __construct(string $name, string $filename, string $expected)
    {
        $this->name = $name;
        $this->filename = $filename;
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

        $output = $runner->runTest('cat ' . $this->filename);

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
