<?php

namespace SVRUnit\Components\Runner\Adapters\Docker;

use SVRUnit\Components\Runner\TestRunnerInterface;


class DockerContainerTestRunner implements TestRunnerInterface
{

    /**
     * @var string
     */
    private $containerName;


    /**
     * DockerContainerTestRunner constructor.
     * @param string $containerName
     */
    public function __construct(string $containerName)
    {
        $this->containerName = $containerName;
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