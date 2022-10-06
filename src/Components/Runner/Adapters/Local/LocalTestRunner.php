<?php

namespace SVRUnit\Components\Runner\Adapters\Local;


use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Services\OutputWriter\OutputWriterInterface;

class LocalTestRunner implements TestRunnerInterface
{


    /**
     *
     */
    public function __construct()
    {
    }


    /**
     *
     */
    public function setUp(): void
    {
    }

    /**
     *
     */
    public function tearDown(): void
    {
    }

    /**
     * @param string $command
     * @return string
     */
    function runTest(string $command): string
    {
        $output = (string)shell_exec($command . " 2>&1");

        return $output;
    }

}
