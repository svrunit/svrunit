<?php

namespace SVRUnit\Commands;

use SVRUnit\SVRUnit;

trait CommandTrait
{

    /**
     * @return void
     */
    protected function showHeader()
    {
        echo "SVRUnit Testing Framework, v" . SVRUnit::VERSION . PHP_EOL;
        echo "Copyright (c) 2022 Christian Dangl" . PHP_EOL;
        echo "www.svrunit.com" . PHP_EOL;
    }

}