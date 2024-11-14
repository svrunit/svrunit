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
     * @param OutputWriterInterface $outputWriter
     */
    public function __construct(OutputWriterInterface $outputWriter)
    {
        $this->outWriter = $outputWriter;
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
        $this->outWriter->debug($command);

        $output = (string)shell_exec($command . " 2>&1");

        return $output;
    }

}
