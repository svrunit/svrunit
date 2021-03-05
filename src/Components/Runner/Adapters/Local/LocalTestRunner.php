<?php

namespace SVRUnit\Components\Runner\Adapters\Local;


use SVRUnit\Components\Runner\TestRunnerInterface;

class LocalTestRunner implements TestRunnerInterface
{

    /**
     * @return mixed|void
     */
    public function setUp(): void
    {
    }

    /**
     * @return mixed|void
     */
    public function tearDown(): void
    {
    }

    /**
     * @param $command
     * @return string
     */
    function runTest($command): string
    {
        $output = shell_exec($command . " 2>&1");

        return $output;
    }

}
