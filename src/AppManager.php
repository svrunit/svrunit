<?php

namespace SVRUnit;

use Exception;
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

        $cmd = new TestCommand();
        $application->add($cmd);

        $application->setDefaultCommand('list');

        $application->run();
    }

}
