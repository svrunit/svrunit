<?php

namespace SVRUnit\Components\Runner\Adapters\Local;


use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Services\OutputWriter\OutputWriterInterface;

class LocalTestRunner implements TestRunnerInterface
{

    /**
     * @var OutputWriterInterface
     */
    private $outputWriter;


    /**
     * @param OutputWriterInterface $outputWriter
     */
    public function __construct(OutputWriterInterface $outputWriter)
    {
        $this->outputWriter = $outputWriter;
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
        $output = shell_exec($command . " 2>&1");

        return $output;
    }

}
