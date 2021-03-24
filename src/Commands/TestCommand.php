<?php

namespace SVRUnit\Commands;

use SVRUnit\Components\Reports\JUnit\JUnitReport;
use SVRUnit\Components\Reports\Null\NullReporter;
use SVRUnit\Components\Runner\TestRunner;
use SVRUnit\Services\OutputWriter\ColoredOutputWriter;
use SVRUnit\SVRUnit;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{

    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('test')
            ->setDescription('Starts the tests for the provided configuration file')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, '', '')
            ->addOption('debug', null, InputOption::VALUE_NONE, '')
            ->addOption('report-junit', null, InputOption::VALUE_NONE, '');

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
        echo "SVRUnit Testing Framework, v" . SVRUnit::VERSION . PHP_EOL;
        echo "Copyright (c) 2021 Christian Dangl" . PHP_EOL;
        echo "www.svrunit.com" . PHP_EOL;


        $configFile = (string)$input->getOption('configuration');
        $debug = ($input->getOption('debug') !== false);
        $reportJunit = ($input->getOption('report-junit') !== false);

        $report = new NullReporter();

        if ($debug) {
            echo PHP_EOL;
            echo "Debug Mode: active" . PHP_EOL;
        }

        if ($reportJunit) {
            $path = $this->getAbsolutePath('./.reports/report.xml');
            echo PHP_EOL;
            echo "Report: JUnit XML, " . $path . PHP_EOL;

            $report = new JUnitReport($path);
        }

        $configAbsolutePath = $this->getAbsolutePath($configFile);

        echo "Configuration: " . $configAbsolutePath . PHP_EOL;
        echo PHP_EOL;


        $outputWriter = new ColoredOutputWriter();

        $testRunner = new TestRunner($configAbsolutePath, $outputWriter, $report);
        $success = $testRunner->run($debug);

        if ($success) {
            return 0;
        }

        return 1;
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

        $cur_dir = explode('\\', getcwd());
        $workingDir = $cur_dir[count($cur_dir) - 1];

        return $workingDir . '/' . $filename;
    }

}
