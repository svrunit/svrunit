<?php

namespace SVRUnit\Commands;

use SVRUnit\SVRUnit;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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
            ->addOption('debug', null, InputOption::VALUE_NONE, '');

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


        if ($debug) {
            echo PHP_EOL;
            echo "Debug Mode: active" . PHP_EOL;
        }

        $configAbsolutePath = $this->getConfigFileAbsolutePath($configFile);

        echo "Configuration: " . $configAbsolutePath . PHP_EOL;
        echo PHP_EOL;

        $runner = new SVRUnit($configAbsolutePath);
        $runner->run($debug);

        return 0;
    }

    /**
     * @return string
     */
    private function getConfigFileAbsolutePath(string $configFile): string
    {
        $cur_dir = explode('\\', getcwd());
        $workingDir = $cur_dir[count($cur_dir) - 1];

        $configAbsolutePath = (!empty($configFile)) ? $workingDir . '/' . $configFile : '';

        return $configAbsolutePath;
    }

}
