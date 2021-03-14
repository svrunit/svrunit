<?php

namespace SVRUnit\Tests\Fakes;

use SVRUnit\Services\ShellRunner\ShellRunnerInterface;


class FakeShellRunner implements ShellRunnerInterface
{

    /**
     * @var string
     */
    private $command;


    /**
     * FakeShellRunner constructor.
     */
    public function __construct()
    {
        $this->command = '';
    }

    /**
     * @return string
     */
    public function getUsedCommand(): string
    {
        return $this->command;
    }

    /**
     * @param string $command
     * @return string
     */
    public function execute(string $command): string
    {
        $this->command = $command;

        return "";
    }

}
