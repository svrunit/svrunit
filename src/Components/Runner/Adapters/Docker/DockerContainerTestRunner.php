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
     * @param OutputWriterInterface $outWriter
     */
    public function __construct(string $containerName, OutputWriterInterface $outWriter)
    {
        $this->containerName = $containerName;
        $this->outWriter = $outWriter;
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
        $cmd = "docker exec " . $this->containerName . " bash -c '" . $command . " 2>&1 '";

        $this->outWriter->debug($cmd);

        $output = (string)shell_exec($cmd);

        return $output;
    }

}