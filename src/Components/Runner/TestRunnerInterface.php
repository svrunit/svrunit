<?php

namespace SVRUnit\Components\Runner;


interface TestRunnerInterface
{

    /**
     *
     */
    public function setUp(): void;

    /**
     * @param string $command
     * @return string
     */
    public function runTest(string $command): string;

    /**
     *
     */
    public function tearDown(): void;

}
