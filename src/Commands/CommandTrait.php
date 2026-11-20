<?php

namespace SVRUnit\Commands;

use SVRUnit\Services\OutputWriter\ColoredOutputWriter;
use SVRUnit\SVRUnit;

trait CommandTrait
{

    /**
     * @return void
     */
    protected function showHeader()
    {
        $writer = new ColoredOutputWriter();

        $writer->info('');
        $writer->section('  ___ __   __ ___  _   _        _  _   ');
        $writer->section(' / __|\ \ / /| _ \| | | | _ _  (_)| |_ ');
        $writer->section(' \__ \ \ V / |   /| |_| || \' \ | ||  _|');
        $writer->section(' |___/  \_/  |_|_\ \___/ |_||_||_| \__|');
        $writer->info('');
        $writer->info(' Server Testing Framework v' . SVRUnit::VERSION);
        $writer->debug(' Copyright (c) 2026 Christian Dangl · www.svrunit.com');
    }

    /**
     * @param string $key
     * @param string $value
     * @return void
     */
    protected function showSetting(string $key, string $value)
    {
        echo sprintf(' %-15s %s', $key, $value) . PHP_EOL;
    }

}
