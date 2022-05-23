<?php

namespace SVRUnit\Components\Tests;


class TestSuite
{

    const TYPE_DOCKER_IMAGE = 1;
    const TYPE_DOCKER_CONTAINER = 2;
    const TYPE_LOCAL = 2;
    const TYPE_DOCKER_COMMAND_RUNNER = 3;


    /**
     * @var string
     */
    private $name = '';

    /**
     * @var string
     */
    private $dockerImage = '';

    /**
     * @var bool
     */
    private $dockerCommandRunner = false;

    /**
     * @var string
     */
    private $dockerContainer = '';

    /**
     * @var array
     */
    private $dockerEnvVariables;

    /**
     * @var string
     */
    private $dockerEntrypoint = '';

    /**
     * @var array
     */
    private $testFolders = [];

    /**
     * @var int
     */
    private $setupTimeSeconds;


    /**
     * TestSuite constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;

        $this->dockerEntrypoint = '';
        $this->dockerEnvVariables = [];

        $this->setupTimeSeconds = 0;
    }


    /**
     * @param string $folder
     */
    public function addTestFolder(string $folder): void
    {
        $this->testFolders[] = $folder;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        if ($this->dockerCommandRunner) {
            return self::TYPE_DOCKER_COMMAND_RUNNER;
        }

        if (trim($this->dockerImage) !== '') {
            return self::TYPE_DOCKER_IMAGE;
        }

        if (trim($this->dockerContainer) !== '') {
            return self::TYPE_DOCKER_CONTAINER;
        }

        return self::TYPE_LOCAL;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getTestFolders()
    {
        return $this->testFolders;
    }

    /**
     * @return string
     */
    public function getDockerImage()
    {
        return $this->dockerImage;
    }

    /**
     * @param string $dockerImage
     */
    public function setDockerImage($dockerImage)
    {
        $this->dockerImage = $dockerImage;
    }

    /**
     * @param bool $isEnabled
     * @return void
     */
    public function setDockerCommandRunner($isEnabled)
    {
        $this->dockerCommandRunner = $isEnabled;
    }

    /**
     * @return string
     */
    public function getDockerEntrypoint()
    {
        return $this->dockerEntrypoint;
    }

    /**
     * @param string $dockerEntrypoint
     */
    public function setDockerEntrypoint($dockerEntrypoint)
    {
        $this->dockerEntrypoint = $dockerEntrypoint;
    }

    /**
     * @param string $dockerContainer
     */
    public function setDockerContainer($dockerContainer)
    {
        $this->dockerContainer = $dockerContainer;
    }

    /**
     * @return string
     */
    public function getDockerContainer()
    {
        return $this->dockerContainer;
    }

    /**
     * @return array
     */
    public function getDockerEnvVariables(): array
    {
        return $this->dockerEnvVariables;
    }

    /**
     * @param array $dockerEnvVariables
     */
    public function setDockerEnvVariables(array $dockerEnvVariables): void
    {
        $this->dockerEnvVariables = $dockerEnvVariables;
    }

    /**
     * @return int
     */
    public function getSetupTimeSeconds(): int
    {
        return $this->setupTimeSeconds;
    }

    /**
     * @param int $setupTimeSeconds
     */
    public function setSetupTimeSeconds(int $setupTimeSeconds): void
    {
        $this->setupTimeSeconds = $setupTimeSeconds;
    }

}
