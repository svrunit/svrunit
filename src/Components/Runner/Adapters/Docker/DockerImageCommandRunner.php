<?php

namespace SVRUnit\Components\Runner\Adapters\Docker;

use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Services\OutputWriter\OutputWriterInterface;
use SVRUnit\Services\ShellRunner\ShellRunnerInterface;

class DockerImageCommandRunner implements TestRunnerInterface
{

    /**
     * @var string
     */
    private $dockerImage;

    /**
     * @var array<mixed>
     */
    private $envVariables;

    /**
     * @var ShellRunnerInterface
     */
    private $shellRunner;

    /**
     * @var OutputWriterInterface
     */
    private $outWriter;


    /**
     * @param string $dockerImage
     * @param array<mixed> $envVariables
     * @param ShellRunnerInterface $shellRunner
     * @param OutputWriterInterface $outWriter
     */
    public function __construct(string $dockerImage, array $envVariables, ShellRunnerInterface $shellRunner, OutputWriterInterface $outWriter)
    {
        $this->dockerImage = $dockerImage;
        $this->envVariables = $envVariables;
        $this->shellRunner = $shellRunner;
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
        $envCommands = "";

        foreach ($this->envVariables as $env) {
            $envCommands .= ' --env ' . $env . ' ';
        }

        # first pull in quiet mode
        # then we don't have such a long cli output
        $cmd = "docker pull -q " . $this->dockerImage;
        $this->shellRunner->execute($cmd);

        $cmd = "docker run --rm " . $envCommands . " " . $this->dockerImage . " bash -c '" . $command . "'";

        $this->outWriter->debug($cmd);

        $output = $this->shellRunner->execute($cmd);

        return (string)$output;
    }

}
