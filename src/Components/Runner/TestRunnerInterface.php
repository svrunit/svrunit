<?php

namespace SVRUnit\Components\Runner;


interface TestRunnerInterface
{

    /**
     *
     */
    public function setUp(): void;

    /**
     * @param $command
     * @return string
     */
    public function runTest($command) : string;

    /**
     *
     */
    public function tearDown(): void;

}
