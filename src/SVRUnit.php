<?php

namespace SVRUnit;

use SVRUnit\Components\Runner\TestRunner;
use SVRUnit\Services\OutputWriter\ColoredOutputWriter;


class SVRUnit
{

    /**
     *
     */
    public const VERSION = "1.0";

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
    public function run(bool $debugMode): void
    {
        $success = $this->testRunner->run($debugMode);

        if ($success) {
            exit(0);
        } else {
            exit(1);
        }
    }

}
