<?php

namespace SVRUnit\Components\Tests\Adapters;


use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Components\Tests\TestResult;
use SVRUnit\Components\Tests\TestResultInterface;


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
     * @return TestResultInterface
     */
    public function executeTest(TestRunnerInterface $runner): TestResultInterface
    {
        $result = new TestResult($this, 'exists');

        $command = '[ -f ' . $this->file . ' ] && echo svrunit-file-exists || echo svrunit-file-not-existing';

        $output = $runner->runTest($command);

        $isExisting = $this->stringContains('svrunit-file-exists', $output);

        if ($this->expected) {
            $result->setSuccess($isExisting);
        } else {
            $result->setSuccess(!$isExisting);
        }

        if ($isExisting) {
            $result->setOutput('file existing');
        } else {
            $result->setOutput('file not existing');
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



