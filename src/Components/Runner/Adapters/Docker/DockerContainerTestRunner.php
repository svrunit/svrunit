<?php

namespace SVRUnit\Components\Runner\Adapters\Docker;

use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Services\OutputWriter\OutputWriterInterface;


class DockerContainerTestRunner implements TestRunnerInterface
{

    /**
     * @var string
     */
    private $containerName;

    /**
     * @var OutputWriterInterface
     */
    private $outWriter;


    /**
     * @param string $containerName
     * @param OutputWriterInterface $outputWriter
     */
    public function __construct(string $containerName, OutputWriterInterface $outputWriter)
    {
        $this->containerName = $containerName;
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
     * @param $command
     * @return string
     */
    public function runTest($command): string
    {
        $cmd = "docker exec " . $this->containerName . " bash -c '" . $command . " 2>&1 '";
        $output = shell_exec($cmd);

        return $output;
    }

}