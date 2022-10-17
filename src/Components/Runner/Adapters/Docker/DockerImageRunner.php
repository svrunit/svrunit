<?php

namespace SVRUnit\Components\Runner\Adapters\Docker;


use _HumbugBox373c0874430e\Roave\Signature\Encoder\Sha1SumEncoder;
use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Services\OutputWriter\OutputWriterInterface;
use SVRUnit\Services\ShellRunner\ShellRunnerInterface;

class DockerImageRunner implements TestRunnerInterface
{

    /**
     * @var string
     */
    private $dockerImage;

    /**
     * @var string
     */
    private $entryPoint;

    /**
     * @var array<mixed>
     */
    private $envVariables;

    /**
     * @var string
     */
    private $name;

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
     * @param string $entryPoint
     * @param string $containerName
     * @param ShellRunnerInterface $shellRunner
     * @param OutputWriterInterface $outputWriter
     */
    public function __construct(string $dockerImage, array $envVariables, string $entryPoint, string $containerName, ShellRunnerInterface $shellRunner, OutputWriterInterface $outputWriter)
    {
        $this->dockerImage = $dockerImage;
        $this->envVariables = $envVariables;
        $this->entryPoint = $entryPoint;
        $this->shellRunner = $shellRunner;
        $this->outWriter = $outputWriter;
        $this->name = $containerName;
    }


    /**
     *
     */
    public function setUp(): void
    {
        $entrypoint = '';

        if (!empty($this->entryPoint)) {
            $entrypoint = ' --entrypoint ' . $this->entryPoint;
        }

        $envCommands = "";

        foreach ($this->envVariables as $env) {
            $envCommands .= ' --env ' . $env . ' ';
        }

        # first pull in quiet mode
        # then we don't have such a long cli output
        $cmd = "docker pull -q " . $this->dockerImage;
        $this->shellRunner->execute($cmd);

        $cmd = "docker run --rm " . $entrypoint . " " . $envCommands . " --name " . $this->name . " -d " . $this->dockerImage;

        $cmd = str_replace('  ', ' ', $cmd);
        $cmd = str_replace('  ', ' ', $cmd);

        $this->outWriter->debug("Setup: " . $cmd);

        $output = $this->shellRunner->execute($cmd);
    }

    /**
     *
     */
    public function tearDown(): void
    {
        $cmd = "docker rm -f " . $this->name;

        $this->outWriter->debug("Teardown: " . $cmd);

        $output = $this->shellRunner->execute($cmd);
    }

    /**
     * @param string $command
     * @return string
     */
    public function runTest(string $command): string
    {
        $cmd = "docker exec " . $this->name . " bash -c '" . $command . " 2>&1 '";

        $output = $this->shellRunner->execute($cmd);

        return (string)$output;
    }

}