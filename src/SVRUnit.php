<?php

namespace SVRUnit;

use SVRUnit\Components\Runner\TestRunner;
use SVRUnit\Services\OutputWriter\ColoredOutputWriter;


class SVRUnit
{

    /**
     *
     */
    public const VERSION = "0.9";

    /**
     * @var TestRunner
     */
    private $testRunner;


    /**
     * SVRUnit constructor.
     * @param $configFile
     */
    public function __construct($configFile)
    {
        $outputWriter = new ColoredOutputWriter();

        $this->testRunner = new TestRunner($configFile, $outputWriter);
    }

    /**
     * @param bool $debugMode
     * @throws \Exception
     */
    public function run(bool $debugMode) : void
    {
        $this->testRunner->run($debugMode);
    }

}
