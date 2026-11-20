<?php

namespace SVRUnit;

use Exception;
use SVRUnit\Commands\ListGroupsCommand;
use SVRUnit\Commands\ListSuitesCommand;
use SVRUnit\Commands\TestCommand;
use Symfony\Component\Console\Application;

class AppManager
{

    /**
     * @param array<mixed> $arguments
     * @return void
     * @throws Exception
     */
    public static function run(array $arguments)
    {
        $application = new Application('SVRUnit', SVRUnit::VERSION);

        $application->addCommand(new ListSuitesCommand());
        $application->addCommand(new ListGroupsCommand());
        $application->addCommand(new TestCommand());

        $application->setDefaultCommand('list');

        $application->run();
    }

}
