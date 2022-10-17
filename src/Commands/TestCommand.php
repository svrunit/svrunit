<?php

namespace SVRUnit\Commands;

use SVRUnit\Components\Reports\Html\HtmlReport;
use SVRUnit\Components\Reports\JUnit\JUnitReport;
use SVRUnit\Components\Runner\TestRunner;
use SVRUnit\Services\OutputWriter\ColoredOutputWriter;
use SVRUnit\SVRUnit;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TestCommand extends Command
{

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('test')
            ->setDescription('Starts the tests for the provided configuration file')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, 'Read configuration from XML file', '')
            ->addOption('list-groups', null, InputOption::VALUE_NONE, 'List available test groups', null)
            ->addOption('list-suites', null, InputOption::VALUE_NONE, 'List available test suites', null)
            ->addOption('group', null, InputOption::VALUE_REQUIRED, 'Only runs tests from the specified group', '')
            ->addOption('exclude-group', null, InputOption::VALUE_REQUIRED, 'Exclude tests from the specified group(s)', '')
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Output debug information during the execution', null)
            ->addOption('stop-on-error', null, InputOption::VALUE_NONE, 'Stop execution upon first error', null)
            ->addOption('report-junit', null, InputOption::VALUE_NONE, 'Log test execution in JUnit XML format to file', null)
            ->addOption('report-html', null, InputOption::VALUE_NONE, 'Log test execution in SVRUnit HTML format to file', null);

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        echo "SVRUnit Testing Framework, v" . SVRUnit::VERSION . PHP_EOL;
        echo "Copyright (c) 2022 Christian Dangl" . PHP_EOL;
        echo "www.svrunit.com" . PHP_EOL;


        $configFile = (string)$input->getOption('configuration');
        $group = (string)$input->getOption('group');
        $excludeGroups = (string)$input->getOption('exclude-group');
        $debug = ($input->getOption('debug') !== false);
        $stopOnError = ($input->getOption('stop-on-error') !== false);
        $reportJunit = ($input->getOption('report-junit') !== false);
        $reportHtml = ($input->getOption('report-html') !== false);
        $listGroupsMode = ($input->getOption('list-groups') !== false);
        $listSuitesMode = ($input->getOption('list-suites') !== false);

        $reporters = [];

        echo PHP_EOL;

        if ($group) {
            echo "Group: " . $group . PHP_EOL;
        }

        if ($debug) {
            echo "Debug Mode: active" . PHP_EOL;
        }

        if ($stopOnError) {
            echo "Stop on Errors: yes" . PHP_EOL;
        }

        if ($reportJunit) {
            $path = $this->getAbsolutePath('./.reports/report.xml');
            echo "Report: JUnit XML, " . $path . PHP_EOL;

            $reporters[] = new JUnitReport($path);
        }

        if ($reportHtml) {
            $path = $this->getAbsolutePath('./.reports/index.html');
            echo "Report: HTML, " . $path . PHP_EOL;

            $reporters[] = new HtmlReport($path);
        }

        $configAbsolutePath = $this->getAbsolutePath($configFile);

        echo "Configuration: " . $configAbsolutePath . PHP_EOL;
        echo PHP_EOL;


        $testRunner = new TestRunner(
            $configAbsolutePath,
            new ColoredOutputWriter(),
            $stopOnError,
            $debug,
            $reporters
        );

        try {

            if ($listGroupsMode) {

                $testRunner->listGroups();

            } elseif ($listSuitesMode) {

                $testRunner->listSuites();

            } else {

                $testRunner->runTests($group, $excludeGroups);
                $io->success("SVRUnit tests successfully completed");
            }

            return 0;

        } catch (\Exception $ex) {

            # just show a simple output and
            # no big red one
            $io->text($ex->getMessage());

            return 1;
        }
    }

    /**
     * @param string $filename
     * @return string
     */
    private function getAbsolutePath(string $filename): string
    {
        if (empty($filename)) {
            return '';
        }

        $cur_dir = explode('\\', (string)getcwd());
        $workingDir = $cur_dir[count($cur_dir) - 1];

        return $workingDir . '/' . $filename;
    }

}
