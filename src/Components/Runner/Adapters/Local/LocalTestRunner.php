<?php

namespace SVRUnit\Components\Runner\Adapters\Local;


use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Services\OutputWriter\OutputWriterInterface;
use SVRUnit\Services\ShellRunner\ShellRunnerInterface;

class LocalTestRunner implements TestRunnerInterface
{

    /**
     * @var ShellRunnerInterface
     */
    private $shellRunner;

    /**
     * @var OutputWriterInterface
     */
    private $outWriter;

    /**
     * @var bool
     */
    private $debugMode;


    /**
     * @param ShellRunnerInterface $shellRunner
     * @param OutputWriterInterface $outputWriter
     */
    public function __construct(ShellRunnerInterface $shellRunner, OutputWriterInterface $outputWriter, bool $debugMode)
    {
        $this->shellRunner = $shellRunner;
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
    public function runTest(string $command): string
    {
        if ($this->debugMode) {
            $this->outWriter->debug($command);
        }

        return $this->shellRunner->execute($command . " 2>&1");
    }

}
