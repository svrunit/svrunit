<?php

namespace SVRUnit\Components\Runner\Adapters\Docker;


use SVRUnit\Components\Runner\TestRunnerInterface;

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
     * @var bool
     */
    private $debugMode;


    /**
     * @param string $dockerImage
     * @param array $envVariables
     * @param string $entryPoint
     * @param bool $debugMode
     */
    public function __construct(string $dockerImage, array $envVariables, string $entryPoint, bool $debugMode)
    {
        $this->dockerImage = $dockerImage;
        $this->envVariables = $envVariables;
        $this->entryPoint = $entryPoint;
        $this->debugMode = $debugMode;

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

        if ($this->debugMode) {
            echo $cmd . PHP_EOL;
        }

        $output = shell_exec($cmd);
    }

    /**
     *
     */
    public function tearDown(): void
    {
        $cmd = "docker rm -f " . $this->name;
        $output = shell_exec($cmd);
    }

    /**
     * @param $command
     * @return string
     */
    public function runTest($command): string
    {
        $cmd = "docker exec " . $this->name . " bash -c '" . $command . " 2>&1 '";

        #  echo $cmd . PHP_EOL;

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