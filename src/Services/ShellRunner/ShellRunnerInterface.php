<?php

namespace SVRUnit\Services\ShellRunner;

interface ShellRunnerInterface
{

    /**
     * @param string $command
     * @return string
     */
    public function execute(string $command): string;

}
