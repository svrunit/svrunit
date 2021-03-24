<?php

namespace SVRUnit\Components\Runner;

use SVRUnit\Components\Reports\ReportInterface;
use SVRUnit\Components\Runner\Adapters\Docker\DockerContainerTestRunner;
use SVRUnit\Components\Runner\Adapters\Docker\DockerImageRunner;
use SVRUnit\Components\Runner\Adapters\Local\LocalTestRunner;
use SVRUnit\Components\Tests\TestInterface;
use SVRUnit\Components\Tests\TestResultInterface;
use SVRUnit\Components\Tests\TestSuite;
use SVRUnit\Components\Tests\TestSuiteResult;
use SVRUnit\Components\Tests\TestSuiteResultInterface;
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
     * @var ReportInterface
     */
    private $report;

    /**
     * @var TestSuiteResultInterface[]
     */
    private $results;


    /**
     * TestRunner constructor.
     * @param string $configFile
     * @param OutputWriterInterface $outputWriter
     * @param ReportInterface $report
     */
    public function __construct(string $configFile, OutputWriterInterface $outputWriter, ReportInterface $report)
    {
        $this->configFile = $configFile;
        $this->outputWriter = $outputWriter;
        $this->report = $report;
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

        $suiteResults = array();

        /** @var TestSuite $suite */
        foreach ($testSuites as $suite) {

            $this->outputWriter->section('** TEST SUITE: ' . $suite->getName() . ', Setup Time: ' . $suite->getSetupTimeSeconds() . 's');
            $this->outputWriter->debug('');

            $result = $this->runTestSuite($suite);

            if (!$result->hasErrors()) {
                $errorsOccured = true;
            }

            $this->outputWriter->debug('');

            $suiteResults[] = $result;
        }

        $endTime = microtime(true);
        $timeMS = round(($endTime - $startTime) * 1000, 2);

        $this->outputWriter->debug('');
        $this->outputWriter->debug('Time: ');
        $this->outputWriter->debug($timeMS . ' ms');


        $this->report->generate($suiteResults);

        if ($errorsOccured) {
            return false;
        }

        return true;
    }


    /**
     * @param TestSuite $suite
     * @return TestSuiteResultInterface
     * @throws \Exception
     */
    private function runTestSuite(TestSuite $suite): TestSuiteResultInterface
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
            return new TestSuiteResult($suite, array());
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
        } else {

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
        }

        return new TestSuiteResult($suite, $tester->getAllResults());
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
