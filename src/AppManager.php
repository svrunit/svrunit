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

        # addCommands() exists in all Symfony Console versions, unlike add() (removed in 8.0)
        # and addCommand() (added in 7.4), so PHP 7.4 projects with older versions keep working
        $application->addCommands([
            new ListSuitesCommand(),
            new ListGroupsCommand(),
            new TestCommand(),
        ]);

        $application->setDefaultCommand('list');

        $application->run();
    }

}
