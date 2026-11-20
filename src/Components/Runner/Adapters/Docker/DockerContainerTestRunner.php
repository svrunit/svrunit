<?php

namespace SVRUnit\Components\Runner\Adapters\Docker;

use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Services\OutputWriter\OutputWriterInterface;
use SVRUnit\Services\ShellRunner\ShellRunnerInterface;


class DockerContainerTestRunner implements TestRunnerInterface
{

    /**
     * @var string
     */
    private $containerName;

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
     * @param string $containerName
     * @param ShellRunnerInterface $shellRunner
     * @param OutputWriterInterface $outWriter
     */
    public function __construct(string $containerName, ShellRunnerInterface $shellRunner, OutputWriterInterface $outWriter, bool $debugMode)
    {
        $this->containerName = $containerName;
        $this->shellRunner = $shellRunner;
        $this->outWriter = $outWriter;
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
        $cmd = "docker exec " . $this->containerName . " bash -c " . escapeshellarg($command . " 2>&1");

        if ($this->debugMode) {
            $this->outWriter->debug($cmd);
        }

        return $this->shellRunner->execute($cmd);
    }

}