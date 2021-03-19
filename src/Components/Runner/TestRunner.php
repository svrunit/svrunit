<?php

namespace SVRUnit\Components\Runner;

use SVRUnit\Components\Runner\Adapters\Docker\DockerContainerTestRunner;
use SVRUnit\Components\Runner\Adapters\Docker\DockerImageRunner;
use SVRUnit\Components\Runner\Adapters\Local\LocalTestRunner;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Components\Tests\TestResultInterface;
use SVRUnit\Components\Tests\TestSuite;
use SVRUnit\Services\ConfigParser\ConfigXmlParser;
use SVRUnit\Services\ConfigParser\TestFileCollector;
use SVRUnit\Services\OutputWriter\OutputWriterInterface;
use SVRUnit\Services\ShellRunner\ShellRunner;
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
     * @return bool
     * @throws \Exception
     */
    public function run(bool $debugMode): bool
    {
        $this->debugMode = $debugMode;


        if (!file_exists($this->configFile)) {
            $this->outputWriter->debug('no configuration file provided');
            return false;
        }


        $configXMLParser = new ConfigXmlParser();
        $testSuites = $configXMLParser->loadTestSuites($this->configFile);

        $startTime = microtime(true);

        $errorsOccured = false;


        /** @var TestSuite $suite */
        foreach ($testSuites as $suite) {

            $this->outputWriter->section('** TEST SUITE: ' . $suite->getName() . ', Setup Time: ' . $suite->getSetupTimeSeconds() . 's');
            $this->outputWriter->debug('');

            $success = $this->runTestSuite($suite);

            if (!$success) {
                $errorsOccured = true;
            }

            $this->outputWriter->debug('');
        }

        $endTime = microtime(true);
        $timeMS = round(($endTime - $startTime) * 1000, 2);

        $this->outputWriter->debug('');
        $this->outputWriter->debug('Time: ');
        $this->outputWriter->debug($timeMS . ' ms');

        if ($errorsOccured) {
            return false;
        }

        return true;
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
            $this->outputWriter->warning('NO TESTS FOUND');
            return false;
        }

        /** @var TestRunnerInterface|null $runner */
        $runner = null;

        switch ($suite->getType()) {

            case TestSuite::TYPE_DOCKER_IMAGE:
                $this->outputWriter->info('Starting tests in new Docker image: ' . $suite->getDockerImage());

                $containerName = "svrunit_" . $this->getRandomName(4);

                $runner = new DockerImageRunner(
                    $suite->getDockerImage(),
                    $suite->getDockerEnvVariables(),
                    $suite->getDockerEntrypoint(),
                    $containerName,
                    new ShellRunner(),
                    $this->outputWriter
                );
                break;

            case TestSuite::TYPE_DOCKER_CONTAINER:
                $this->outputWriter->info('Starting tests in existing Docker container: ' . $suite->getDockerContainer());
                $runner = new DockerContainerTestRunner(
                    $suite->getDockerContainer(),
                    $this->outputWriter
                );
                break;

            case TestSuite::TYPE_LOCAL:
                $this->outputWriter->info('Starting tests locally');
                $runner = new LocalTestRunner(
                    $this->outputWriter
                );
                break;

            default:
                throw new \Exception('Undefined Runner Type');
        }


        $tester = new TestSuiteRunner($allSuiteTests, $suite->getSetupTimeSeconds());
        $tester->testAll($runner, $this->outputWriter, $this->debugMode);

        $this->outputWriter->debug('');

        if ($tester->getFailedTestsCount() <= 0) {
            $this->outputWriter->success('OK ' . $tester->getPassedTestsCount() . '/' . $tester->getAllTestsCount() . ' TESTS PASSED');
            return true;
        }

        /** @var TestResultInterface $result */
        foreach ($tester->getFailedTests() as $result) {
            $this->outputWriter->debug('[TEST] ' . $result->getTest()->getName() . ' FAILED....');

            if ($this->debugMode) {
                $this->outputWriter->debug('Actual: ' . $result->getOutput());
                $this->outputWriter->debug('Expected: ' . $result->getExpected());
            }
        }

        $this->outputWriter->debug('');
        $this->outputWriter->error('FAILED ' . $tester->getFailedTestsCount() . '/' . $tester->getAllTestsCount() . ' TESTS FAILED');

        return false;
    }

    /**
     * @param $length
     * @return string
     */
    private function getRandomName($length): string
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
