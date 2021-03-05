<?php

namespace SVRUnit\Components\Launcher;

use SVRUnit\Components\Launcher\Models\LaunchOptions;

class Launcher
{

    /**
     * @var array
     */
    private $arguments;

    /**
     * @var LaunchOptions
     */
    private $options;


    /**
     * LaunchCommands constructor.
     * @param array $args
     */
    public function __construct(array $args)
    {
        $this->arguments = $args;
    }


    /**
     *
     */
    public function load()
    {
        $this->options = new LaunchOptions();

        /** @var string $arg */
        while ($arg = array_shift($this->arguments)) {

            if ($this->stringStartsWith('--version', $arg)) {
                $this->options->setShowVersion(true);
            }

            if ($this->stringStartsWith('--configuration=', $arg)) {
                $configFile = str_replace('--configuration=', '', $arg);
                $this->options->setConfigurationFile($configFile);
            }

            if ($this->stringStartsWith('--debug', $arg)) {
                $this->options->setDebugMode(true);
            }

        }
    }

    /**
     * @return LaunchOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param $search
     * @param $text
     * @return bool
     */
    private function stringStartsWith($search, $text)
    {
        if (strpos($text, $search) === 0) {
            return true;
        }

        return false;
    }

}
