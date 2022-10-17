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

class ListGroupsCommand extends Command
{

    use CommandTrait;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('list:groups')
            ->setDescription('Lists all available groups of the provided configuration file.')
            ->addOption('configuration', null, InputOption::VALUE_REQUIRED, 'Read configuration from XML file', '');

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

        $this->showHeader();

        $configFile = (string)$input->getOption('configuration');

        $reporters = [];

        echo PHP_EOL;


        $configAbsolutePath = $this->getAbsolutePath($configFile);

        echo "Configuration: " . $configAbsolutePath . PHP_EOL;
        echo PHP_EOL;


        $testRunner = new TestRunner(
            $configAbsolutePath,
            new ColoredOutputWriter(),
            false,
            false,
            $reporters
        );

        try {

            $testRunner->listGroups();

            $io->writeln("");
            $io->writeln("");

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
