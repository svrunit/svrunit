<?php

namespace SVRUnit\Components\Launcher\Models;

class LaunchOptions
{


    /**
     * @var string
     */
    private $configurationFile;

    /**
     * @var bool
     */
    private $showVersion;

    /**
     * @var bool
     */
    private $debugMode;


    /**
     * StartupSettings constructor.
     */
    public function __construct()
    {
        $this->configurationFile = '';
        $this->showVersion = false;
        $this->debugMode = false;
    }


    /**
     * @return string
     */
    public function getConfigurationFile()
    {
        return $this->configurationFile;
    }

    /**
     * @param string $configurationFile
     */
    public function setConfigurationFile($configurationFile)
    {
        $this->configurationFile = $configurationFile;
    }

    /**
     * @return bool
     */
    public function isShowVersion()
    {
        return $this->showVersion;
    }

    /**
     * @param bool $showVersion
     */
    public function setShowVersion($showVersion)
    {
        $this->showVersion = $showVersion;
    }

    /**
     * @return bool
     */
    public function isDebugMode(): bool
    {
        return $this->debugMode;
    }

    /**
     * @param bool $debugMode
     */
    public function setDebugMode(bool $debugMode): void
    {
        $this->debugMode = $debugMode;
    }

}
