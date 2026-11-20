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
     * @var string
     */
    private $dockerTag;

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
    public function __construct(string $configFile, OutputWriterInterface $outputWriter, bool $stopOnErrors, bool $debugMode, string $dockerTag, array $reporters)
    {
        $this->configFile = $configFile;
        $this->outputWriter = $outputWriter;
        $this->reporters = $reporters;
        $this->stopOnErrors = $stopOnErrors;
        $this->dockerTag = $dockerTag;
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

        $testSuites = $this->parserSuites->loadTestSuites($this->configFile, $this->dockerTag);

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

        $testSuites = $this->parserSuites->loadTestSuites($this->configFile, $this->dockerTag);

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

        if ($this->dockerTag === '' && strpos((string)file_get_contents($this->configFile), '{{tag}}') !== false) {
            throw new \Exception('Configuration uses the {{tag}} placeholder, but no --docker-tag option was provided!');
        }

        # first start by loading our
        # list of test suites from our xml configuration file
        $testSuites = $this->parserSuites->loadTestSuites($this->configFile, $this->dockerTag);


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

            $this->outputWriter->info('');
            $this->outputWriter->section(' ▶ ' . $suite->getName());

            $result = $this->runTestSuite($suite);

            $runResult->addSuiteResult($result);

            if ($result->hasErrors() && $this->stopOnErrors) {
                # also quit upcoming test suites
                break;
            }
        }


        $failed = $runResult->getErrorCount();

        $summary = sprintf(
            ' Suites: %d   Tests: %d   Passed: %d   Failed: %d   Time: %.2fs',
            count($runResult->getTestSuiteResults()),
            $runResult->getTestCount(),
            $runResult->getTestCount() - $failed,
            $failed,
            $runResult->getTestTime()
        );

        $this->outputWriter->info('');
        $this->outputWriter->debug(' ' . str_repeat('─', 60));

        if ($failed > 0) {
            $this->outputWriter->error($summary);
        } else {
            $this->outputWriter->success($summary);
        }

        if (count($this->reporters) > 0) {
            $this->outputWriter->info('');
            $this->outputWriter->debug(' Building test reports...');
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

        $runner = new TestSuiteRunner(
            $suite,
            $allSuiteTests,
            $suite->getSetupTimeSeconds(),
            $this->stopOnErrors
        );

        $testRunner = $this->buildTestRunner($suite);

        if ($suite->getSetupTimeSeconds() > 0) {
            $this->outputWriter->debug('   waiting ' . $suite->getSetupTimeSeconds() . 's for setup');
        }

        # execute our test suite and print
        # every test result as soon as it's done
        $runner->runTestSuite(
            $testRunner,
            function (TestResult $result) {
                $this->printTestResult($result);
            }
        );

        # ...and grab the report data
        $suiteReport = $runner->getResults();

        $summary = sprintf(
            '   %d/%d passed · %.2fs',
            count($suiteReport->getPassedTests()),
            count($suiteReport->getAllTestResults()),
            $suiteReport->getTestTime()
        );

        if ($suiteReport->hasErrors()) {
            $this->outputWriter->error($summary);
        } else {
            $this->outputWriter->success($summary);
        }

        return $suiteReport;
    }

    /**
     * @param TestResult $result
     * @return void
     */
    private function printTestResult(TestResult $result)
    {
        $line = sprintf('%s  %.2fs', $result->getTest()->getName(), $result->getTimeSeconds());

        if ($result->isSuccess()) {
            $this->outputWriter->success('   ✔ ' . $line);
            return;
        }

        $this->outputWriter->error('   ✘ ' . $line);

        $actual = trim($result->getActual());

        # keep the output readable, the full output
        # is only shown in debug mode
        if (!$this->debugMode && strlen($actual) > 150) {
            $actual = substr($actual, 0, 150) . '...';
        }

        $this->outputWriter->debug('       expected: ' . $result->getExpected());
        $this->outputWriter->debug('       actual:   ' . $actual);
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
                $this->outputWriter->debug('   Docker image: ' . $suite->getDockerImage());

                $containerName = "svrunit_" . bin2hex(random_bytes(4));

                $runner = new DockerImageRunner(
                    $suite->getDockerImage(),
                    $suite->getDockerEnvVariables(),
                    $suite->getDockerEntrypoint(),
                    $containerName,
                    new ShellRunner(),
                    $this->outputWriter,
                    $this->debugMode
                );
                break;

            case TestSuite::TYPE_DOCKER_COMMAND_RUNNER:
                $this->outputWriter->debug('   Docker image (command runner): ' . $suite->getDockerImage());
                $runner = new DockerImageCommandRunner(
                    $suite->getDockerImage(),
                    $suite->getDockerEnvVariables(),
                    new ShellRunner(),
                    $this->outputWriter,
                    $this->debugMode
                );
                break;

            case TestSuite::TYPE_DOCKER_CONTAINER:
                $this->outputWriter->debug('   Docker container: ' . $suite->getDockerContainer());
                $runner = new DockerContainerTestRunner(
                    $suite->getDockerContainer(),
                    new ShellRunner(),
                    $this->outputWriter,
                    $this->debugMode
                );
                break;

            case TestSuite::TYPE_LOCAL:
                $this->outputWriter->debug('   Local machine');
                $runner = new LocalTestRunner(new ShellRunner(), $this->outputWriter, $this->debugMode);
                break;

            default:
                throw new \Exception('Undefined Runner Type');
        }

        return $runner;
    }


}
