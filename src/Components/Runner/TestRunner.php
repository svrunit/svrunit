<?php

namespace SVRUnit\Components\Runner;

use SVRUnit\Components\Reports\Null\NullReporter;
use SVRUnit\Components\Reports\ReportInterface;
use SVRUnit\Components\Reports\TestSuiteResult;
use SVRUnit\Components\Runner\Adapters\Docker\DockerContainerTestRunner;
use SVRUnit\Components\Runner\Adapters\Docker\DockerImageRunner;
use SVRUnit\Components\Runner\Adapters\Local\LocalTestRunner;
use SVRUnit\Components\Tests\Results\RunResult;
use SVRUnit\Components\Tests\Results\SuiteResult;
use SVRUnit\Components\Tests\Results\TestResult;
use SVRUnit\Components\Tests\TestSuite;
use SVRUnit\Services\ConfigParser\ConfigXmlParser;
use SVRUnit\Services\ConfigParser\TestFileCollector;
use SVRUnit\Services\OutputWriter\OutputWriterInterface;
use SVRUnit\Services\ShellRunner\ShellRunner;
use SVRUnit\Services\Stopwatch\Stopwatch;
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
     * @var ConfigXmlParser
     */
    private $parserSuites;


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

        $this->parserSuites = new ConfigXmlParser();
    }


    /**
     * @param bool $debugMode
     * @return bool
     * @throws \Exception
     */
    public function run(bool $debugMode): bool
    {
        $this->debugMode = $debugMode;

        $runResult = new RunResult();

        if (!file_exists($this->configFile)) {
            $this->outputWriter->debug('no configuration file provided');
            return $runResult->hasErrors();
        }

        # first start by loading our
        # list of test suites from our xml configuration file
        $testSuites = $this->parserSuites->loadTestSuites($this->configFile);


        /** @var TestSuite $suite */
        foreach ($testSuites as $suite) {

            $this->outputWriter->section('** TEST SUITE: ' . $suite->getName() . ', Setup Time: ' . $suite->getSetupTimeSeconds() . 's');
            $this->outputWriter->debug('');

            $result = $this->runTestSuite($suite);

            $runResult->addSuiteResult($result);
        }


        $this->outputWriter->debug('');
        $this->outputWriter->debug('Time: ');
        $this->outputWriter->debug($runResult->getTestTime() . ' ms');


        if (!$this->report instanceof NullReporter) {

            $this->outputWriter->debug('');
            $this->outputWriter->debug('.........building test report........');

            $this->report->generate($runResult);
        }

        return !$runResult->hasErrors();
    }


    /**
     * @param TestSuite $suite
     * @return SuiteResult
     * @throws \Exception
     */
    private function runTestSuite(TestSuite $suite): SuiteResult
    {
        $allSuiteTests = $this->loadTestsOfSuite($suite);

        if (count($allSuiteTests) <= 0) {

            $this->outputWriter->warning('NO TESTS FOUND');

            return new SuiteResult($suite, []);
        }

        $this->outputWriter->debug('');


        $runner = new TestSuiteRunner(
            $suite,
            $allSuiteTests,
            $suite->getSetupTimeSeconds()
        );


        # execute our test suite
        $runner->runTestSuite($this->buildTestRunner($suite));

        # ...and grab the report data
        $suiteReport = $runner->getResults();


        $this->outputWriter->debug('');

        /** @var TestResult $result */
        foreach ($suiteReport->getAllTestResults() as $result) {

            if (!$result->isSuccess()) {

                $this->outputWriter->debug('[TEST] ' . $result->getTest()->getName() . ' FAILED....');

                if ($this->debugMode) {
                    $this->outputWriter->debug('Actual: ' . $result->getActual());
                    $this->outputWriter->debug('Expected: ' . $result->getExpected());
                }
            }
        }

        $this->outputWriter->debug('');

        if ($suiteReport->hasErrors()) {

            $this->outputWriter->error('FAILED ' . count($suiteReport->getFailedTests()) . '/' . count($suiteReport->getAllTestResults()) . ' TESTS FAILED');

        } else {

            $this->outputWriter->success('OK ' . count($suiteReport->getPassedTests()) . '/' . count($suiteReport->getAllTestResults()) . ' TESTS PASSED');
        }

        return $suiteReport;
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


    /**
     * @param TestSuite $suite
     * @return array
     */
    private function loadTestsOfSuite(TestSuite $suite): array
    {
        $parser = new YamlTestParser();
        $fileCollector = new TestFileCollector();
        $allSuiteTests = [];


        /** @var array $testFiles */
        $testFiles = $fileCollector->searchTestFiles(dirname($this->configFile), $suite->getTestFolders());


        /** @var string $file */
        foreach ($testFiles as $file) {
            $newTests = $parser->parse($file);
            $allSuiteTests = array_merge($newTests, $allSuiteTests);
        }

        return $allSuiteTests;
    }

    /**
     * @param TestSuite $suite
     * @return TestRunnerInterface
     * @throws \Exception
     */
    private function buildTestRunner(TestSuite $suite): TestRunnerInterface
    {
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

        return $runner;
    }


}
