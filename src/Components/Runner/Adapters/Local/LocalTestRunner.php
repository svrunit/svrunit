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
