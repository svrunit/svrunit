<?php

namespace SVRUnit\Components\Runner;

use SVRUnit\Components\Runner\Adapters\Docker\DockerContainerTestRunner;
use SVRUnit\Components\Runner\Adapters\Docker\DockerImageTestRunner;
use SVRUnit\Components\Runner\Adapters\Local\LocalTestRunner;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Components\Tests\TestResultInterface;
use SVRUnit\Components\Tests\TestSuite;
use SVRUnit\Services\ConfigParser\ConfigXmlParser;
use SVRUnit\Services\ConfigParser\TestFileCollector;
use SVRUnit\Services\OutputWriter\OutputWriterInterface;
use SVRUnit\Services\TestParser\YamlTestParser;


class TestRunner
{

    /**
     * @var OutputWriterInterface
     */
    private $outputWriter;

    /**
     * @var string
     */
    private $configFile;

    /**
     * @var bool
     */
    private $debugMode;


    /**
     * TestRunner constructor.
     * @param string $configFile
     * @param OutputWriterInterface $outputWriter
     */
    public function __construct(string $configFile, OutputWriterInterface $outputWriter)
    {
        $this->configFile = $configFile;
        $this->outputWriter = $outputWriter;
    }

    /**
     * @param bool $debugMode
     * @throws \Exception
     */
    public function run(bool $debugMode): void
    {
        $this->debugMode = $debugMode;


        if (!file_exists($this->configFile)) {
            $this->outputWriter->writeDebug('no configuration file provided');
            return;
        }


        $configXMLParser = new ConfigXmlParser();
        $testSuites = $configXMLParser->loadTestSuites($this->configFile);

        $startTime = microtime(true);

        $errorsOccured = false;


        /** @var TestSuite $suite */
        foreach ($testSuites as $suite) {

            $this->outputWriter->writeDebug('** TEST SUITE: ' . $suite->getName() . ', Setup Time: ' . $suite->getSetupTimeSeconds() . 's');
            $success = $this->runTestSuite($suite);

            if (!$success) {
                $errorsOccured = true;
            }

            $this->outputWriter->writeDebug('');
        }

        $endTime = microtime(true);
        $timeMS = round(($endTime - $startTime) * 1000, 2);

        $this->outputWriter->writeDebug('');
        $this->outputWriter->writeDebug('Time: ');
        $this->outputWriter->writeDebug($timeMS . ' ms');

        if ($errorsOccured) {
            exit(1);
        }

        exit(0);
    }


    /**
     * @param TestSuite $suite
     * @param int $setupTimeSeconds
     * @return bool
     * @throws \Exception
     */
    private function runTestSuite(TestSuite $suite): bool
    {
        $parser = new YamlTestParser();
        $fileCollector = new TestFileCollector();
        $allSuiteTests = array();

        /** @var array $testFiles */
        $testFiles = $fileCollector->searchTestFiles(
            dirname($this->configFile),
            $suite->getTestFolders()
        );


        /** @var string $file */
        foreach ($testFiles as $file) {
            $newTests = $parser->parse($file);
            $allSuiteTests = array_merge($newTests, $allSuiteTests);
        }

        if (count($allSuiteTests) <= 0) {
            $this->outputWriter->writeWarning('NO TESTS FOUND');
            return false;
        }

        /** @var TestRunnerInterface|null $runner */
        $runner = null;

        switch ($suite->getType()) {

            case TestSuite::TYPE_DOCKER_IMAGE:
                $this->outputWriter->writeInfo('>> Starting tests in new Docker image: ' . $suite->getDockerImage());
                $runner = new DockerImageTestRunner(
                    $suite->getDockerImage(),
                    $suite->getDockerEnvVariables(),
                    $suite->getDockerEntrypoint()
                );
                break;

            case TestSuite::TYPE_DOCKER_CONTAINER:
                $this->outputWriter->writeInfo('>> Starting tests in existing Docker container: ' . $suite->getDockerContainer());
                $runner = new DockerContainerTestRunner($suite->getDockerContainer());
                break;

            case TestSuite::TYPE_LOCAL:
                $this->outputWriter->writeInfo('Starting Tests locally');
                $runner = new LocalTestRunner();
                break;

            default:
                throw new \Exception('Undefined Runner Type');
        }


        $tester = new TestSuiteRunner($allSuiteTests, $suite->getSetupTimeSeconds());
        $tester->testAll($runner);


        $this->outputWriter->writeDebug('');

        if ($tester->getFailedTestsCount() <= 0) {
            $this->outputWriter->writeSuccess('OK ' . $tester->getPassedTestsCount() . '/' . $tester->getAllTestsCount() . ' TESTS PASSED');
            return true;
        }

        /** @var TestResultInterface $result */
        foreach ($tester->getFailedTests() as $result) {
            $this->outputWriter->writeDebug('[TEST] ' . $result->getTest()->getName() . ' FAILED....');

            if ($this->debugMode) {
                $this->outputWriter->writeDebug('Actual: ' . $result->getOutput());
                $this->outputWriter->writeDebug('Expected: ' . $result->getExpected());
            }
        }

        $this->outputWriter->writeDebug('');
        $this->outputWriter->writeError('FAILED ' . $tester->getFailedTestsCount() . '/' . $tester->getAllTestsCount() . ' TESTS FAILED');

        return false;
    }

}
