<?php

namespace SVRUnit\Components\Tests\Adapters;


use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\Results\TestResult;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Traits\StringTrait;


class PhpIniTest implements TestInterface
{

    use StringTrait;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $specFile;

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
     * @var array<mixed>
     */
    private $expectedOr;


    /**
     * @param string $name
     * @param string $specFile
     * @param string $phpSetting
     * @param string $expected
     * @param string $notExpected
     * @param array<mixed> $expectedOr
     */
    public function __construct(string $name, string $specFile, string $phpSetting, string $expected, string $notExpected, array $expectedOr)
    {
        $this->name = $name;
        $this->specFile = $specFile;
        $this->phpSetting = $phpSetting;
        $this->expected = $expected;
        $this->notExpected = $notExpected;
        $this->expectedOr = $expectedOr;
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
     * @throws \Exception
     */
    public function executeTest(TestRunnerInterface $runner): TestResult
    {
        if ($this->phpSetting === '') {
            throw new \Exception('No setting specified for PHP Ini test: ' . $this->name);
        }

        if ($this->expected === '' && $this->notExpected === '' && count($this->expectedOr) === 0) {
            throw new \Exception('No expected values defined for PHP Ini test: ' . $this->name);
        }

        $command = 'php -i | grep ' . $this->phpSetting;

        $output = $runner->runTest($command);

        # remote the setting itself and keep the value part
        $output = str_replace($this->phpSetting, '', $output);

        if ($this->stringContains('=>', $output)) {
            $parts = explode('=>', $output);
            $output = $parts[1];
        }

        if ($this->stringContains("\n", $output)) {
            $parts = explode("\n", $output);
            $output = $parts[0];
        }

        $output = trim($output);


        $success = false;

        if (count($this->expectedOr) > 0) {

            $expectedText = 'Should contain one of these: ';

            foreach ($this->expectedOr as $condition) {

                $operator = $condition['operator'];
                $value = $condition['value'];

                $expectedText .= $operator . ' ' . $value . ', ';

                $isOK = false;

                switch ($operator) {

                    case '=':
                        $isOK = (string)$value === $output;
                        break;

                    case '>':
                        $isOK = $this->toByteSize($output) > $this->toByteSize($value);
                        break;

                    case '>=':
                        $isOK = $this->toByteSize($output) >= $this->toByteSize($value);
                        break;

                    case '<':
                        $isOK = $this->toByteSize($output) < $this->toByteSize($value);
                        break;

                    case '<=':
                        $isOK = $this->toByteSize($output) <= $this->toByteSize($value);
                        break;

                    default:
                        throw new \Exception('Unknown operator found in expected OR conditions in PHP Ini test: ' . $this->name);
                }

                if ($isOK) {
                    $success = true;
                    break;
                }
            }

        } else if (!empty($this->expected)) {
            $expectedText = 'Should match: ' . $this->expected;
            $success = $this->stringContains($this->expected, $output);
        } else {
            $expectedText = 'Should not match: ' . $this->notExpected;
            $success = !$this->stringContains($this->notExpected, $output);
        }

        return new TestResult(
            $this,
            $this->specFile,
            $success,
            1,
            $expectedText,
            $output
        );
    }


    private function toByteSize($p_sFormatted): float
    {
        $aUnits = array('B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4, 'PB' => 5, 'EB' => 6, 'ZB' => 7, 'YB' => 8);

        $found = false;
        foreach ($aUnits as $unit) {
            if ($this->stringContains($unit, $p_sFormatted)) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            return (float)$p_sFormatted;
        }

        $sUnit = strtoupper(trim(substr($p_sFormatted, -2)));
        if (intval($sUnit) !== 0) {
            $sUnit = 'B';
        }
        if (!in_array($sUnit, array_keys($aUnits))) {
            return false;
        }
        $iUnits = trim(substr($p_sFormatted, 0, strlen($p_sFormatted) - 2));
        if (!intval($iUnits) == $iUnits) {
            return false;
        }
        return $iUnits * pow(1024, $aUnits[$sUnit]);
    }

}
