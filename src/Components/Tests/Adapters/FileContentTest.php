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
     * @var string
     */
    private $notExpected;

    /**
     * @param string $name
     * @param string $filename
     * @param string $expected
     * @param string $notExpected
     */
    public function __construct(string $name, string $filename, string $expected, string $notExpected)
    {
        $this->name = $name;
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
     * @return TestResultInterface
     */
    public function executeTest(TestRunnerInterface $runner): TestResultInterface
    {
        $result = new TestResult($this, $this->expected);

        $output = $runner->runTest('cat ' . $this->filename);

        $result->setOutput($output);

        if (!empty($this->expected)) {
            $success = $this->stringContains($this->expected, $output);
        } else {
            $success = !$this->stringContains($this->notExpected, $output);
        }

        $result->setSuccess($success);

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
