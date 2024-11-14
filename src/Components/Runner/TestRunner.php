<?php

namespace SVRUnit\Components\Runner;

use SVRUnit\Components\Reports\ReportInterface;
use SVRUnit\Components\Runner\Adapters\Docker\DockerContainerTestRunner;
use SVRUnit\Components\Runner\Adapters\Docker\DockerImageCommandRunner;
use SVRUnit\Components\Runner\Adapters\Docker\DockerImageRunner;
use SVRUnit\Components\Runner\Adapters\Local\LocalTestRunner;
use SVRUnit\Components\Tests\Results\RunResult;
use SVRUnit\Components\Tests\Results\SuiteResult;
use SVRUnit\Components\Tests\Results\TestResult;
use SVRUnit\Components\Tests\TestSuite;
use SVRUnit\Services\ConfigParser\TestFileCollector;
use SVRUnit\Services\ConfigParser\TestSuiteConfigParser;
use SVRUnit\Services\OutputWriter\OutputWriterInterface;
use SVRUnit\Services\ShellRunner\ShellRunner;
use SVRUnit\Services\TestParser\TestSpecFileParser;


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
     * @var ReportInterface[]
     */
    private $reporters;

    /**
     * @var bool
     */
    private $stopOnErrors;

    /**
     * @var TestSuiteConfigParser
     */
    private $parserSuites;


    /**
     * @param string $configFile
     * @param OutputWriterInterface $outputWriter
     * @param bool $stopOnErrors
     * @param bool $debugMode
     * @param array<mixed> $reporters
     */
    public function __construct(string $configFile, OutputWriterInterface $outputWriter, bool $stopOnErrors, bool $debugMode, array $reporters)
    {
        $this->configFile = $configFile;
        $this->outputWriter = $outputWriter;
        $this->reporters = $reporters;
        $this->stopOnErrors = $stopOnErrors;
        $this->debugMode = $debugMode;

        $this->parserSuites = new TestSuiteConfigParser();
    }


    /**
     * @return void
     * @throws \Exception
     */
    public function listGroups()
    {
        if (!file_exists($this->configFile)) {
            throw new \Exception('No configuration file provided!');
        }

        $testSuites = $this->parserSuites->loadTestSuites($this->configFile);

        $this->outputWriter->section("Available Groups:");

        /** @var TestSuite $suite */
        foreach ($testSuites as $suite) {
            if (!empty($suite->getGroup())) {
                $this->outputWriter->info("   - " . $suite->getGroup());
            }
        }
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function listSuites()
    {
        if (!file_exists($this->configFile)) {
            throw new \Exception('No configuration file provided!');
        }

        $testSuites = $this->parserSuites->loadTestSuites($this->configFile);

        $this->outputWriter->section("Available Suites");

        /** @var TestSuite $suite */
        foreach ($testSuites as $suite) {
            $this->outputWriter->info("   - " . $suite->getName());
        }
    }

    /**
     * @param string $filterGroup
     * @param string $excludedFilterGroups
     * @return void
     * @throws \Exception
     */
    public function runTests(string $filterGroup, string $excludedFilterGroups)
    {
        # always clear old reports
        foreach ($this->reporters as $reporter) {
            $reporter->clear();
        }

        $runResult = new RunResult();

        if (!file_exists($this->configFile)) {
            throw new \Exception('No configuration file provided!');
        }

        # first start by loading our
        # list of test suites from our xml configuration file
        $testSuites = $this->parserSuites->loadTestSuites($this->configFile);


        $excludedGroups = array_filter(explode(',', $excludedFilterGroups));

        /** @var TestSuite $suite */
        foreach ($testSuites as $suite) {

            # if we have a group filter applied
            # then skip our suite if the group doesn't match
            if (!empty($filterGroup) && $suite->getGroup() !== $filterGroup) {
                continue;
            }

            # if we have excluded groups, and our suite is
            # in that group, then just move on to the next suite
            if (count($excludedGroups) > 0) {
                if (in_array($suite->getGroup(), $excludedGroups)) {
                    continue;
                }
            }

            $this->outputWriter->debug('');
            $this->outputWriter->section('** TEST SUITE: ' . $suite->getName() . ', Setup Time: ' . $suite->getSetupTimeSeconds() . 's');

            $result = $this->runTestSuite($suite);

            $runResult->addSuiteResult($result);

            if ($result->hasErrors() && $this->stopOnErrors) {
                # also quit upcoming test suites
                break;
            }
        }


        $this->outputWriter->debug('');
        $this->outputWriter->debug('Time: ');
        $this->outputWriter->debug($runResult->getTestTime() . ' ms');

        if (count($this->reporters) > 0) {
            $this->outputWriter->debug('');
            $this->outputWriter->debug('.........building test reports........');
        }

        foreach ($this->reporters as $reporter) {
            $reporter->generate($runResult);
        }

        if ($runResult->hasErrors()) {
            throw new \Exception('Tests have failed');
        }
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
            throw new \Exception('No tests found in suite: ' . $suite->getName());
        }

        $this->outputWriter->debug('');


        $runner = new TestSuiteRunner(
            $suite,
            $allSuiteTests,
            $suite->getSetupTimeSeconds(),
            $this->stopOnErrors
        );


        # execute our test suite
        $runner->runTestSuite($this->buildTestRunner($suite));

        # ...and grab the report data
        $suiteReport = $runner->getResults();


        $this->outputWriter->debug('');

        /** @var TestResult $result */
        foreach ($suiteReport->getAllTestResults() as $result) {

            if (!$result->isSuccess()) {

                $this->outputWriter->debug('* [TEST] ' . $result->getTest()->getName() . ' FAILED....');

                if ($this->debugMode) {
                    $this->outputWriter->debug('    - Expected: ' . $result->getExpected());
                    $this->outputWriter->debug('    + Actual: ' . $result->getActual());
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
     * @param int $length
     * @return string
     */
    private function getRandomName(int $length): string
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
     * @return array<mixed>
     */
    private function loadTestsOfSuite(TestSuite $suite): array
    {
        $parser = new TestSpecFileParser();
        $fileCollector = new TestFileCollector();
        $allSuiteTests = [];


        /** @var array<mixed> $testFiles */
        $testFiles = $fileCollector->searchTestFiles(dirname($this->configFile), $suite->getTestFolders());

        # also add individual files
        foreach ($suite->getTestFiles() as $file) {
            $testFiles[] = dirname($this->configFile) . '/' . $file;
        }

        /** @var string $file */
        foreach ($testFiles as $file) {
            $newTests = $parser->parse($file, $suite->getExecutable());
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

            case TestSuite::TYPE_DOCKER_COMMAND_RUNNER:
                $this->outputWriter->info('Starting tests in Docker image as command runner');
                $runner = new DockerImageCommandRunner(
                    $suite->getDockerImage(),
                    $suite->getDockerEnvVariables(),
                    new ShellRunner(),
                    $this->outputWriter
                );
                break;

            case TestSuite::TYPE_DOCKER_CONTAINER:
                $this->outputWriter->info('Starting tests in existing Docker container: ' . $suite->getDockerContainer());
                $runner = new DockerContainerTestRunner(
                    $suite->getDockerContainer()
                );
                break;

            case TestSuite::TYPE_LOCAL:
                $this->outputWriter->info('Starting tests locally');
                $runner = new LocalTestRunner($this->outputWriter);
                break;

            default:
                throw new \Exception('Undefined Runner Type');
        }

        return $runner;
    }


}
