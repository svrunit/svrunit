<?php

namespace SVRUnit\Components\Runner\Adapters\Local;


use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Services\OutputWriter\OutputWriterInterface;

class LocalTestRunner implements TestRunnerInterface
{

    /**
     * @var OutputWriterInterface
     */
    private $outWriter;

    /**
     * @var bool
     */
    private $debugMode;


    /**
     * @param OutputWriterInterface $outputWriter
     */
    public function __construct(OutputWriterInterface $outputWriter, bool $debugMode)
    {
        $this->outWriter = $outputWriter;
        $this->debugMode = $debugMode;
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
        if ($this->debugMode) {
            $this->outWriter->debug($command);
        }

        return (string)shell_exec($command . " 2>&1");
    }

}
