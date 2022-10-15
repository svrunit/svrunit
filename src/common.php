<?php

use SVRUnit\SVRUnit;
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

        $cmd = new \SVRUnit\Commands\TestCommand();
        $application->add($cmd);

        $application->setDefaultCommand('list');

        $application->run();
    }

}
