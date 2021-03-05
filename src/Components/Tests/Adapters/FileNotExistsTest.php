<?php

namespace SVRUnit\Components\Tests\Adapters;

use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Components\Tests\TestResult;
use SVRUnit\Components\Tests\TestResultInterface;


class FileNotExistsTest implements TestInterface
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
     * FileNotExistsTest constructor.
     * @param string $name
     * @param string $file
     */
    public function __construct(string $name, string $file)
    {
        $this->name = $name;
        $this->file = $file;
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
        $result = new TestResult($this, 'not existing');

        $command = '[ -f ' . $this->file . ' ] && echo svrunit-file-exists || echo svrunit-file-not-existing';

        $output = $runner->runTest($command);


        if ($this->stringContains('svrunit-file-exists', $output)) {
            $result->setSuccess(false);
            $result->setOutput('file existing');
        } else {
            $result->setSuccess(true);
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



