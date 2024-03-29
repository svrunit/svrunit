<?php

namespace SVRUnit\Components\Tests\Adapters;


use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Components\Tests\Results\TestResult;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Traits\StringTrait;


class PhpIniTest implements TestInterface
{

    public const MODE_WEB = 'web';
    public const MODE_CLI = 'cli';


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
    private $mode;

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
     * @param string $mode
     * @param string $phpSetting
     * @param string $expected
     * @param string $notExpected
     * @param array<mixed> $expectedOr
     */
    public function __construct(string $name, string $specFile, string $mode, string $phpSetting, string $expected, string $notExpected, array $expectedOr)
    {
        $this->name = $name;
        $this->specFile = $specFile;
        $this->mode = $mode;
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

        if ($this->mode == self::MODE_CLI) {
            $output = $this->getCli($runner);
        } else if ($this->mode == self::MODE_WEB) {
            $output = $this->getWeb($runner);
        } else {
            throw new \Exception('Unknown mode for PHP Init Test: ' . $this->name . '. Available modes: web | cli');
        }


        $success = false;

        if (count($this->expectedOr) > 0) {

            $expectedText = 'Should contain one of these: ';

            if ($output !== '') {
                foreach ($this->expectedOr as $condition) {

                    $operator = $condition['operator'];
                    $value = $condition['value'];

                    $expectedText .= $operator . ' ' . $value . ', ';

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
                            throw new \Exception('Unknown operator ' . $operator . ' found in expected or conditions in PHP Ini test: ' . $this->name);
                    }

                    if ($isOK) {
                        $success = true;
                        break;
                    }
                }
            }

        } else if (!empty($this->expected)) {
            $expectedText = 'Should match: ' . $this->expected;
            $success = $this->containsString($this->expected, $output);
        } else {
            $expectedText = 'Should not match: ' . $this->notExpected;
            $success = !$this->containsString($this->notExpected, $output);
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


    private function toByteSize(string $p_sFormatted): float
    {
        $aUnits = ['B' => 0, 'KB' => 1, 'MB' => 2, 'GB' => 3, 'TB' => 4, 'PB' => 5, 'EB' => 6, 'ZB' => 7, 'YB' => 8];
        $unites = [
            'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'
        ];

        $found = false;
        foreach ($unites as $unit) {
            if ($this->containsString($unit, $p_sFormatted)) {
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
            throw new \Exception('cannot calculate: ' . $p_sFormatted);
        }
        $iUnits = trim(substr($p_sFormatted, 0, strlen($p_sFormatted) - 2));
        if (!intval($iUnits) == $iUnits) {
            throw new \Exception('cannot calculate: ' . $p_sFormatted);
        }

        return (float)$iUnits * pow(1024, (float)$aUnits[$sUnit]);
    }

    /**
     * @param TestRunnerInterface $runner
     * @return string
     */
    private function getCli(TestRunnerInterface $runner)
    {
        # $command = "php -r \"echo ini_get('" . $this->phpSetting . "');\"";
        $command = 'php -i | grep ' . $this->phpSetting;

        $output = $runner->runTest($command);

        # remote the setting itself and keep the value part
        $output = str_replace($this->phpSetting, '', $output);

        if ($this->containsString('=>', $output)) {
            $parts = explode('=>', $output);
            $output = $parts[1];
        }

        if ($this->containsString("\n", $output)) {
            $parts = explode("\n", $output);
            $output = $parts[0];
        }

        return trim($output);
    }

    /**
     * @param TestRunnerInterface $runner
     * @return string
     */
    private function getWeb(TestRunnerInterface $runner)
    {
        // $docRoot = $runner->runTest($getDocRoot);
        $docRoot = $this->getDocRoot($runner);

        if (empty($docRoot)) {
            throw new \Error('Could not detect Apache DocRoot folder. PHP INI web test cannot be executed');
        }

        $phpFile = "svrunit" . $this->generateRandomString(5) . ".php";
        $phpPath = $docRoot . "/" . $phpFile;


        if ($this->phpSetting === 'PHP_VERSION') {
            $command = 'echo "<?php echo \"SVRUNIT: \" . phpversion();"';
        } else {
            $command = 'echo "<?php echo \"SVRUNIT: \" . ini_get(\"' . $this->phpSetting . '\");"';
        }


        $runner->runTest("touch " . $phpPath);
        $runner->runTest($command . ' > ' . $phpPath);


        $tries = 0;
        $exit = false;
        while ($exit == false && $tries <= 5) {

            sleep(2);

            $output = $runner->runTest('curl -L http://localhost/' . $phpFile);

            if (!$this->containsString("Failed to connect to localhost", $output)) {
                $exit = true;
            }
            $tries++;
        }

        $runner->runTest("rm -rf " . $phpPath);

        if ($this->containsString('SVRUNIT:', $output)) {

            /** @var array<mixed> $parts */
            $parts = explode('SVRUNIT:', $output);

            if (count($parts) >= 1) {
                $output = $parts[1];
            } else {
                $output = '';
            }

        } else {
            $output = '';
        }


        $output = trim((string)$output);


        if ($this->phpSetting === 'PHP_VERSION' && $this->containsString('.', $output)) {
            $parts = explode('.', $output);
            $output = $parts[0] . '.' . $parts[1];
        }


        return trim((string)$output);
    }

    /**
     * @param TestRunnerInterface $runner
     * @return string
     */
    private function getDocRoot(TestRunnerInterface $runner)
    {
        $cmd = "php -i | grep APACHE_DOCROOT";

        $output = $runner->runTest($cmd);


        if (!$this->containsString('=>', $output)) {
            return '';
        }


        $parts = explode('=>', $output);

        $output = $parts[1];

        $output = str_replace('$_SERVER[\'APACHE_DOCROOT\']', "", $output);

        $output = trim($output);

        return $output;
    }

    private function generateRandomString(int $length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
