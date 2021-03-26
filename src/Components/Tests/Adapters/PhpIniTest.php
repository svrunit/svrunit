<?php

namespace SVRUnit\Components\Tests\Adapters;


use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\Results\TestResult;
use SVRUnit\Components\Tests\TestInterface;


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
     * @var string
     */
    private $notExpected;

    /**
     * @param string $name
     * @param string $phpSetting
     * @param string $expected
     * @param string $notExpected
     */
    public function __construct(string $name, string $phpSetting, string $expected, string $notExpected)
    {
        $this->name = $name;
        $this->phpSetting = $phpSetting;
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
     * @return TestResult
     */
    public function executeTest(TestRunnerInterface $runner): TestResult
    {
        $command = 'php -i | grep ' . $this->phpSetting;

        $output = $runner->runTest($command);

        # remote the setting itself and keep the value part
        $output = str_replace($this->phpSetting, '', $output);

        if (!empty($this->expected)) {
            $success = $this->stringContains($this->expected, $output);
        } else {
            $success = !$this->stringContains($this->notExpected, $output);
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
