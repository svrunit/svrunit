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
    private $group = '';

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
     * @var array<mixed>
     */
    private $dockerEnvVariables;

    /**
     * @var string
     */
    private $dockerEntrypoint = '';

    /**
     * @var array<mixed>
     */
    private $testFolders = [];

    /**
     * @var array<mixed>
     */
    private $testFiles = [];

    /**
     * @var int
     */
    private $setupTimeSeconds;


    /**
     * @param string $name
     * @param string $group
     */
    public function __construct(string $name, string $group)
    {
        $this->name = $name;
        $this->group = $group;

        $this->dockerEntrypoint = '';
        $this->dockerEnvVariables = [];

        $this->setupTimeSeconds = 0;

        $this->testFiles = [];
        $this->testFolders = [];
    }


    /**
     * @param string $folder
     */
    public function addTestFolder(string $folder): void
    {
        $this->testFolders[] = $folder;
    }

    /**
     * @param string $file
     * @return void
     */
    public function addTestFile(string $file): void
    {
        $this->testFiles[] = $file;
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
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return array<mixed>
     */
    public function getTestFolders()
    {
        return $this->testFolders;
    }

    /**
     * @return array<mixed>
     */
    public function getTestFiles(): array
    {
        return $this->testFiles;
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
     * @return void
     */
    public function setDockerImage(string $dockerImage)
    {
        $this->dockerImage = $dockerImage;
    }

    /**
     * @param bool $isEnabled
     * @return void
     */
    public function setDockerCommandRunner(bool $isEnabled)
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
     * @return void
     */
    public function setDockerEntrypoint(string $dockerEntrypoint)
    {
        $this->dockerEntrypoint = $dockerEntrypoint;
    }

    /**
     * @param string $dockerContainer
     * @return void
     */
    public function setDockerContainer(string $dockerContainer)
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
     * @return array<mixed>
     */
    public function getDockerEnvVariables(): array
    {
        return $this->dockerEnvVariables;
    }

    /**
     * @param array<mixed> $dockerEnvVariables
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
