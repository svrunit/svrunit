<?php

namespace SVRUnit\Components\Tests\Adapters;


use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Components\Tests\TestResult;
use SVRUnit\Components\Tests\TestResultInterface;


class PhpIniTest implements TestInterface
{

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $phpSetting;

    /**
     * @var string
     */
    private $expected;


    /**
     * PhpIniTest constructor.
     * @param string $name
     * @param string $phpSetting
     * @param string $expected
     */
    public function __construct(string $name, string $phpSetting, string $expected)
    {
        $this->name = $name;
        $this->phpSetting = $phpSetting;
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

        $command = 'php -i | grep ' . $this->phpSetting;

        $output = $runner->runTest($command);

        # remote the setting itself and keep the value part
        $output = str_replace($this->phpSetting, '', $output);

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
