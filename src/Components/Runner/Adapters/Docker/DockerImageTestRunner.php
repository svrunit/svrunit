<?php

namespace SVRUnit\Components\Runner\Adapters\Docker;


use SVRUnit\Components\Runner\TestRunnerInterface;
use SVRUnit\Services\OutputWriter\OutputWriterInterface;

class DockerImageTestRunner implements TestRunnerInterface
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
     * @var array
     */
    private $envVariables;

    /**
     * @var string
     */
    private $name;

    /**
     * @var OutputWriterInterface
     */
    private $outWriter;


    /**
     * @param string $dockerImage
     * @param array $envVariables
     * @param string $entryPoint
     * @param OutputWriterInterface $outputWriter
     */
    public function __construct(string $dockerImage, array $envVariables, string $entryPoint, OutputWriterInterface $outputWriter)
    {
        $this->dockerImage = $dockerImage;
        $this->envVariables = $envVariables;
        $this->entryPoint = $entryPoint;
        $this->outWriter = $outputWriter;

        $this->name = "svrunit_" . $this->getRandomName(4);
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

        $cmd = "docker run --rm " . $entrypoint . " " . $envCommands . " --name " . $this->name . " -d " . $this->dockerImage;

        $cmd = str_replace('  ', ' ', $cmd);

        $this->outWriter->debug("Setup: " . $cmd);

        $output = shell_exec($cmd);
    }

    /**
     *
     */
    public function tearDown(): void
    {
        $cmd = "docker rm -f " . $this->name;

        $this->outWriter->debug("Teardown: " . $cmd);

        $output = shell_exec($cmd);
    }

    /**
     * @param $command
     * @return string
     */
    public function runTest($command): string
    {
        $cmd = "docker exec " . $this->name . " bash -c '" . $command . " 2>&1 '";

        $output = shell_exec($cmd);

        return (string)$output;
    }


    /**
     * @param $length
     * @return string
     */
    private function getRandomName($length): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

}