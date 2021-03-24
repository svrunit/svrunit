<?php

use SVRUnit\SVRUnit;
use Symfony\Component\Console\Application;

class AppManager
{

    /**
     * @param array $arguments
     * @throws Exception
     */
    public static function run(array $arguments)
    {
        $application = new Application('SVRUnit', SVRUnit::VERSION);

        $cmd = new \SVRUnit\Commands\TestCommand();
        $application->add($cmd);

        $application->setDefaultCommand($cmd->getName());

        $application->run();
    }

}
