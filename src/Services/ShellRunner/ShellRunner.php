<?php

namespace SVRUnit\Services\ShellRunner;

class ShellRunner implements ShellRunnerInterface
{

    /**
     * @param string $command
     * @return string
     */
    public function execute(string $command): string
    {
        return (string)shell_exec($command);
    }

}
